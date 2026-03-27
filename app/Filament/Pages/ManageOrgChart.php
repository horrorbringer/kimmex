<?php

namespace App\Filament\Pages;

use App\Models\OrgUnit;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageOrgChart extends Page
{
    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-rectangle-stack';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Organization');
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationLabel(): string
    {
        return __('Hierarchy Manager');
    }

    public function getTitle(): string
    {
        return __('Org Chart Management');
    }

    protected static string $view = 'filament.pages.manage-org-chart';

    public $tree = [];

    public function mount()
    {
        $this->refreshTree();
    }

    public function refreshTree()
    {
        $this->tree = $this->loadChildren(null);
    }

    protected function loadChildren($parentId)
    {
        return OrgUnit::where('parentId', $parentId)
            ->orderBy('orderIndex')
            ->with(['employee', 'department'])
            ->get()
            ->map(fn(OrgUnit $unit) => [
                'id' => $unit->id,
                'name' => $unit->employee?->name ?? ($unit->getTranslation('title', app()->getLocale()) ?: $unit->getTranslation('title', 'en')),
                'role' => $unit->employee?->role ?? ($unit->department ? $unit->department->getTranslation('name', app()->getLocale()) : 'Staff'),
                'image' => $unit->employee?->image ? \Illuminate\Support\Facades\Storage::url($unit->employee->image) : null,
                'children' => $this->loadChildren($unit->id),
            ])
            ->toArray();
    }

    public function updateHierarchy($data)
    {
        $this->saveLevel($data, null);

        Notification::make()
            ->title(__('Hierarchy saved successfully'))
            ->success()
            ->send();

        $this->refreshTree();
    }

    protected function saveLevel($items, $parentId)
    {
        foreach ($items as $idx => $item) {
            OrgUnit::where('id', $item['id'])->update([
                'parentId' => $parentId,
                'orderIndex' => $idx
            ]);

            if (!empty($item['children'])) {
                $this->saveLevel($item['children'], $item['id']);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reset')
                ->label(__('Reset Order'))
                ->action('refreshTree')
                ->color('gray'),
            Action::make('publish')
                ->label(__('Publish Changes'))
                ->action(fn() => Notification::make()->title(__('Live Changes Published'))->success()->send())
                ->color('primary'),
        ];
    }
}
