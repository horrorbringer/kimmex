<?php

namespace App\Filament\Support;

use App\Services\AutoTranslateService;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;

class TranslationHelper
{
    /**
     * Returns a Filament Action that can be added as a suffixAction to translatable fields.
     */
    public static function getAutoTranslateAction(string $fieldName): Action
    {
        return Action::make('autoTranslate')
            ->icon('heroicon-m-language')
            ->tooltip(__('Translate from English to Khmer'))
            ->action(function (Get $get, Set $set, $state, $record) use ($fieldName) {
                $sourceText = $state;

                // If the current field is empty, try to get the English value from the record or form
                if (empty($sourceText) && $record) {
                    $sourceText = $record->getTranslation($fieldName, 'en');
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
                $translated = $translator->translate($sourceText, 'km');

                if ($translated) {
                    $set($fieldName, $translated);
                    
                    Notification::make()
                        ->success()
                        ->title(__('Translated successfully'))
                        ->send();
                } else {
                    Notification::make()
                        ->error()
                        ->title(__('Translation failed'))
                        ->body(__('Check your internet connection or API limits.'))
                        ->send();
                }
            });
    }
}
