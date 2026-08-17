<?php

namespace App\Filament\Resources\Sectors\Tables;

use App\Filament\Support\FlatRecordDetails;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
            ]);
    }
}
