<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Filament\Support\FlatRecordDetails;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('causer'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('event')
                    ->label(__('Action'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => __(Str::headline($state ?: 'Updated')))
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'deleted' => 'danger',
                        default => 'warning',
                    })
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('What Changed'))
                    ->limit(60)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->label(__('Changed Item'))
                    ->getStateUsing(fn ($record): string => self::formatChangedItem($record))
                    ->searchable(),
                TextColumn::make('causer.name')
                    ->label(__('Changed By'))
                    ->placeholder(__('System'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('When'))
                    ->since()
                    ->sortable(),
                TextColumn::make('causer_type')
                    ->label(__('User Type'))
                    ->formatStateUsing(fn (?string $state): string => $state ? Str::headline(class_basename($state)) : __('System'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('causer_id')
                    ->label(__('User ID'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('log_name')
                    ->label(__('Log Name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('batch_uuid')
                    ->label(__('Batch UUID'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
            ])
            ->toolbarActions([]);
    }

    protected static function formatChangedItem($record): string
    {
        $type = $record->subject_type
            ? __(Str::headline(class_basename($record->subject_type)))
            : __('System');

        $subject = $record->subject;

        if (! $subject instanceof Model) {
            return $record->subject_id
                ? "{$type}: ".__('Deleted record')
                : $type;
        }

        foreach (['title', 'name', 'clientName', 'applicantName', 'email', 'subject', 'slug'] as $attribute) {
            $value = $subject->getAttribute($attribute);

            if (is_array($value)) {
                $value = $value[app()->getLocale()] ?? $value['en'] ?? collect($value)->first();
            }

            if (filled($value)) {
                return "{$type}: {$value}";
            }
        }

        return $type;
    }
}
