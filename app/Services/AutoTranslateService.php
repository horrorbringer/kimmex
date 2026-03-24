<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;

class AutoTranslateService
{
    protected GoogleTranslate $translator;

    public function __construct()
    {
        $this->translator = new GoogleTranslate();
        $this->translator->setSource('en');
        $this->translator->setTarget('km');
    }

    /**
     * Translate a single string from English to target language.
     */
    public function translate(string $text, string $targetLocale = 'km'): ?string
    {
        if (empty(trim(strip_tags($text)))) {
            return $text;
        }

        try {
            $this->translator->setTarget($targetLocale);

            // Handle HTML content (from RichEditor)
            // Improved detection: if it contains '<' and '>', treat as HTML
            if (preg_match('/<[^>]*>/', $text)) {
                return $this->translateHtml($text, $targetLocale);
            }

            return $this->translator->translate($text);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("AutoTranslateService Failure: " . $e->getMessage(), [
                'text' => $text,
                'target' => $targetLocale
            ]);
            return null;
        }
    }

    /**
     * Translate HTML content while preserving tags.
     */
    protected function translateHtml(string $html, string $targetLocale): string
    {
        // Extract text nodes, translate, and reconstruct
        $this->translator->setTarget($targetLocale);

        // Simple approach: translate the whole HTML (Google handles tags well)
        return $this->translator->translate($html);
    }

    /**
     * Translate an array recursively.
     * Useful for Filament Repeater and Builder fields.
     */
    public function translateArray(array $data, array $skipKeys = [], string $targetLocale = 'km'): array
    {
        $translated = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $skipKeys)) {
                $translated[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $translated[$key] = $this->translateArray($value, $skipKeys, $targetLocale);
                continue;
            }

            if (!is_string($value) || empty(trim($value))) {
                $translated[$key] = $value;
                continue;
            }

            $result = $this->translate($value, $targetLocale);
            $translated[$key] = $result ?? $value;
        }

        return $translated;
    }

    /**
     * Auto-translate translatable fields on a Spatie Translatable model.
     * Translates from the source locale to all other configured locales.
     */
    public function translateModel($model, string $sourceLocale = 'en', array $targetLocales = ['km']): void
    {
        foreach ($model->getTranslatableAttributes() as $attribute) {
            $sourceText = $model->getTranslation($attribute, $sourceLocale);

            if (empty($sourceText)) {
                continue;
            }

            foreach ($targetLocales as $locale) {
                $existing = $model->getTranslation($attribute, $locale);

                // Only auto-translate if the target locale is empty
                if (empty($existing)) {
                    $translated = $this->translate($sourceText, $locale);
                    if ($translated) {
                        $model->setTranslation($attribute, $locale, $translated);
                    }
                }
            }
        }
    }
}
