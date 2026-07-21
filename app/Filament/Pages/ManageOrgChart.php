<?php

namespace App\Filament\Pages;

use App\Filament\Resources\OrgUnits\Schemas\OrgUnitForm;
use App\Models\OrgUnit;
use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
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

    public $chartData = [];

    public ?array $data = [];

    public function mount()
    {
        $org = SystemSetting::get('organization_profile', []);
        $this->form->fill([
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
                Section::make(__('Display Mode'))
                    ->description(__('Choose whether to build an interactive chart below, or upload a static file.'))
                    ->schema([
                        Select::make('org_chart_type')
                            ->label(__('Chart Type'))
                            ->options([
                                'dynamic' => __('Interactive Chart (Builder Below)'),
                                'image' => __('Upload Image (PNG/JPG)'),
                                'pdf' => __('Upload PDF'),
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
                            ->visible(fn ($get) => $get('org_chart_type') === 'image'),
                        FileUpload::make('org_chart_pdf')
                            ->label(__('Organization Chart PDF'))
                            ->disk(config('filesystems.public_uploads_disk'))
                            ->directory('organization')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(1048576) // 1GB limit in app
                            ->visible(fn ($get) => $get('org_chart_type') === 'pdf'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function saveDisplaySettings()
    {
        $org = SystemSetting::get('organization_profile', []);
        $org = array_merge($org, $this->form->getState());
        SystemSetting::set('organization_profile', $org);

        // Clear cache
        Cache::forget('about_orgchart_en');
        Cache::forget('about_orgchart_kh');
        Cache::forget('about_orgchart_km');

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
                ->model(OrgUnit::class)
                ->form(OrgUnitForm::getSchema()),
        ];
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->model(OrgUnit::class)
            ->form(OrgUnitForm::getSchema())
            ->fillForm(fn (array $arguments): array => OrgUnit::find($arguments['id'])->toArray())
            ->action(function (array $data, array $arguments): void {
                OrgUnit::find($arguments['id'])->update($data);
                $this->loadChartData();
            });
    }

    public function addChildAction(): Action
    {
        return Action::make('addChild')
            ->model(OrgUnit::class)
            ->form(OrgUnitForm::getSchema())
            ->action(function (array $data, array $arguments): void {
                $data['parentId'] = $arguments['id'];
                OrgUnit::create($data);
                $this->loadChartData();
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->action(function (array $arguments): void {
                $unit = OrgUnit::find($arguments['id']);
                if ($unit) {
                    $unit->delete();
                }
                $this->loadChartData();
            });
    }

    public function loadChartData()
    {
        $unitsByParent = OrgUnit::with(['employee', 'department'])
            ->orderBy('orderIndex')
            ->get()
            ->groupBy(fn (OrgUnit $unit): string => (string) ($unit->parentId ?? '__root__'));

        $this->chartData = $this->buildTree($unitsByParent);
        $this->dispatch('chartUpdated');
    }

    protected function buildTree($unitsByParent, $parentId = null)
    {
        return $unitsByParent->get((string) ($parentId ?? '__root__'), collect())
            ->map(function (OrgUnit $unit) use ($unitsByParent) {
                return [
                    'id' => $unit->id,
                    'title' => $unit->getTranslation('title', app()->getLocale()),
                    'type' => $unit->type,
                    'name' => $unit->employee?->name ?? ($unit->department ? $unit->department->getTranslation('name', app()->getLocale()) : 'N/A'),
                    'role' => $unit->employee?->role ?? $unit->type,
                    'image' => $unit->employee?->image,
                    'children' => $this->buildTree($unitsByParent, $unit->id),
                ];
            })->toArray();
    }

    public function saveOrder($data)
    {
        $this->updateHierarchy($data);

        // Clear cache for both English and Khmer
        Cache::forget('about_orgchart_en');
        Cache::forget('about_orgchart_kh');
        Cache::forget('about_orgchart_km');

        Notification::make()
            ->title(__('Saved successfully'))
            ->success()
            ->send();

        $this->loadChartData();
    }

    protected function updateHierarchy($items, $parentId = null)
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
