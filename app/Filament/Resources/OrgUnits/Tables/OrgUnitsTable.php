<?php

namespace App\Filament\Resources\OrgUnits\Tables;

use App\Filament\Exports\OrgUnitExporter;
use App\Filament\Imports\OrgUnitImporter;
use App\Filament\Pages\ManageOrgChart;
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
use Filament\Tables\Columns\ImageColumn;
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
            ->modifyQueryUsing(fn ($query) => $query->with(['employee', 'department', 'parent']))
            ->columns([
                ImageColumn::make('employee.image')
                    ->label('')
                    ->circular()
                    ->disk(config('filesystems.public_uploads_disk', 'public'))
                    ->defaultImageUrl(fn (OrgUnit $record) => $record->employee ? 'https://ui-avatars.com/api/?name='.urlencode($record->employee->name).'&color=0B2B5C&background=EBF4FF' : null)
                    ->size(32),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->description(fn (OrgUnit $record) => $record->getPath())
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('Tier'))
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
                        'STAFF' => __('Staff'),
                        'DEPARTMENT' => __('Department'),
                        'OFFICE' => __('Office'),
                        default => $state,
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'EXECUTIVE' => 'heroicon-o-sparkles',
                        'MANAGEMENT' => 'heroicon-o-shield-check',
                        'DIRECTOR' => 'heroicon-o-academic-cap',
                        'MANAGER' => 'heroicon-o-identification',
                        'STAFF' => 'heroicon-o-user',
                        'DEPARTMENT' => 'heroicon-o-building-office-2',
                        'OFFICE' => 'heroicon-o-map-pin',
                        default => 'heroicon-o-user',
                    }),

                TextColumn::make('employee.name')
                    ->label(__('Team Member'))
                    ->placeholder('-')
                    ->weight('semibold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('chart_group')
                    ->label(__('Section'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => OrgUnit::getChartGroupOptions()[$state ?? 'main'] ?? ($state ?: 'main'))
                    ->sortable(),

                TextColumn::make('department.name')
                    ->label(__('Department'))
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

                TextColumn::make('card_style')
                    ->label(__('Template'))
                    ->badge()
                    ->placeholder(__('Inherit'))
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'avatar_top' => __('Circle Photo (T1)'),
                        'floating' => __('Floating Avatar (T2)'),
                        'badge' => __('Executive Badge (T3)'),
                        'capsule' => __('Capsule (T4)'),
                        'corporate' => __('Corporate (T5)'),
                        'nameplate' => __('Framed Nameplate (T6)'),
                        default => __('Inherit'),
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('orderIndex')
            ->groups([
                Group::make('chart_group')
                    ->label(__('Section'))
                    ->collapsible(),
                Group::make('type')
                    ->label(__('Tier'))
                    ->collapsible(),
                Group::make('department.name')
                    ->label(__('Department'))
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('chart_group')
                    ->label(__('Section'))
                    ->options(fn () => OrgUnit::getChartGroupOptions()),

                SelectFilter::make('type')
                    ->label(__('Tier'))
                    ->options([
                        'EXECUTIVE' => __('Executive'),
                        'MANAGEMENT' => __('Management'),
                        'DIRECTOR' => __('Director'),
                        'MANAGER' => __('Manager'),
                        'STAFF' => __('Staff'),
                        'DEPARTMENT' => __('Department'),
                        'OFFICE' => __('Office'),
                    ]),

                SelectFilter::make('departmentId')
                    ->label(__('Department'))
                    ->relationship('department', 'name', fn ($query) => $query->orderBy('name->en')),

                SelectFilter::make('isActive')
                    ->label(__('Status'))
                    ->options([
                        '1' => __('Active'),
                        '0' => __('Hidden'),
                    ]),
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
                Action::make('visualOrgChart')
                    ->label(__('Visual Org Chart'))
                    ->icon('heroicon-o-presentation-chart-line')
                    ->color('primary')
                    ->url(ManageOrgChart::getUrl()),

                Action::make('loadTemplate')
                    ->label(__('Load Template'))
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->modalHeading(__('Load Template'))
                    ->modalDescription(__('Choose a corporate template to populate the organization chart.'))
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

                        $count = OrgStructureTemplateService::applyTemplate(
                            (string) ($data['template'] ?? 'kimmex_corporate'),
                            (bool) ($data['clear_existing'] ?? true),
                        );

                        Notification::make()
                            ->title(__('Corporate template applied successfully! :count positions loaded.', ['count' => $count]))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (): bool => (app()->isLocal() || app()->runningUnitTests()) && (auth()->user()?->isAdmin() ?? false)),

                ImportAction::make('importOrgUnits')
                    ->label(__('Import CSV'))
                    ->importer(OrgUnitImporter::class)
                    ->fileRules(['max:5120'])
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),

                ExportAction::make('exportOrgUnits')
                    ->label(__('Export CSV'))
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
