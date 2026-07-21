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
            ->icon('heroicon-m-language')
            ->tooltip(__('Translate between English and Khmer'))
            ->action(function (Get $get, Set $set, $state, $record, Component $livewire) use ($sourceField, $targetField, $targetLocale) {
                $sourceText = $state;

                $sourceHasKhmer = self::containsKhmer((string) $sourceText);
                $resolvedTargetLocale = $sourceHasKhmer ? 'en' : $targetLocale;
                $sourceLocale = $sourceHasKhmer ? 'km' : 'en';

                // If the current field is empty, try to get the English value from the record or form
                if (empty($sourceText) && $record) {
                    $sourceText = $record->getTranslation($sourceField, $sourceLocale, false);
                    $sourceHasKhmer = self::containsKhmer((string) $sourceText);
                    $resolvedTargetLocale = $sourceHasKhmer ? 'en' : $targetLocale;
                    $sourceLocale = $sourceHasKhmer ? 'km' : 'en';
                }

                // For new records, get the unsaved English text from the otherLocaleData array
                if (empty($sourceText) && property_exists($livewire, 'otherLocaleData')) {
                    $sourceText = $livewire->otherLocaleData[$sourceLocale][$sourceField] ?? '';
                    $sourceHasKhmer = self::containsKhmer((string) $sourceText);
                    $resolvedTargetLocale = $sourceHasKhmer ? 'en' : $targetLocale;
                    $sourceLocale = $sourceHasKhmer ? 'km' : 'en';
                }

                if (empty($sourceText)) {
                    Notification::make()
                        ->warning()
                        ->title(__('No source text found'))
                        ->body(__('Please enter English text first or save the record.'))
                        ->send();

                    return;
                }

                $translator = app(AutoTranslateService::class);
                $translated = $translator->translateFrom($sourceText, $resolvedTargetLocale, $sourceLocale);
                $translated ??= self::localFallback($sourceText, $resolvedTargetLocale);

                if ($translated) {
                    $activeLocale = property_exists($livewire, 'activeLocale') && $livewire->activeLocale
                        ? $livewire->activeLocale
                        : $resolvedTargetLocale;

                    if ($resolvedTargetLocale === $activeLocale) {
                        $set($targetField, $translated);
                    } elseif (property_exists($livewire, 'otherLocaleData')) {
                        $livewire->otherLocaleData[$resolvedTargetLocale][$targetField] = $translated;

                        if (method_exists($livewire, 'setActiveLocale')) {
                            $livewire->setActiveLocale($resolvedTargetLocale);
                        }
                    } else {
                        $set($targetField, $translated);
                    }

                    Notification::make()
                        ->success()
                        ->title(__('Translated successfully'))
                        ->body($resolvedTargetLocale === 'en' ? __('English field updated.') : __('Khmer field updated.'))
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

    protected static function containsKhmer(string $text): bool
    {
        return preg_match('/[\x{1780}-\x{17FF}]/u', $text) === 1;
    }
}
