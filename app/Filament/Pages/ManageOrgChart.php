<?php

namespace App\Filament\Pages;

use App\Filament\Exports\OrgUnitExporter;
use App\Filament\Imports\OrgUnitImporter;
use App\Filament\Resources\OrgUnits\OrgUnitResource;
use App\Filament\Resources\OrgUnits\Schemas\OrgUnitForm;
use App\Models\OrgUnit;
use App\Models\SystemSetting;
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
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

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
                ->modalWidth('lg')
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
                ->modalHeading(__('Load Organization Chart Template'))
                ->modalDescription(__('Choose a corporate template to quickly populate your organization chart without creating each position manually.'))
                ->modalWidth('md')
                ->modalSubmitActionLabel(__('Apply Template'))
                ->form([
                    Select::make('template')
                        ->label(__('Select Corporate Template'))
                        ->options([
                            'kimmex_corporate' => __('KIMMEX Corporate Structure (Full 3-Tier Enterprise: CEO, DCEO, DGM, 7 Divisions, 16+ Positions)'),
                            'standard_company' => __('Standard Business Structure (CEO, COO, CFO, CTO, 5 Key Departments - 8 Positions)'),
                            'starter_root' => __('Starter Hierarchy (CEO + 3 Core Division Heads - 4 Positions)'),
                        ])
                        ->default('kimmex_corporate')
                        ->required()
                        ->native(false),
                    Toggle::make('clear_existing')
                        ->label(__('Replace existing positions (Fresh start)'))
                        ->helperText(__('Clear existing organization units before loading the template. Recommended for a clean hierarchy.'))
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
                ->label(__('Manage Groups'))
                ->icon('heroicon-o-rectangle-group')
                ->color('gray')
                ->size('xs')
                ->modalHeading(__('Manage Organization Chart Groups'))
                ->modalDescription(__('Configure separate organization chart sections for your public About page (e.g. Corporate Governance, Project Teams, Board of Directors).'))
                ->modalWidth('4xl')
                ->modalSubmitActionLabel(__('Save Groups'))
                ->fillForm(fn () => [
                    'groups' => OrgUnit::getChartGroups(),
                ])
                ->form([
                    Placeholder::make('explanation')
                        ->hiddenLabel()
                        ->content(new HtmlString('
                            <div style="font-size: 0.8125rem; line-height: 1.5; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 0.25rem;">
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.375rem;">
                                    <span>💡</span> '.e(__('How Multi-Chart Groups Work:')).'
                                </div>
                                <ul style="list-style-type: disc; padding-left: 1.25rem; margin: 0; space-y: 0.25rem;">
                                    <li>'.e(__('Each group appears as an independent flowchart section on your website About page.')).'</li>
                                    <li>'.e(__('Drag the handle on the left to reorder how sections appear from top to bottom.')).'</li>
                                    <li>'.e(__('Toggle "Public" off to hide a group from the public website while keeping your data.')).'</li>
                                </ul>
                            </div>
                        ')),

                    Repeater::make('groups')
                        ->label(__('Configured Sections'))
                        ->compact()
                        ->table([
                            TableColumn::make(__('Section ID (Code)'))
                                ->width('160px'),
                            TableColumn::make(__('English Title'))
                                ->markAsRequired(),
                            TableColumn::make(__('Khmer Title')),
                            TableColumn::make(__('Card Style'))
                                ->width('160px'),
                            TableColumn::make(__('Public'))
                                ->alignment('center')
                                ->width('80px'),
                        ])
                        ->schema([
                            TextInput::make('key')
                                ->required()
                                ->regex('/^[a-z0-9_]+$/')
                                ->placeholder('e.g. board_of_directors')
                                ->readOnly(fn (?string $state) => $state === 'main')
                                ->helperText(fn (?string $state) => $state === 'main' ? __('Default main chart') : null)
                                ->extraInputAttributes(['style' => 'font-size: 0.8125rem; font-family: monospace;']),

                            TextInput::make('name_en')
                                ->required()
                                ->placeholder(__('e.g. Executive Board'))
                                ->extraInputAttributes(['style' => 'font-size: 0.8125rem;']),

                            TextInput::make('name_km')
                                ->placeholder(__('e.g. គណៈកម្មាធិការនាយក'))
                                ->extraInputAttributes(['style' => 'font-size: 0.8125rem;']),

                            Select::make('card_style')
                                ->options([
                                    'default' => __('Global Default'),
                                    'avatar_top' => __('Circle Photo Above Box'),
                                    'floating' => __('Floating Avatar'),
                                    'badge' => __('Executive Badge'),
                                    'capsule' => __('Horizontal Capsule'),
                                    'corporate' => __('Corporate Tagged'),
                                ])
                                ->default('default')
                                ->native(false)
                                ->extraInputAttributes(['style' => 'font-size: 0.8125rem;']),

                            Toggle::make('is_active')
                                ->hiddenLabel()
                                ->default(true),
                        ])
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
            ->modalWidth('lg')
            ->modalSubmitActionLabel(__('Save Changes'))
            ->model(OrgUnit::class)
            ->form(OrgUnitForm::getSchema(context: 'edit'))
            ->fillForm(fn (array $arguments): array => OrgUnit::find($arguments['id'])->toArray())
            ->action(function (array $data, array $arguments): void {
                OrgUnit::find($arguments['id'])->update($data);
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
            ->modalHeading(fn (array $arguments): string => __('Add Subordinate under :name', [
                'name' => OrgUnit::find($arguments['id'] ?? null)?->title ?? __('Position'),
            ]))
            ->modalWidth('lg')
            ->modalSubmitActionLabel(__('Add Subordinate'))
            ->model(OrgUnit::class)
            ->form(OrgUnitForm::getSchema(context: 'child'))
            ->fillForm(fn (array $arguments): array => [
                'parentId' => $arguments['id'] ?? null,
                'type' => 'STAFF',
                'chart_group' => $this->activeChartGroup,
                'isActive' => true,
                'orderIndex' => 0,
            ])
            ->action(function (array $data, array $arguments): void {
                $data['parentId'] = $arguments['id'] ?? $data['parentId'] ?? null;
                OrgUnit::create($data);
                $this->loadChartData();
                Notification::make()
                    ->title(__('Subordinate added successfully'))
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
