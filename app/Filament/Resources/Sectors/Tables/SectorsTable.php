<?php

namespace App\Filament\Resources\Sectors\Tables;

use App\Filament\Resources\Sectors\Pages\ListSectors;
use App\Filament\Support\FlatRecordDetails;
use App\Models\Sector;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SectorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orderIndex')
                    ->label('#')
                    ->sortable(),

                ImageColumn::make('image')
                    ->label(__('Image'))
                    ->disk(config('filesystems.public_uploads_disk'))
                    ->visibility('public')
                    ->circular(false)
                    ->square(),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('title->en', $direction)),

                TextColumn::make('icon')
                    ->label(__('Icon'))
                    ->fontFamily(FontFamily::Mono),

                ToggleColumn::make('isActive')
                    ->label(__('Is Active'))
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->reorderable('orderIndex')
            ->defaultSort('orderIndex')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                    EditAction::make(),
                ])
                    ->icon(Heroicon::EllipsisVertical)
                    ->iconButton()
                    ->tooltip(__('Actions')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('No sectors found'))
            ->emptyStateDescription(__('Create your first sector or generate the standard default sectors.'))
            ->emptyStateActions([
                Action::make('generateDefaultSectorsEmpty')
                    ->label(__('Generate Default Sectors'))
                    ->icon('heroicon-m-sparkles')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading(__('Generate default sectors?'))
                    ->modalDescription(__('This will create default industry sectors (Government, Education, Commercial, Infrastructure).'))
                    ->action(function (): void {
                        foreach (ListSectors::defaultSectors() as $sector) {
                            Sector::updateOrCreate(
                                ['orderIndex' => $sector['orderIndex']],
                                $sector,
                            );
                        }

                        Notification::make()
                            ->title(__('Default sectors generated successfully'))
                            ->success()
                            ->send();
                    }),
                CreateAction::make(),
            ]);
    }
}
