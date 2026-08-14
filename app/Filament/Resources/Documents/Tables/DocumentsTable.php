<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Filament\Support\FlatRecordDetails;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['documentCategory', 'department']))
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('title->en', $direction))
                    ->description(fn ($record) => $record->slug),

                TextColumn::make('documentCategory.name')
                    ->label(__('Category'))
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('fileType')
                    ->label(__('File Type'))
                    ->badge()
                    ->color(fn ($state, $record) => str_starts_with((string) $record->fileUrl, 'http') ? 'info' : 'gray')
                    ->formatStateUsing(function ($state, $record) {
                        $isExternal = str_starts_with((string) $record->fileUrl, 'http');
                        $ext = $state ?: ($isExternal ? 'URL' : 'FILE');

                        return ($isExternal ? '🔗 ' : '📄 ').$ext;
                    }),
                ToggleColumn::make('isPublic')
                    ->label(__('Public')),
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('downloadCount')
                    ->label(__('Downloads'))
                    ->numeric()
                    ->sortable(),
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
                        ->url(fn ($record) => url("/documents/{$record->slug}"))
                        ->openUrlInNewTab(),
                    Action::make('copy_link')
                        ->label(__('Copy Link'))
                        ->icon('heroicon-o-link')
                        ->color('gray')
                        ->extraAttributes(fn ($record) => [
                            'x-on:click' => "window.navigator.clipboard.writeText('".url("/documents/{$record->slug}")."')",
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
