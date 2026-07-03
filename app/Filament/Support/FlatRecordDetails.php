<?php

namespace App\Filament\Support;

use App\Support\PublicStorage;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class FlatRecordDetails
{
    protected const HIDDEN_ATTRIBUTES = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    public static function schema(Model $record): array
    {
        return [
            Section::make(__('Details'))
                ->columns([
                    'default' => 1,
                    'md' => 2,
                ])
                ->schema(self::entries($record)),
        ];
    }

    protected static function entries(Model $record): array
    {
        return collect($record->getAttributes())
            ->reject(fn (mixed $value, string $attribute): bool => in_array($attribute, self::HIDDEN_ATTRIBUTES, true))
            ->map(fn (mixed $value, string $attribute) => self::entry($record, $attribute))
            ->values()
            ->all();
    }

    protected static function entry(Model $record, string $attribute): TextEntry|IconEntry|ImageEntry|KeyValueEntry
    {
        $label = __(Str::headline($attribute));

        if (self::isImageAttribute($attribute)) {
            return ImageEntry::make($attribute)
                ->label($label)
                ->state(fn (Model $record): ?string => PublicStorage::urlIfExists($record->getAttribute($attribute)))
                ->checkFileExistence(false)
                ->imageHeight(120)
                ->placeholder(__('No image'));
        }

        if (self::isBooleanAttribute($record, $attribute)) {
            return IconEntry::make($attribute)
                ->label($label)
                ->boolean()
                ->state(fn (Model $record): ?bool => self::booleanState($record->getAttribute($attribute)));
        }

        if (self::isStructuredAttribute($record->getAttribute($attribute))) {
            return KeyValueEntry::make($attribute)
                ->label($label)
                ->state(fn (Model $record): array => self::structuredState($record->getAttribute($attribute)))
                ->columnSpanFull();
        }

        return TextEntry::make($attribute)
            ->label($label)
            ->state(fn (Model $record): string => self::textState($record->getAttribute($attribute)))
            ->placeholder('-')
            ->wrap()
            ->columnSpan(fn (): int|string|null => self::isLongTextAttribute($attribute) ? 'full' : null);
    }

    protected static function isImageAttribute(string $attribute): bool
    {
        return Str::contains(Str::lower($attribute), ['image', 'photo', 'logo', 'avatar', 'thumbnail']);
    }

    protected static function isBooleanAttribute(Model $record, string $attribute): bool
    {
        $value = $record->getAttribute($attribute);

        if (is_bool($value)) {
            return true;
        }

        return Str::startsWith($attribute, ['is_', 'has_', 'can_'])
            || Str::startsWith(Str::lower($attribute), ['is', 'has', 'can']);
    }

    protected static function booleanState(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
    }

    protected static function isStructuredAttribute(mixed $value): bool
    {
        return is_array($value) || is_object($value);
    }

    protected static function structuredState(mixed $value): array
    {
        $value = json_decode(json_encode($value), true) ?: [];

        return collect($value)
            ->mapWithKeys(fn (mixed $item, string|int $key): array => [
                (string) $key => is_scalar($item) || $item === null
                    ? self::textState($item)
                    : json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ])
            ->all();
    }

    protected static function textState(mixed $value): string
    {
        if ($value instanceof Carbon) {
            return $value->format('M j, Y H:i');
        }

        if ($value instanceof \BackedEnum) {
            return Str::headline((string) $value->value);
        }

        if ($value instanceof \UnitEnum) {
            return Str::headline($value->name);
        }

        if (is_bool($value)) {
            return $value ? __('Yes') : __('No');
        }

        if ($value === null || $value === '') {
            return '';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        }

        return trim(strip_tags((string) $value));
    }

    protected static function isLongTextAttribute(string $attribute): bool
    {
        return Str::contains(Str::lower($attribute), [
            'content',
            'description',
            'message',
            'body',
            'summary',
            'excerpt',
            'bio',
            'notes',
            'letter',
            'metadata',
        ]);
    }
}
