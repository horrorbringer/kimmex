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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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

    public function mount(): void
    {
        $org = SystemSetting::get('organization_profile', []);
        $this->form->fill([
            'org_chart_visible' => (bool) ($org['org_chart_visible'] ?? true),
            'org_chart_type' => $org['org_chart_type'] ?? 'dynamic',
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

        // Clear cache
        Cache::forget('about_orgchart_en');
        Cache::forget('about_orgchart_kh');
        Cache::forget('about_orgchart_km');
        Cache::forget('about_page_en');
        Cache::forget('about_page_kh');
        Cache::forget('about_page_km');

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
                ->modalHeading(__('Add Root Position'))
                ->modalWidth('lg')
                ->modalSubmitActionLabel(__('Create Position'))
                ->model(OrgUnit::class)
                ->form(OrgUnitForm::getSchema(context: 'root')),

            Action::make('loadTemplate')
                ->label(__('Load Template'))
                ->icon('heroicon-o-sparkles')
                ->color('success')
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

            Action::make('displaySettings')
                ->label(__('Website Display'))
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->modalHeading(__('Website Display Settings'))
                ->modalDescription(__('Configure how the organization chart is presented on the public About page.'))
                ->modalWidth('md')
                ->modalSubmitActionLabel(__('Save Settings'))
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

                    Cache::forget('about_orgchart_en');
                    Cache::forget('about_orgchart_kh');
                    Cache::forget('about_orgchart_km');
                    Cache::forget('about_page_en');
                    Cache::forget('about_page_kh');
                    Cache::forget('about_page_km');

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
                ->button(),
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
            ->orderBy('orderIndex')
            ->get()
            ->groupBy(fn (OrgUnit $unit): string => (string) ($unit->parentId ?? '__root__'));

        $this->chartData = $this->buildTree($unitsByParent);
        $this->dispatch('chartUpdated', chartData: $this->chartData);
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
