<?php

namespace App\Filament\Resources\OrgUnits\Tables;

use App\Filament\Exports\OrgUnitExporter;
use App\Filament\Imports\OrgUnitImporter;
use App\Filament\Support\FlatRecordDetails;
use App\Models\OrgUnit;
use App\Services\OrgStructureTemplateService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class OrgUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['employee', 'department']))
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->description(fn (OrgUnit $record) => $record->getPath())
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('Tier / Type'))
                    ->badge()
                    ->colors([
                        'danger' => 'EXECUTIVE',
                        'warning' => 'MANAGEMENT',
                        'success' => 'DIRECTOR',
                        'info' => 'MANAGER',
                        'primary' => 'STAFF',
                        'secondary' => 'DEPARTMENT',
                        'gray' => 'OFFICE',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'EXECUTIVE' => __('Executive'),
                        'MANAGEMENT' => __('Management'),
                        'DIRECTOR' => __('Director'),
                        'MANAGER' => __('Manager'),
                        'STAFF' => __('Individual'),
                        'DEPARTMENT' => __('Department'),
                        'OFFICE' => __('Facility'),
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'EXECUTIVE' => 'heroicon-o-sparkles',
                        'MANAGEMENT' => 'heroicon-o-shield-check',
                        'DIRECTOR' => 'heroicon-o-academic-cap',
                        'MANAGER' => 'heroicon-o-identification',
                        'STAFF' => 'heroicon-o-user',
                        'DEPARTMENT' => 'heroicon-o-building-office-2',
                        'OFFICE' => 'heroicon-o-map-pin',
                    }),

                TextColumn::make('employee.name')
                    ->label(__('Assigned Employee'))
                    ->placeholder('-')
                    ->weight('semibold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department.name')
                    ->label(__('Related Dept'))
                    ->placeholder('-')
                    ->color('gray')
                    ->searchable(),

                TextInputColumn::make('orderIndex')
                    ->label(__('Sort'))
                    ->sortable(),
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),

            ])
            ->defaultSort('orderIndex')
            ->groups([
                Group::make('type')
                    ->label(__('Organizational Tier'))
                    ->collapsible(),
                Group::make('department.name')
                    ->label(__('Department'))
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('Filter by Tier'))
                    ->options([
                        'EXECUTIVE' => __('C-Suite'),
                        'MANAGEMENT' => __('Senior Management'),
                        'DIRECTOR' => __('Directors'),
                        'MANAGER' => __('Managers'),
                        'STAFF' => __('Staff'),
                        'DEPARTMENT' => __('Departments'),
                    ]),
                SelectFilter::make('departmentId')
                    ->label(__('Department'))
                    ->relationship('department', 'name', fn ($query) => $query->orderBy('name->en')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                    EditAction::make(),
                ])
                    ->icon(Heroicon::EllipsisVertical)
                    ->iconButton()
                    ->tooltip(__('Actions')),
            ])
            ->headerActions([
                Action::make('loadTemplate')
                    ->label(__('Load Template'))
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->modalHeading(__('Load Organization Chart Template'))
                    ->modalDescription(__('Choose a corporate template to quickly populate your organization chart without creating each position manually.'))
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
                            ->helperText(__('Clear existing organization units before loading the template.'))
                            ->default(true),
                    ])
                    ->action(function (array $data): void {
                        $count = OrgStructureTemplateService::applyTemplate(
                            (string) ($data['template'] ?? 'kimmex_corporate'),
                            (bool) ($data['clear_existing'] ?? true),
                        );

                        Notification::make()
                            ->title(__('Corporate template applied successfully! :count positions loaded.', ['count' => $count]))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),

                ImportAction::make('importOrgUnits')
                    ->label(__('Import Positions'))
                    ->importer(OrgUnitImporter::class)
                    ->fileRules(['max:5120'])
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),

                ExportAction::make('exportOrgUnits')
                    ->label(__('Export Positions'))
                    ->exporter(OrgUnitExporter::class)
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),

                Action::make('downloadCsvTemplate')
                    ->label(__('CSV Template'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(asset('org-chart-importer-example.csv'))
                    ->openUrlInNewTab()
                    ->tooltip(__('Download sample CSV template')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(OrgUnitExporter::class)
                        ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
