<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class LatestInquiriesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Inquiry::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('Customer Name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->limit(30),
                TextColumn::make('attachment_url')
                    ->label(__('Attachment'))
                    ->formatStateUsing(fn ($state) => $state ? __('View File') : __('No File'))
                    ->icon(fn ($state) => $state ? 'heroicon-o-paper-clip' : null)
                    ->color(fn ($state) => $state ? 'primary' : 'gray')
                    ->url(fn ($record) => $record->attachment_url ? \Illuminate\Support\Facades\Storage::url($record->attachment_url) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'NEW' => 'info',
                        'READ' => 'warning',
                        'REPLIED' => 'success',
                        'CLOSED' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label(__('View'))
                    ->url(fn (Inquiry $record): string => \App\Filament\Resources\Inquiries\InquiryResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
