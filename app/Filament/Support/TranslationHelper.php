<?php

namespace App\Filament\Support;

use App\Services\AutoTranslateService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Livewire\Component;

class TranslationHelper
{
    /**
     * Returns a Filament Action that can be added as a suffixAction to translatable fields.
     */
    public static function getAutoTranslateAction(
        string $sourceField,
        ?string $targetField = null,
        string $targetLocale = 'km'
    ): Action {
        $targetField ??= $sourceField;

        return Action::make('autoTranslate_'.$sourceField.'_'.$targetField)
            ->label(fn (Component $livewire): string => self::activeLocale($livewire) === 'km' ? __('To EN') : __('To KH'))
            ->icon('heroicon-m-language')
            ->tooltip(fn (Component $livewire): string => self::activeLocale($livewire) === 'km' ? __('Translate to English') : __('Translate to Khmer'))
            ->action(function (Get $get, Set $set, $state, $record, Component $livewire) use ($sourceField, $targetLocale) {
                $sourceText = $get($sourceField) ?? $state;
                $sourceLocale = self::activeLocale($livewire, (string) $sourceText);
                $resolvedTargetLocale = $sourceLocale === 'km' ? 'en' : $targetLocale;

                $sourceData = self::localeData($livewire, $record, $sourceLocale);
                $sourceData[$sourceField] = $sourceText;

                if (! self::hasTranslatableContent($sourceData)) {
                    Notification::make()
                        ->warning()
                        ->title(__('No source text found'))
                        ->body(__('Please enter English text first or save the record.'))
                        ->send();

                    return;
                }

                $translator = app(AutoTranslateService::class);
                $translatedData = self::translateLocaleData($translator, $sourceData, $resolvedTargetLocale, $sourceLocale);

                if ($translatedData !== []) {
                    $activeLocale = property_exists($livewire, 'activeLocale') && $livewire->activeLocale
                        ? $livewire->activeLocale
                        : $resolvedTargetLocale;

                    if ($resolvedTargetLocale === $activeLocale) {
                        foreach ($translatedData as $field => $translated) {
                            $set($field, $translated);
                        }
                    } elseif (property_exists($livewire, 'otherLocaleData')) {
                        $livewire->otherLocaleData[$resolvedTargetLocale] = [
                            ...self::recordLocaleData($record, $resolvedTargetLocale),
                            ...($livewire->otherLocaleData[$resolvedTargetLocale] ?? []),
                            ...$translatedData,
                        ];

                        if (method_exists($livewire, 'setActiveLocale')) {
                            $livewire->setActiveLocale($resolvedTargetLocale);
                        }
                    } else {
                        foreach ($translatedData as $field => $translated) {
                            $set($field, $translated);
                        }
                    }

                    Notification::make()
                        ->success()
                        ->title(__('Translated successfully'))
                        ->body($resolvedTargetLocale === 'en' ? __('All English fields are ready.') : __('All Khmer fields are ready.'))
                        ->send();
                } else {
                    Notification::make()
                        ->danger()
                        ->title(__('Translation failed'))
                        ->body(__('Check your internet connection or API limits.'))
                        ->send();
                }
            });
    }

    protected static function localFallback(string $sourceText, string $targetLocale): ?string
    {
        if ($targetLocale !== 'en') {
            return null;
        }

        $dictionary = [
            'រដ្ឋាភិបាល' => 'Government',
            'ហេដ្ឋារចនាសម្ព័ន្ធ' => 'Infrastructure',
            'ពាណិជ្ជកម្ម' => 'Commercial',
            'អប់រំ' => 'Education',
            'គម្រោង' => 'Project',
            'សំណង់' => 'Construction',
            'ការសាងសង់' => 'Construction',
            'អគារ' => 'Building',
            'ការិយាល័យ' => 'Office',
            'កម្ពស់' => 'Height',
            'ជាន់' => 'Floor',
            'ក្រសួង' => 'Ministry',
            'សេដ្ឋកិច្ច' => 'Economy',
        ];

        $translated = str_replace(array_keys($dictionary), array_values($dictionary), $sourceText);
        $translated = preg_replace('/\s+/', ' ', trim($translated));
        $translated = preg_replace('/\b([A-Za-z][A-Za-z& ]+)\s+\1\b/i', '$1', $translated);

        return $translated !== trim($sourceText) ? $translated : null;
    }

    protected static function activeLocale(Component $livewire, string $fallbackText = ''): string
    {
        if (property_exists($livewire, 'activeLocale') && in_array($livewire->activeLocale, ['en', 'km'], true)) {
            return $livewire->activeLocale;
        }

        return preg_match('/[\x{1780}-\x{17FF}]/u', $fallbackText) === 1 ? 'km' : 'en';
    }

    /**
     * @return array<string, mixed>
     */
    protected static function recordLocaleData(mixed $record, string $locale): array
    {
        if (! is_object($record) || ! method_exists($record, 'getTranslatableAttributes') || ! method_exists($record, 'getTranslation')) {
            return [];
        }

        $data = [];

        foreach ($record->getTranslatableAttributes() as $attribute) {
            $data[$attribute] = $record->getTranslation($attribute, $locale, false);
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    protected static function localeData(Component $livewire, mixed $record, string $locale): array
    {
        $data = self::recordLocaleData($record, $locale);

        try {
            $formData = $livewire->form->getState();
            $fields = array_keys($data);

            if ($fields === [] && method_exists($livewire, 'getResource')) {
                $resource = $livewire::getResource();
                $fields = $resource::getTranslatableAttributes();
            }

            foreach ($fields as $field) {
                if (array_key_exists($field, $formData)) {
                    $data[$field] = $formData[$field];
                }
            }
        } catch (\Throwable) {
            // The current editor value is still translated below.
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected static function hasTranslatableContent(array $data): bool
    {
        foreach ($data as $value) {
            if (is_string($value) && filled(strip_tags($value))) {
                return true;
            }

            if (is_array($value) && $value !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected static function translateLocaleData(AutoTranslateService $translator, array $data, string $targetLocale, string $sourceLocale): array
    {
        $translatedData = [];

        foreach ($data as $field => $value) {
            if (is_array($value)) {
                $translatedData[$field] = $translator->translateArray($value, [], $targetLocale);

                continue;
            }

            if (! is_string($value)) {
                continue;
            }

            $translated = $translator->translateFrom($value, $targetLocale, $sourceLocale);
            $translated ??= self::localFallback($value, $targetLocale);

            if ($translated !== null) {
                $translatedData[$field] = $translated;
            }
        }

        return $translatedData;
    }
}
