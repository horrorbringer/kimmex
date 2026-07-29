<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Filament\Support\FlatRecordDetails;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

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
                            Cache::forget("home_testimonials_array_{$locale}");
                        }
                    }),
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
