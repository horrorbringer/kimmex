<?php

namespace App\Filament\Resources\OrgUnits\Schemas;

use App\Filament\Support\TranslationHelper;
use App\Models\Employee;
use App\Models\OrgUnit;
use App\Support\PublicStorage;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class OrgUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(static::getSchema());
    }

    public static function getSchema(?string $context = null, ?string $parentId = null): array
    {
        return [
            // 1. Compact Context Pill for Subordinate mode
            Placeholder::make('supervisor_preview')
                ->hiddenLabel()
                ->visible(fn () => $context === 'child' && filled($parentId))
                ->content(function () use ($parentId) {
                    $parent = OrgUnit::with(['employee', 'department'])->find($parentId);
                    if (! $parent) {
                        return null;
                    }
                    $parentName = $parent->employee?->name;
                    $parentTitle = $parent->title;
                    $avatar = $parent->employee?->image ? PublicStorage::urlIfExists($parent->employee->image) : null;
                    $initials = strtoupper(substr($parentName ?: $parentTitle, 0, 2));

                    return new HtmlString('
                        <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 9999px; padding: 0.25rem 0.75rem 0.25rem 0.35rem; margin-bottom: 0.5rem; font-size: 0.75rem;">
                            <div style="width: 1.5rem; height: 1.5rem; border-radius: 9999px; background: #0b2b5c; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.625rem; overflow: hidden; flex-shrink: 0;">
                                '.($avatar ? '<img src="'.e($avatar).'" style="width:100%;height:100%;object-fit:cover;" />' : e($initials)).'
                            </div>
                            <span style="color: #64748b;">'.e(__('Reports to')).':</span>
                            <strong style="color: #0b2b5c;">'.e($parentTitle).'</strong>
                            '.($parentName ? '<span style="color: #475569;">('.e($parentName).')</span>' : '').'
                        </div>
                    ');
                }),

            // In child mode, include hidden parentId field to guarantee submission binding
            Hidden::make('parentId')
                ->default(fn () => $parentId)
                ->visible(fn () => $context === 'child'),

            // 2. Main Details Section
            Section::make(__('Position Details'))
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 2])->schema([
                        Select::make('employeeId')
                            ->label(__('Team Member'))
                            ->relationship('employee', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Employee $record) => "{$record->name}".($record->role ? " — {$record->role}" : ''))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $employee = Employee::find($state);
                                if ($employee) {
                                    $set('title', filled($employee->role) ? $employee->role : $employee->name);
                                }
                            })
                            ->columnSpan(['default' => 1, 'sm' => 2]),

                        TextInput::make('title')
                            ->label(__('Title'))
                            ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                            ->required()
                            ->columnSpan(1),

                        Select::make('type')
                            ->label(__('Tier'))
                            ->options([
                                'EXECUTIVE' => __('Executive'),
                                'MANAGEMENT' => __('Management'),
                                'DIRECTOR' => __('Director'),
                                'MANAGER' => __('Manager'),
                                'STAFF' => __('Staff'),
                                'DEPARTMENT' => __('Department'),
                                'OFFICE' => __('Office'),
                            ])
                            ->native(false)
                            ->selectablePlaceholder(false)
                            ->default('STAFF')
                            ->required()
                            ->columnSpan(1),

                        Select::make('departmentId')
                            ->label(__('Department'))
                            ->relationship('department', 'name', fn ($query) => $query->orderBy('name->en'))
                            ->searchable()
                            ->preload()
                            ->columnSpan(['default' => 1, 'sm' => 2]),
                    ]),
                ]),

            // 3. Hierarchy & Placement Section
            Section::make(__('Hierarchy & Placement'))
                ->icon('heroicon-o-bars-3-bottom-left')
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 2])->schema([
                        Select::make('parentId')
                            ->label(__('Reports To'))
                            ->relationship('parent', 'title', fn ($query, ?Model $record) => $query->orderBy('title->en')->when($record, fn ($q) => $q->where('id', '!=', $record->id)))
                            ->getOptionLabelFromRecordUsing(fn (OrgUnit $record) => "{$record->title}".($record->employee ? " ({$record->employee->name})" : ''))
                            ->searchable()
                            ->preload()
                            ->placeholder(__('None (Root)'))
                            ->visible(fn () => $context !== 'root' && $context !== 'child')
                            ->columnSpan(['default' => 1, 'sm' => 2]),

                        Select::make('chart_group')
                            ->label(__('Section'))
                            ->options(fn () => OrgUnit::getChartGroupOptions())
                            ->allowHtml(false)
                            ->native(false)
                            ->default('main')
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        TextInput::make('orderIndex')
                            ->label(__('Sort Order'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->columnSpan(1),
                    ]),
                ]),
        ];
    }
}
