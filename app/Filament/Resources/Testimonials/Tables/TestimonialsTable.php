<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clientName')
                    ->label(__('Client Name'))
                    ->searchable(),
                TextColumn::make('company')
                    ->label(__('Company'))
                    ->searchable(),
                TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('isFeatured')
                    ->label(__('Featured'))
                    ->boolean(),
                TextColumn::make('orderIndex')
                    ->label(__('Order'))
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger')
                    ->afterStateUpdated(function () {
                        foreach (['en', 'km', 'kh'] as $locale) {
                            \Illuminate\Support\Facades\Cache::forget("home_testimonials_array_{$locale}");
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make()->schema(fn ($record): array => \App\Filament\Support\FlatRecordDetails::schema($record)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
