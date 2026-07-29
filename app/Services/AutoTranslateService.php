<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\GoogleTranslate;

class AutoTranslateService
{
    protected GoogleTranslate $translator;

    public function __construct(?GoogleTranslate $translator = null)
    {
        $this->translator = $translator ?? new GoogleTranslate;
        $this->translator->setSource('en');
        $this->translator->setTarget('km');
    }

    /**
     * Translate a single string from English to target language.
     */
    public function translate(string $text, string $targetLocale = 'km'): ?string
    {
        return $this->translateFrom($text, $targetLocale, 'en');
    }

    public function translateFrom(string $text, string $targetLocale = 'km', ?string $sourceLocale = null): ?string
    {
        if (empty(trim(strip_tags($text)))) {
            return $text;
        }

        try {
            $this->translator->setSource($sourceLocale);
            $this->translator->setTarget($targetLocale);

            // Handle HTML content (from RichEditor)
            // Improved detection: if it contains '<' and '>', treat as HTML
            if (preg_match('/<[^>]*>/', $text)) {
                return $this->translateHtml($text, $targetLocale);
            }

            return $this->translator->translate($text);
        } catch (\Throwable $e) {
            Log::warning('AutoTranslateService skipped translation: '.$e->getMessage(), [
                'text' => $text,
                'target' => $targetLocale,
            ]);

            return null;
        }
    }

    /**
     * Translate HTML content while preserving tags.
     */
    protected function translateHtml(string $html, string $targetLocale): string
    {
        $this->translator->setTarget($targetLocale);

        $previousLibxmlState = libxml_use_internal_errors(true);

        try {
            $document = new \DOMDocument('1.0', 'UTF-8');
            $document->loadHTML(
                '<?xml encoding="UTF-8"><div id="translation-root">'.$html.'</div>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
            );

            $root = $document->getElementById('translation-root');

            if (! $root) {
                return $html;
            }

            $this->translateTextNodes($root);

            $translatedHtml = '';
            foreach ($root->childNodes as $child) {
                $translatedHtml .= $document->saveHTML($child);
            }

            return $translatedHtml;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousLibxmlState);
        }
    }

    public function containsHtml(string $content): bool
    {
        return preg_match('/<\/?[a-z][^>]*>/i', $content) === 1;
    }

    private function translateTextNodes(\DOMNode $node): void
    {
        foreach ($node->childNodes as $child) {
            if ($child instanceof \DOMText) {
                $child->nodeValue = $this->translateTextNode($child->nodeValue);

                continue;
            }

            if ($child instanceof \DOMElement && in_array(strtolower($child->tagName), ['script', 'style'], true)) {
                continue;
            }

            $this->translateTextNodes($child);
        }
    }

    private function translateTextNode(string $text): string
    {
        if (trim($text) === '') {
            return $text;
        }

        preg_match('/^(\s*)(.*?)(\s*)$/us', $text, $matches);

        return ($matches[1] ?? '').$this->translator->translate($matches[2] ?? $text).($matches[3] ?? '');
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

            if (! is_string($value) || empty(trim($value))) {
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
