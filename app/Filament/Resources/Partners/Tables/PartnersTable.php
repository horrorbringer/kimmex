<?php

namespace App\Filament\Resources\Partners\Tables;

use App\Filament\Support\FlatRecordDetails;
use App\Models\Partner;
use App\Support\PublicStorage;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logoUrl')
                    ->label(__('Logo'))
                    ->getStateUsing(fn (Partner $record): ?string => ($logoUrl = PublicStorage::urlIfExists($record->logoUrl)) ? url($logoUrl) : null)
                    ->defaultImageUrl(asset('images/partner-placeholder.svg'))
                    ->square()
                    ->imageSize(48),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->searchable(),
                TextColumn::make('orderIndex')
                    ->label(__('Order'))
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                //
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
