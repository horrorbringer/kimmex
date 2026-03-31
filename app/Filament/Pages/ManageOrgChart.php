<?php

namespace App\Filament\Pages;

use App\Models\OrgUnit;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class ManageOrgChart extends Page
{
    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-rectangle-stack';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Site Content');
    }

    public static function getNavigationSort(): ?int
    {
        return 0;
    }

    public static function getNavigationLabel(): string
    {
        return __('Org Chart (Design Z)');
    }

    public function getTitle(): string
    {
        return __('Org Chart Management');
    }

    public function getSubheading(): ?string
    {
        return null; // subtitle rendered in blade
    }

    protected string $view = 'filament.pages.manage-org-chart';

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
                'role' => $unit->getTranslation('title', app()->getLocale())
                    ?: $unit->getTranslation('title', 'en')
                    ?: ($unit->employee?->role ?? ($unit->department ? $unit->department->getTranslation('name', app()->getLocale()) : 'Staff')),
                'image' => $unit->employee?->image ? \Illuminate\Support\Facades\Storage::url($unit->employee->image) : null,
                'children' => $this->loadChildren($unit->id),
            ])
            ->toArray();
    }

    public function updateHierarchy($data)
    {
        if (empty($data))
            return;

        \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $this->saveLevel($data, null);
        });

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
            Action::make('addTopLevel')
                ->label(__('New Top Level / CEO'))
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->form([
                    Select::make('employeeId')
                        ->label(__('Select Employee'))
                        ->options(\App\Models\Employee::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('title')
                        ->label(__('Custom Title (Optional)'))
                        ->helperText(__('If left blank, employee role will be used.')),
                ])
                ->action(function (array $data) {
                    OrgUnit::create([
                        'employeeId' => $data['employeeId'],
                        'title' => $data['title'] ? ['en' => $data['title']] : null,
                        'parentId' => null,
                        'orderIndex' => OrgUnit::whereNull('parentId')->count(),
                    ]);
                    $this->refreshTree();
                    $this->dispatch('node-added', parentId: null);
                    Notification::make()->title(__('Root added successfully'))->success()->send();
                }),
            Action::make('reset')
                ->label(__('Reset Items'))
                ->icon('heroicon-o-arrow-path')
                ->action('refreshTree')
                ->color('gray'),
            Action::make('publish')
                ->label(__('Publish Changes'))
                ->icon('heroicon-o-cloud-arrow-up')
                ->action(fn() => Notification::make()->title(__('Live Changes Published'))->success()->send())
                ->color('primary'),
        ];
    }

    public function addChildAction(): Action
    {
        return Action::make('addChild')
            ->label(__('Add Staff'))
            ->icon('heroicon-o-plus')
            ->color('gray')
            ->form([
                Select::make('employeeId')
                    ->label(__('Select Employee'))
                    ->options(\App\Models\Employee::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('title')
                    ->label(__('Custom Title (Optional)')),
            ])
            ->action(function (array $data, array $arguments) {
                OrgUnit::create([
                    'employeeId' => $data['employeeId'],
                    'title' => $data['title'] ? ['en' => $data['title']] : null,
                    'parentId' => $arguments['parentId'],
                    'orderIndex' => OrgUnit::where('parentId', $arguments['parentId'])->count(),
                ]);
                $this->refreshTree();
                $this->dispatch('node-added', parentId: $arguments['parentId']);
                Notification::make()->title(__('Staff member added'))->success()->send();
            });
    }

    public function editNodeAction(): Action
    {
        return Action::make('editNode')
            ->label(__('Edit Member'))
            ->icon('heroicon-o-pencil-square')
            ->fillForm(fn(array $arguments) => OrgUnit::find($arguments['id'])?->toArray())
            ->form([
                Select::make('employeeId')
                    ->label(__('Select Employee'))
                    ->options(\App\Models\Employee::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('title.en')
                    ->label(__('Title (English)'))
                    ->placeholder('e.g. Finance Director'),
                TextInput::make('title.kh')
                    ->label(__('Title (Khmer)'))
                    ->placeholder('ឧទាហរណ៍៖ ប្រធានផ្នែកហិរញ្ញវត្ថុ'),
                Select::make('departmentId')
                    ->label(__('Department'))
                    ->options(\App\Models\Department::all()->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->action(function (array $data, array $arguments) {
                OrgUnit::find($arguments['id'])->update($data);
                $this->refreshTree();
                Notification::make()->title(__('Member updated'))->success()->send();
            });
    }

    public function deleteNodeAction(): Action
    {
        return Action::make('deleteNode')
            ->label(__('Delete'))
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                OrgUnit::where('id', $arguments['id'])->delete();
                $this->refreshTree();
                Notification::make()->title(__('Member removed'))->danger()->send();
            });
    }
}
