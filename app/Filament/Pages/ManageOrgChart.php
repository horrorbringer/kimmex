<?php

namespace App\Filament\Pages;

use App\Filament\Exports\OrgUnitExporter;
use App\Filament\Imports\OrgUnitImporter;
use App\Filament\Resources\OrgUnits\OrgUnitResource;
use App\Filament\Resources\OrgUnits\Schemas\OrgUnitForm;
use App\Models\OrgUnit;
use App\Models\SystemSetting;
use App\Services\AutoTranslateService;
use App\Services\OrgStructureTemplateService;
use App\Support\PublicStorage;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ManageOrgChart extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected string $view = 'filament.pages.manage-org-chart';

    public static function getNavigationGroup(): ?string
    {
        return __('HR Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage Org Chart');
    }

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public array $chartData = [];

    public ?array $data = [];

    public string $activeChartGroup = 'main';

    public function mount(): void
    {
        $org = SystemSetting::get('organization_profile', []);
        $this->form->fill([
            'org_chart_visible' => (bool) ($org['org_chart_visible'] ?? true),
            'org_chart_type' => $org['org_chart_type'] ?? 'dynamic',
            'org_chart_card_style' => $org['org_chart_card_style'] ?? 'avatar_top',
            'org_chart_image' => $org['org_chart_image'] ?? null,
            'org_chart_pdf' => $org['org_chart_pdf'] ?? null,
        ]);
        $this->loadChartData();
    }

    public function form($form)
    {
        return $form
            ->schema([
                Section::make(__('Display & Visibility Settings'))
                    ->description(__('Manage overall org chart visibility and display format on the public website.'))
                    ->schema([
                        Toggle::make('org_chart_visible')
                            ->label(__('Show Org Chart on Website'))
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Enable or disable displaying the organization chart section on the About page.'))
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),
                        Select::make('org_chart_type')
                            ->label(__('Chart Type'))
                            ->options([
                                'dynamic' => __('Interactive Chart (Builder Below)'),
                                'image' => __('Upload Image (PNG/JPG)'),
                                'pdf' => __('Upload PDF'),
                                'none' => __('Hidden (Do Not Show on Website)'),
                            ])
                            ->default('dynamic')
                            ->required()
                            ->live(),
                        Select::make('org_chart_card_style')
                            ->label(__('Card Design Template'))
                            ->options([
                                'avatar_top' => __('Circle Photo Above Box (Clean Canva)'),
                                'floating' => __('Floating Circle Avatar (Classic Overlap)'),
                                'badge' => __('Executive Portrait Badge (Integrated Photo)'),
                                'capsule' => __('Horizontal Capsule (Avatar on Left)'),
                                'corporate' => __('Corporate Tagged Minimalist (Role Badges)'),
                                'nameplate' => __('Framed Portrait with Nameplate (Template 6)'),
                            ])
                            ->default('avatar_top')
                            ->native(false)
                            ->visible(fn ($get) => (bool) $get('org_chart_visible') && $get('org_chart_type') === 'dynamic'),
                        FileUpload::make('org_chart_image')
                            ->label(__('Organization Chart Image'))
                            ->image()
                            ->disk(config('filesystems.public_uploads_disk'))
                            ->directory('organization')
                            ->visibility('public')
                            ->maxSize(102400) // 100MB
                            ->visible(fn ($get) => (bool) $get('org_chart_visible') && $get('org_chart_type') === 'image'),
                        FileUpload::make('org_chart_pdf')
                            ->label(__('Organization Chart PDF'))
                            ->disk(config('filesystems.public_uploads_disk'))
                            ->directory('organization')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(1048576) // 1GB limit in app
                            ->visible(fn ($get) => (bool) $get('org_chart_visible') && $get('org_chart_type') === 'pdf'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function saveDisplaySettings(): void
    {
        $org = SystemSetting::get('organization_profile', []);
        $org = array_merge($org, $this->form->getState());
        SystemSetting::set('organization_profile', $org);

        OrgUnit::clearOrgCache();

        Notification::make()
            ->title(__('Display Settings Saved'))
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('addRoot')
                ->label(__('Add Root Unit'))
                ->icon('heroicon-o-plus')
                ->size('xs')
                ->modalHeading(__('Add Root Position'))
                ->modalWidth('2xl')
                ->modalSubmitActionLabel(__('Create Position'))
                ->model(OrgUnit::class)
                ->form(OrgUnitForm::getSchema(context: 'root'))
                ->mutateFormDataUsing(function (array $data): array {
                    $data['chart_group'] = $this->activeChartGroup;

                    return $data;
                }),

            Action::make('loadTemplate')
                ->label(__('Load Template'))
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->size('xs')
                ->visible(fn (): bool => (app()->isLocal() || app()->runningUnitTests()) && $this->activeChartGroup === 'main')
                ->modalHeading(__('Load Template'))
                ->modalDescription(__('Choose a corporate template to populate the chart.'))
                ->modalWidth('md')
                ->modalSubmitActionLabel(__('Apply Template'))
                ->form([
                    Select::make('template')
                        ->label(__('Template'))
                        ->options([
                            'kimmex_corporate' => __('KIMMEX Corporate Structure (Full)'),
                            'standard_company' => __('Standard Business Structure'),
                            'starter_root' => __('Starter Hierarchy'),
                        ])
                        ->default('kimmex_corporate')
                        ->required()
                        ->native(false),
                    Toggle::make('clear_existing')
                        ->label(__('Replace existing positions'))
                        ->default(true),
                ])
                ->action(function (array $data): void {
                    if (! (app()->isLocal() || app()->runningUnitTests())) {
                        Notification::make()
                            ->title(__('Template loading is only available in local development.'))
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($this->activeChartGroup !== 'main') {
                        Notification::make()
                            ->title(__('Templates are only available for the Main Organization Structure.'))
                            ->warning()
                            ->send();

                        return;
                    }

                    $count = OrgStructureTemplateService::applyTemplate(
                        (string) ($data['template'] ?? 'kimmex_corporate'),
                        (bool) ($data['clear_existing'] ?? true),
                    );

                    $this->loadChartData();

                    Notification::make()
                        ->title(__('Corporate template applied successfully! :count positions loaded.', ['count' => $count]))
                        ->success()
                        ->send();
                }),

            Action::make('manageGroups')
                ->label(__('Manage Sections'))
                ->icon('heroicon-o-rectangle-group')
                ->color('gray')
                ->size('xs')
                ->modalHeading(__('Manage Sections'))
                ->modalWidth('3xl')
                ->modalSubmitActionLabel(__('Save Sections'))
                ->fillForm(fn () => [
                    'groups' => OrgUnit::getChartGroups(),
                ])
                ->form([

                    Repeater::make('groups')
                        ->label(__('Sections'))
                        ->itemLabel(function (array $state): ?string {
                            $name = $state['name_en'] ?? null;
                            $key = $state['key'] ?? '';
                            if (empty($name) && empty($key)) {
                                return __('New Section');
                            }
                            $isMain = $key === 'main';
                            $isActive = (bool) ($state['is_active'] ?? true);

                            return ($name ?: ucfirst(str_replace('_', ' ', $key))).($isMain ? ' • 🔒' : '').($isActive ? '' : ' • ⚪ '.__('Hidden'));
                        })
                        ->collapsible()
                        ->collapsed()
                        ->collapseAllAction(fn (Action $action) => $action->label(__('Collapse All')))
                        ->expandAllAction(fn (Action $action) => $action->label(__('Expand All')))
                        ->schema([
                            Grid::make(['default' => 1, 'md' => 3])->schema([
                                TextInput::make('name_en')
                                    ->label(__('Title (EN)'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                                        if ($get('key') !== 'main' && empty($get('key')) && filled($state)) {
                                            $set('key', Str::slug($state, '_'));
                                        }
                                    }),

                                TextInput::make('name_km')
                                    ->label(__('Title (KM)'))
                                    ->suffixAction(
                                        Action::make('translateKhmer')
                                            ->icon('heroicon-m-language')
                                            ->tooltip(__('Auto-translate'))
                                            ->action(function (Get $get, Set $set) {
                                                $english = trim((string) $get('name_en'));
                                                if (empty($english)) {
                                                    Notification::make()
                                                        ->warning()
                                                        ->title(__('Please enter an English title first'))
                                                        ->send();

                                                    return;
                                                }
                                                $translator = app(AutoTranslateService::class);
                                                $translated = $translator->translateFrom($english, 'km', 'en') ?: __($english, [], 'km');
                                                if ($translated && $translated !== $english) {
                                                    $set('name_km', $translated);
                                                    Notification::make()
                                                        ->success()
                                                        ->title(__('Translated'))
                                                        ->send();
                                                }
                                            })
                                    ),

                                TextInput::make('key')
                                    ->label(__('Code'))
                                    ->required()
                                    ->regex('/^[a-z0-9_]+$/')
                                    ->disabled(fn (?string $state) => $state === 'main')
                                    ->dehydrated()
                                    ->extraInputAttributes(['style' => 'font-family: monospace; font-size: 0.8125rem;']),
                            ]),

                            Grid::make(['default' => 1, 'md' => 3])->schema([
                                Select::make('card_style')
                                    ->label(__('Template'))
                                    ->options([
                                        'default' => __('Global Default'),
                                        'avatar_top' => __('Circle Photo (Template 1)'),
                                        'floating' => __('Floating Avatar (Template 2)'),
                                        'badge' => __('Executive Badge (Template 3)'),
                                        'capsule' => __('Horizontal Capsule (Template 4)'),
                                        'corporate' => __('Corporate Tagged (Template 5)'),
                                        'nameplate' => __('Framed Nameplate (Template 6)'),
                                    ])
                                    ->default('default')
                                    ->native(false)
                                    ->columnSpan(['default' => 1, 'md' => 2]),

                                Toggle::make('is_active')
                                    ->label(__('Public'))
                                    ->default(true)
                                    ->inline(false)
                                    ->columnSpan(['default' => 1, 'md' => 1]),
                            ]),
                        ])
                        ->deleteAction(
                            fn (Action $action) => $action->hidden(fn (array $arguments, Repeater $component): bool => ($component->getRawItemState($arguments['item'])['key'] ?? null) === 'main'
                            )
                        )
                        ->reorderable()
                        ->addActionLabel(__('Add New Section / Group'))
                        ->minItems(1),
                ])
                ->action(function (array $data): void {
                    $groups = $data['groups'] ?? [];
                    $cleanGroups = [];
                    $seenKeys = [];
                    foreach ($groups as $group) {
                        $rawKey = $group['key'] ?? '';
                        $key = strtolower(trim((string) preg_replace('/[^a-zA-Z0-9_]+/', '_', $rawKey)));
                        if (empty($key) || isset($seenKeys[$key])) {
                            continue;
                        }
                        $seenKeys[$key] = true;
                        $cleanGroups[] = [
                            'key' => $key,
                            'name_en' => trim($group['name_en'] ?? ucfirst(str_replace('_', ' ', $key))),
                            'name_km' => trim($group['name_km'] ?? ''),
                            'card_style' => $group['card_style'] ?? 'default',
                            'is_active' => (bool) ($group['is_active'] ?? true),
                        ];
                    }

                    // Guarantee 'main' is never lost
                    $hasMain = false;
                    foreach ($cleanGroups as $g) {
                        if ($g['key'] === 'main') {
                            $hasMain = true;
                            break;
                        }
                    }
                    if (! $hasMain) {
                        array_unshift($cleanGroups, [
                            'key' => 'main',
                            'name_en' => 'Organization Structure',
                            'name_km' => 'រចនាសម្ព័ន្ធអង្គភាព',
                            'card_style' => 'default',
                            'is_active' => true,
                        ]);
                    }

                    SystemSetting::set('org_chart_groups', $cleanGroups);
                    OrgUnit::clearOrgCache();

                    if (! in_array($this->activeChartGroup, array_column($cleanGroups, 'key'))) {
                        $this->activeChartGroup = $cleanGroups[0]['key'] ?? 'main';
                    }

                    $this->loadChartData();

                    Notification::make()
                        ->title(__('Chart groups updated successfully!'))
                        ->success()
                        ->send();
                }),

            Action::make('displaySettings')
                ->label(__('Website Display'))
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->size('xs')
                ->modalHeading(__('Website Display Settings'))
                ->modalDescription(__('Configure how the organization chart is presented on the public About page.'))
                ->modalWidth('md')
                ->modalSubmitActionLabel(__('Save Settings'))
                ->fillForm(fn (): array => [
                    'org_chart_visible' => (bool) (SystemSetting::get('organization_profile', [])['org_chart_visible'] ?? true),
                    'org_chart_type' => SystemSetting::get('organization_profile', [])['org_chart_type'] ?? 'dynamic',
                    'org_chart_card_style' => SystemSetting::get('organization_profile', [])['org_chart_card_style'] ?? 'avatar_top',
                    'org_chart_image' => SystemSetting::get('organization_profile', [])['org_chart_image'] ?? null,
                    'org_chart_pdf' => SystemSetting::get('organization_profile', [])['org_chart_pdf'] ?? null,
                ])
                ->form([
                    Toggle::make('org_chart_visible')
                        ->label(__('Show Org Chart on Website'))
                        ->default(fn () => (bool) (SystemSetting::get('organization_profile', [])['org_chart_visible'] ?? true))
                        ->live(),
                    Select::make('org_chart_type')
                        ->label(__('Chart Type'))
                        ->options([
                            'dynamic' => __('Interactive Flowchart (Builder)'),
                            'image' => __('Static Image (PNG/JPG)'),
                            'pdf' => __('Downloadable PDF Document'),
                            'none' => __('Hidden (Do Not Show)'),
                        ])
                        ->default(fn () => SystemSetting::get('organization_profile', [])['org_chart_type'] ?? 'dynamic')
                        ->required()
                        ->live(),
                    Select::make('org_chart_card_style')
                        ->label(__('Card Design Template'))
                        ->options([
                            'avatar_top' => __('Circle Photo Above Box (Clean Canva)'),
                            'floating' => __('Floating Circle Avatar (Classic Overlap)'),
                            'badge' => __('Executive Portrait Badge (Integrated Photo)'),
                            'capsule' => __('Horizontal Capsule (Avatar on Left)'),
                            'corporate' => __('Corporate Tagged Minimalist (Role Badges)'),
                            'nameplate' => __('Framed Portrait with Nameplate (Template 6)'),
                        ])
                        ->default(fn () => SystemSetting::get('organization_profile', [])['org_chart_card_style'] ?? 'avatar_top')
                        ->native(false)
                        ->visible(fn ($get) => (bool) $get('org_chart_visible') && $get('org_chart_type') === 'dynamic'),
                    FileUpload::make('org_chart_image')
                        ->label(__('Organization Chart Image'))
                        ->image()
                        ->disk(config('filesystems.public_uploads_disk'))
                        ->directory('organization')
                        ->visibility('public')
                        ->maxSize(102400)
                        ->default(fn () => SystemSetting::get('organization_profile', [])['org_chart_image'] ?? null)
                        ->visible(fn ($get) => (bool) $get('org_chart_visible') && $get('org_chart_type') === 'image'),
                    FileUpload::make('org_chart_pdf')
                        ->label(__('Organization Chart PDF'))
                        ->disk(config('filesystems.public_uploads_disk'))
                        ->directory('organization')
                        ->visibility('public')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(1048576)
                        ->default(fn () => SystemSetting::get('organization_profile', [])['org_chart_pdf'] ?? null)
                        ->visible(fn ($get) => (bool) $get('org_chart_visible') && $get('org_chart_type') === 'pdf'),
                ])
                ->action(function (array $data): void {
                    $org = SystemSetting::get('organization_profile', []);
                    $org = array_merge($org, $data);
                    SystemSetting::set('organization_profile', $org);

                    OrgUnit::clearOrgCache();

                    Notification::make()
                        ->title(__('Display settings updated successfully'))
                        ->success()
                        ->send();
                }),

            ActionGroup::make([
                ImportAction::make('importOrgUnits')
                    ->label(__('Import CSV'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->importer(OrgUnitImporter::class)
                    ->fileRules(['max:5120'])
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false)
                    ->after(fn () => $this->loadChartData()),

                ExportAction::make('exportOrgUnits')
                    ->label(__('Export CSV'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(OrgUnitExporter::class)
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),

                Action::make('downloadCsvTemplate')
                    ->label(__('CSV Template'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(asset('org-chart-importer-example.csv'))
                    ->openUrlInNewTab()
                    ->tooltip(__('Download sample CSV template with hierarchy structure')),

                Action::make('unitsTable')
                    ->label(__('Units Table List'))
                    ->icon('heroicon-o-table-cells')
                    ->url(OrgUnitResource::getUrl('index')),
            ])
                ->label(__('More Tools'))
                ->icon('heroicon-o-ellipsis-vertical')
                ->color('gray')
                ->button()
                ->size('xs'),
        ];
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->modalHeading(__('Edit Position'))
            ->modalWidth('2xl')
            ->modalSubmitActionLabel(__('Save Changes'))
            ->model(OrgUnit::class)
            ->record(fn (array $arguments): ?OrgUnit => OrgUnit::find($arguments['id'] ?? null))
            ->form(fn (array $arguments) => OrgUnitForm::getSchema(context: 'edit', parentId: OrgUnit::find($arguments['id'] ?? null)?->parentId))
            ->fillForm(function (array $arguments): array {
                $unit = OrgUnit::find($arguments['id'] ?? null);
                if (! $unit) {
                    return [];
                }

                $locale = app()->getLocale();
                $title = $unit->getTranslation('title', $locale, false);
                if (! filled($title)) {
                    $title = $unit->getTranslation('title', 'en', false) ?: $unit->getTranslation('title', 'km', false);
                }
                if (is_array($title)) {
                    $title = $title[$locale] ?? ($title['en'] ?? reset($title));
                }

                return [
                    'employeeId' => $unit->employeeId,
                    'title' => (string) ($title ?: $unit->title),
                    'type' => $unit->type,
                    'parentId' => $unit->parentId,
                    'departmentId' => $unit->departmentId,
                    'chart_group' => $unit->chart_group ?? $this->activeChartGroup,
                    'card_style' => $unit->card_style,
                    'orderIndex' => $unit->orderIndex,
                    'isActive' => (bool) $unit->isActive,
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $unit = OrgUnit::find($arguments['id'] ?? null);
                if (! $unit) {
                    return;
                }

                $title = $data['title'] ?? null;
                unset($data['title']);

                $unit->fill($data);

                if (filled($title)) {
                    $locale = app()->getLocale();
                    $unit->setTranslation('title', $locale, $title);
                }

                $unit->save();
                $this->loadChartData();
                Notification::make()
                    ->title(__('Position updated successfully'))
                    ->success()
                    ->send();
            });
    }

    public function toggleActiveAction(): Action
    {
        return Action::make('toggleActive')
            ->model(OrgUnit::class)
            ->action(function (array $arguments): void {
                $unit = OrgUnit::find($arguments['id'] ?? null);
                if ($unit) {
                    $unit->update(['isActive' => ! $unit->isActive]);
                    Notification::make()
                        ->title($unit->isActive ? __('Position is now visible') : __('Position is now hidden'))
                        ->success()
                        ->send();
                }
                $this->loadChartData();
            });
    }

    public function addChildAction(): Action
    {
        return Action::make('addChild')
            ->modalHeading(__('Add Subordinate'))
            ->modalWidth('2xl')
            ->modalSubmitActionLabel(__('Add Subordinate'))
            ->model(OrgUnit::class)
            ->form(fn (array $arguments) => OrgUnitForm::getSchema(context: 'child', parentId: $arguments['id'] ?? null))
            ->fillForm(fn (array $arguments): array => [
                'parentId' => $arguments['id'] ?? null,
                'type' => 'STAFF',
                'chart_group' => $this->activeChartGroup,
                'card_style' => null,
                'isActive' => true,
                'orderIndex' => 0,
            ])
            ->action(function (array $data, array $arguments): void {
                $data['parentId'] = $arguments['id'] ?? $data['parentId'] ?? null;
                $unit = new OrgUnit;
                $title = $data['title'] ?? null;
                unset($data['title']);

                $unit->fill($data);
                if (filled($title)) {
                    $unit->setTranslation('title', app()->getLocale(), $title);
                }
                $unit->save();

                $this->loadChartData();
                Notification::make()
                    ->title(__('Subordinate position created successfully'))
                    ->success()
                    ->send();
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->modalHeading(__('Delete Position'))
            ->modalDescription(__('Are you sure you want to delete this position from the organization structure?'))
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->action(function (array $arguments): void {
                $unit = OrgUnit::find($arguments['id']);
                if ($unit) {
                    $unit->delete();
                    Notification::make()
                        ->title(__('Position deleted successfully'))
                        ->success()
                        ->send();
                }
                $this->loadChartData();
            });
    }

    public function loadChartData(): void
    {
        $unitsByParent = OrgUnit::with(['employee', 'department'])
            ->forChart($this->activeChartGroup)
            ->orderBy('orderIndex')
            ->get()
            ->groupBy(fn (OrgUnit $unit): string => (string) ($unit->parentId ?? '__root__'));

        $this->chartData = $this->buildTree($unitsByParent);
        $this->dispatch('chartUpdated', chartData: $this->chartData);
    }

    public function switchChartGroup(string $group): void
    {
        $this->activeChartGroup = $group;
        $this->loadChartData();
    }

    public function updateActiveGroupCardStyle(string $style): void
    {
        $groups = OrgUnit::getChartGroups();
        $found = false;
        foreach ($groups as &$group) {
            if (($group['key'] ?? 'main') === $this->activeChartGroup) {
                $group['card_style'] = $style;
                $found = true;
                break;
            }
        }
        if ($found) {
            SystemSetting::set('org_chart_groups', $groups);
            OrgUnit::clearOrgCache();
            Notification::make()
                ->title(__('Section card template updated'))
                ->success()
                ->send();
        }
    }

    public function getActiveGroupCardStyle(): string
    {
        $groups = OrgUnit::getChartGroups();
        foreach ($groups as $group) {
            if (($group['key'] ?? 'main') === $this->activeChartGroup) {
                return $group['card_style'] ?? 'default';
            }
        }

        return 'default';
    }

    /**
     * @param  Collection<string, Collection<int, OrgUnit>>  $unitsByParent
     * @return array<int, array<string, mixed>>
     */
    protected function buildTree($unitsByParent, ?string $parentId = null): array
    {
        return $unitsByParent->get((string) ($parentId ?? '__root__'), collect())
            ->map(function (OrgUnit $unit) use ($unitsByParent) {
                return [
                    'id' => $unit->id,
                    'title' => $unit->getTranslation('title', app()->getLocale()),
                    'type' => $unit->type,
                    'card_style' => $unit->card_style,
                    'name' => $unit->employee?->name ?? ($unit->department ? $unit->department->getTranslation('name', app()->getLocale()) : 'N/A'),
                    'role' => $unit->employee?->role ?? $unit->type,
                    'image' => PublicStorage::urlIfExists($unit->employee?->image),
                    'isActive' => (bool) $unit->isActive,
                    'children' => $this->buildTree($unitsByParent, $unit->id),
                ];
            })->toArray();
    }

    public function saveOrder(array $data): void
    {
        $this->updateHierarchy($data);

        // Clear cache for both English and Khmer
        Cache::forget('about_orgchart_en');
        Cache::forget('about_orgchart_kh');
        Cache::forget('about_orgchart_km');
        Cache::forget('about_page_en');
        Cache::forget('about_page_kh');
        Cache::forget('about_page_km');

        Notification::make()
            ->title(__('Saved successfully'))
            ->success()
            ->send();

        $this->loadChartData();
    }

    /**
     * @param  array<int, array{id: string, children?: array<int, mixed>}>  $items
     */
    protected function updateHierarchy(array $items, ?string $parentId = null): void
    {
        foreach ($items as $index => $item) {
            OrgUnit::where('id', $item['id'])->update([
                'parentId' => $parentId,
                'orderIndex' => $index,
            ]);

            if (! empty($item['children'])) {
                $this->updateHierarchy($item['children'], $item['id']);
            }
        }
    }

    public function getTitle(): string
    {
        return __('Org Chart Management');
    }
}
