<?php

namespace App\Filament\Pages;

use App\Models\OrgUnit;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class ManageOrgChart extends Page implements \Filament\Actions\Contracts\HasActions
{
    use \Filament\Actions\Concerns\InteractsWithActions;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected string $view = 'filament.pages.manage-org-chart';

    protected static string|\UnitEnum|null $navigationGroup = 'HR Management';

    protected static ?int $navigationSort = 1;


    public $chartData = [];

    public function mount()
    {
        $this->loadChartData();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('addRoot')
                ->label(__('Add Root Unit'))
                ->model(OrgUnit::class)
                ->form(\App\Filament\Resources\OrgUnits\Schemas\OrgUnitForm::getSchema()),
        ];
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->model(OrgUnit::class)
            ->form(\App\Filament\Resources\OrgUnits\Schemas\OrgUnitForm::getSchema())
            ->fillForm(fn(array $arguments): array => OrgUnit::find($arguments['id'])->toArray())
            ->action(function (array $data, array $arguments): void {
                OrgUnit::find($arguments['id'])->update($data);
                $this->loadChartData();
            });
    }

    public function addChildAction(): Action
    {
        return Action::make('addChild')
            ->model(OrgUnit::class)
            ->form(\App\Filament\Resources\OrgUnits\Schemas\OrgUnitForm::getSchema())
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
        $this->chartData = $this->buildTree();
    }

    protected function buildTree($parentId = null)
    {
        return OrgUnit::where('parentId', $parentId)
            ->with(['employee', 'department'])
            ->orderBy('orderIndex')
            ->get()
            ->map(function (OrgUnit $unit) {
                return [
                    'id' => $unit->id,
                    'title' => $unit->getTranslation('title', app()->getLocale()),
                    'type' => $unit->type,
                    'name' => $unit->employee?->name ?? ($unit->department ? $unit->department->getTranslation('name', app()->getLocale()) : 'N/A'),
                    'role' => $unit->employee?->role ?? $unit->type,
                    'image' => $unit->employee?->image,
                    'children' => $this->buildTree($unit->id),
                ];
            })->toArray();
    }

    public function saveOrder($data)
    {
        // Recursive function to update hierarchy from flattened Sortable data
        $this->updateHierarchy($data);

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

            if (!empty($item['children'])) {
                $this->updateHierarchy($item['children'], $item['id']);
            }
        }
    }

    public function getTitle(): string
    {
        return __('Org Chart Management');
    }
}
