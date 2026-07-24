<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Filament\Support\FlatRecordDetails;
use App\Models\Project;
use App\Support\PublicStorage;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('projectCategory'))
            ->columns([
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->searchable(),
                ImageColumn::make('heroImage')
                    ->label(__('Image'))
                    ->getStateUsing(fn (Project $record): ?string => ($imageUrl = PublicStorage::urlIfExists($record->heroImage)) ? url($imageUrl) : null)
                    ->square()
                    ->imageSize(48),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->words(6)
                    ->searchable()
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('title->en', $direction))
                    ->description(fn ($record) => $record->slug),
                TextColumn::make('projectCategory.name')
                    ->label(__('Category'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('completionDate')
                    ->label(__('Completion Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                    EditAction::make(),
                    Action::make('view_on_website')
                        ->label(__('View on Website'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('info')
                        ->url(fn ($record) => url("/projects/{$record->slug}"))
                        ->openUrlInNewTab(),
                    Action::make('copy_link')
                        ->label(__('Copy Link'))
                        ->icon('heroicon-o-link')
                        ->color('gray')
                        ->extraAttributes(fn ($record) => [
                            'x-on:click' => "window.navigator.clipboard.writeText('".url("/projects/{$record->slug}")."')",
                        ])
                        ->action(fn () => Notification::make()->title(__('Link Copied'))->success()->send()),
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
