<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Email address'))
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                ImageColumn::make('image')
                    ->label(__('Profile'))
                    ->getStateUsing(fn ($record) => \App\Support\PublicStorage::url($record->image))
                    ->disk(config('filesystems.public_uploads_disk')),
                TextColumn::make('role')
                    ->label(__('Role'))
                    ->badge()
                    ->colors([
                        'danger' => 'ADMIN',
                        'info' => 'EDITOR',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'ADMIN' => __('Admin'),
                        'EDITOR' => __('Editor'),
                        default => $state ?: '-',
                    })
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label(__('Dashboard Access'))
                    ->disabled(fn ($record): bool => $record->id === auth()->id()),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
