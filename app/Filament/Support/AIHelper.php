<?php

namespace App\Filament\Support;

use App\Services\AIGeneratorService;
use App\Services\AutoTranslateService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class AIHelper
{
    /**
     * Returns a Filament Action that improves the current field content using AI.
     */
    public static function getImproveAction(string $fieldName, string $defaultPrompt = 'Improve this content to be more professional and engaging.'): Action
    {
        return Action::make('aiImprove_' . $fieldName)
            ->label(__('Improve'))
            ->icon('heroicon-m-sparkles')
            ->tooltip(__('Improve with AI'))
            ->form([
                \Filament\Forms\Components\Textarea::make('suggestion')
                    ->label(__('Specific Suggestion (Optional)'))
                    ->placeholder(__('e.g. Make it shorter, focus on our team, or sound more formal...'))
                    ->rows(2),
            ])
            ->action(function ($state, Set $set, array $data, AIGeneratorService $ai) use ($fieldName, $defaultPrompt) {
                if (empty($state)) {
                    Notification::make()
                        ->warning()
                        ->title(__('Field is empty'))
                        ->body(__('Please enter some text first so the AI can improve it.'))
                        ->send();
                    return;
                }

                try {
                    $customPrompt = !empty($data['suggestion']) 
                        ? "Improve this content following this suggestion: " . $data['suggestion'] 
                        : $defaultPrompt;

                    $improved = $ai->improveContent($state, $customPrompt);
                    
                    if ($improved) {
                        $set($fieldName, $improved);
                        Notification::make()
                            ->success()
                            ->title(__('Content Improved'))
                            ->send();
                    }
                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('AI Improvement Failed'))
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }

    /**
     * Returns a Filament Action that generates new content based on a topic.
     */
    public static function getGenerateAction(string $fieldName, string $type = 'article'): Action
    {
        return Action::make('aiGenerate_' . $fieldName)
            ->label(__('Generate with AI'))
            ->icon('heroicon-o-sparkles')
            ->form([
                \Filament\Forms\Components\TextInput::make('topic')
                    ->label(__('What should this be about?'))
                    ->placeholder(__('e.g. Benefits of choosing Kimmex for your next project'))
                    ->required(),
                \Filament\Forms\Components\Textarea::make('instructions')
                    ->label(__('Specific Instructions (Optional)'))
                    ->placeholder(__('e.g. Use a friendly tone, mention our 10 years experience'))
                    ->rows(2),
            ])
            ->action(function (Set $set, array $data, AIGeneratorService $ai) use ($fieldName, $type) {
                try {
                    $content = $ai->generateContent($data['topic'], $type, $data['instructions'] ?? null);
                    
                    if ($content) {
                        $set($fieldName, $content);
                        Notification::make()
                            ->success()
                            ->title(__('Content Generated'))
                            ->send();
                    }
                } catch (\Exception $e) {
                    $fallback = self::buildFallbackContent($data['topic'], $type, $data['instructions'] ?? null);

                    if ($fallback) {
                        $set($fieldName, $fallback);

                        Notification::make()
                            ->warning()
                            ->title(__('AI quota unavailable'))
                            ->body(__('Used a local draft instead. You can edit it before saving.'))
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->danger()
                        ->title(__('AI Generation Failed'))
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }

    protected static function buildFallbackContent(string $topic, string $type, ?string $instructions = null): ?string
    {
        $topic = trim($topic);

        if ($topic === '') {
            return null;
        }

        $instructionSentence = $instructions
            ? ' ' . trim($instructions)
            : '';

        if (str_contains(strtolower($type), 'project category')) {
            return "{$topic} projects cover planning, construction, and delivery work for clients who need reliable execution, clear coordination, and durable results. This category highlights Kimmex experience in managing project requirements, site conditions, quality control, and practical construction solutions.{$instructionSentence}";
        }

        return "{$topic} focuses on practical value, clear execution, and professional delivery. This content can be refined further to match the final message, audience, and tone.{$instructionSentence}";
    }

    public static function getTranslateAction(
        string $sourceField,
        ?string $targetField = null,
        string $targetLanguage = 'Khmer',
        string $targetLocale = 'km',
        ?string $sourceLocale = null
    ): Action {
        $targetField ??= $sourceField;

        return Action::make('aiTranslate_' . $sourceField . '_' . $targetField)
            ->label(__('AI Translate'))
            ->icon('heroicon-m-language')
            ->tooltip(__('Translate with AI'))
            ->action(function (Get $get, Set $set, $state, AIGeneratorService $ai, AutoTranslateService $translator) use ($sourceField, $targetField, $targetLanguage, $targetLocale, $sourceLocale) {
                $sourceText = $state ?: $get($sourceField);
                $sourceHasKhmer = self::containsKhmer((string) $sourceText);
                $resolvedTargetLanguage = $sourceHasKhmer ? 'English' : $targetLanguage;
                $resolvedTargetLocale = $sourceHasKhmer ? 'en' : $targetLocale;
                $resolvedSourceLocale = $sourceHasKhmer ? 'km' : ($sourceLocale ?? 'en');

                if (empty(trim(strip_tags((string) $sourceText)))) {
                    Notification::make()
                        ->warning()
                        ->title(__('No source text found'))
                        ->body(__('Please enter source text first.'))
                        ->send();
                    return;
                }

                try {
                    $translated = $ai->translateContent($sourceText, $resolvedTargetLanguage);

                    if ($translated) {
                        $set($targetField, $translated);

                        Notification::make()
                            ->success()
                            ->title(__('Translated successfully'))
                            ->send();
                    }
                } catch (\Exception $e) {
                    $fallback = $translator->translateFrom($sourceText, $resolvedTargetLocale, $resolvedSourceLocale);
                    $fallback ??= self::buildLocalTranslationFallback($sourceText, $resolvedTargetLocale);

                    if ($fallback) {
                        $set($targetField, $fallback);

                        Notification::make()
                            ->warning()
                            ->title(__('AI quota unavailable'))
                            ->body(__('Used automatic translation fallback instead.'))
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->danger()
                        ->title(__('AI Translation Failed'))
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }

    protected static function buildLocalTranslationFallback(string $sourceText, string $targetLocale): ?string
    {
        if ($targetLocale !== 'en') {
            return null;
        }

        $dictionary = [
            'រដ្ឋាភិបាល' => 'Government',
            'ហេដ្ឋារចនាសម្ព័ន្ធ' => 'Infrastructure',
            'ពាណិជ្ជកម្ម' => 'Commercial',
            'អប់រំ' => 'Education',
            'ថាមពល និងប្រព័ន្ធសាធារណៈ' => 'Energy & Utilities',
            'ថាមពល' => 'Energy',
            'ប្រព័ន្ធសាធារណៈ' => 'Utilities',
            'ប្រព័ន្ធសម្អាតទឹក' => 'Water Treatment',
            'គម្រោង' => 'Project',
            'សំណង់' => 'Construction',
        ];

        try {
            foreach (\App\Models\ProjectCategory::query()->get() as $category) {
                $english = $category->getTranslation('name', 'en', false);
                $khmer = $category->getTranslation('name', 'km', false)
                    ?: $category->getTranslation('name', 'kh', false);

                if ($english && $khmer) {
                    $dictionary[$khmer] = $english;
                }
            }
        } catch (\Throwable) {
            //
        }

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
