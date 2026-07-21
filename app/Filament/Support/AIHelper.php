<?php

namespace App\Filament\Support;

use App\Models\ProjectCategory;
use App\Services\AIGeneratorService;
use App\Services\AutoTranslateService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class AIHelper
{
    /**
     * Returns a Filament Action that improves the current field content using AI.
     */
    public static function getImproveAction(string $fieldName, string $defaultPrompt = 'Improve this content to be more professional and engaging.'): Action
    {
        return Action::make('aiImprove_'.$fieldName)
            ->label(__('Improve'))
            ->icon('heroicon-m-sparkles')
            ->tooltip(__('Improve with AI'))
            ->form([
                Textarea::make('suggestion')
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
                    $customPrompt = ! empty($data['suggestion'])
                        ? 'Improve this content following this suggestion: '.$data['suggestion']
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
        return Action::make('aiGenerate_'.$fieldName)
            ->label(__('Generate with AI'))
            ->icon('heroicon-o-sparkles')
            ->form([
                TextInput::make('topic')
                    ->label(__('What should this be about?'))
                    ->placeholder(__('e.g. Benefits of choosing Kimmex for your next project'))
                    ->required(),
                Textarea::make('instructions')
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
            ? ' '.trim($instructions)
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

        return Action::make('aiTranslate_'.$sourceField.'_'.$targetField)
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
            foreach (ProjectCategory::query()->get() as $category) {
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

    /**
     * Returns a Filament Action that auto-fills multiple fields at once using AI.
     * Place this as a header action on Create/Edit pages.
     */
    public static function getAutoFillAction(string $type = 'news'): Action
    {
        $prompts = match ($type) {
            'news' => [
                'label' => __('AI Auto-Fill'),
                'description' => __('Generate excerpt, tags, category, and SEO from your title and content.'),
                'fields' => ['excerpt', 'tags', 'category', 'metaTitle', 'metaDescription'],
            ],
            'job' => [
                'label' => __('AI Generate Job Details'),
                'description' => __('Generate responsibilities, requirements, and benefits from the title and summary.'),
                'fields' => ['responsibilities', 'requirements', 'benefits'],
            ],
            'project' => [
                'label' => __('AI Auto-Fill'),
                'description' => __('Generate description, highlights, and SEO from the project title.'),
                'fields' => ['description', 'metaTitle', 'metaDescription'],
            ],
            default => [
                'label' => __('AI Auto-Fill'),
                'description' => __('Auto-generate content from existing fields.'),
                'fields' => [],
            ],
        };

        return Action::make('aiAutoFill')
            ->label($prompts['label'])
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading($prompts['label'])
            ->modalDescription($prompts['description'].' '.__('This will overwrite empty fields only.'))
            ->modalSubmitActionLabel(__('Generate'))
            ->action(function (Get $get, Set $set, AIGeneratorService $ai) use ($type) {
                $title = $get('title') ?? '';

                if (empty(trim(strip_tags($title)))) {
                    Notification::make()
                        ->warning()
                        ->title(__('Enter a title first'))
                        ->body(__('AI needs at least a title to generate content.'))
                        ->send();

                    return;
                }

                $filled = 0;

                try {
                    if ($type === 'news') {
                        $content = strip_tags($get('content') ?? '');
                        $context = $title.($content ? '. Content: '.Str::limit($content, 500) : '');

                        // Generate excerpt if empty
                        if (empty(trim($get('excerpt') ?? ''))) {
                            $excerpt = $ai->generateContent($context, 'text', 'Write a 1-2 sentence engaging excerpt/summary for this news article. Max 160 characters. No quotes.');
                            if ($excerpt) {
                                $set('excerpt', trim(strip_tags($excerpt)));
                                $filled++;
                            }
                        }

                        // Generate category if empty
                        if (empty(trim($get('category') ?? ''))) {
                            $category = $ai->generateContent($title, 'text', 'Suggest ONE short category word for this article (e.g. Construction, Project Update, Company News, CSR, Infrastructure). Just the category name, nothing else.');
                            if ($category) {
                                $set('category', trim(strip_tags($category)));
                                $filled++;
                            }
                        }

                        // Generate tags if empty
                        $currentTags = $get('tags');
                        if (empty($currentTags)) {
                            $tagsStr = $ai->generateContent($context, 'text', 'Generate 3-5 relevant tags for this article. Return ONLY comma-separated lowercase tags. Example: construction, phnom penh, infrastructure');
                            if ($tagsStr) {
                                $tags = array_map('trim', explode(',', strip_tags($tagsStr)));
                                $set('tags', $tags);
                                $filled++;
                            }
                        }

                        // Generate SEO
                        if (empty(trim($get('metaTitle') ?? ''))) {
                            $set('metaTitle', Str::limit($title, 60));
                            $filled++;
                        }
                        if (empty(trim($get('metaDescription') ?? ''))) {
                            $meta = $ai->generateContent($context, 'text', 'Write a concise SEO meta description for this article. Max 155 characters. No quotes.');
                            if ($meta) {
                                $set('metaDescription', Str::limit(trim(strip_tags($meta)), 155));
                                $filled++;
                            }
                        }

                    } elseif ($type === 'job') {
                        $summary = strip_tags($get('summary') ?? '');
                        $context = "Job title: {$title}. ".($summary ? "Summary: {$summary}" : 'For a construction/engineering company in Cambodia.');

                        if (empty(trim(strip_tags($get('responsibilities') ?? '')))) {
                            $resp = $ai->generateContent($context, 'text', 'Write 5-7 key responsibilities for this job position as an HTML unordered list (<ul><li>). Be specific to the construction industry.');
                            if ($resp) {
                                $set('responsibilities', $resp);
                                $filled++;
                            }
                        }

                        if (empty(trim(strip_tags($get('requirements') ?? '')))) {
                            $reqs = $ai->generateContent($context, 'text', 'Write 5-7 requirements/qualifications for this job as an HTML unordered list (<ul><li>). Include education, experience, and skills relevant to construction.');
                            if ($reqs) {
                                $set('requirements', $reqs);
                                $filled++;
                            }
                        }

                        if (empty(trim(strip_tags($get('benefits') ?? '')))) {
                            $bens = $ai->generateContent($context, 'text', 'Write 4-6 employee benefits for this position as an HTML unordered list (<ul><li>). Include standard benefits for a professional construction company.');
                            if ($bens) {
                                $set('benefits', $bens);
                                $filled++;
                            }
                        }

                    } elseif ($type === 'project') {
                        if (empty(trim(strip_tags($get('description') ?? '')))) {
                            $desc = $ai->generateContent($title, 'text', 'Write a 2-3 sentence professional project description for a construction/engineering project. Be concise.');
                            if ($desc) {
                                $set('description', $desc);
                                $filled++;
                            }
                        }

                        if (empty(trim($get('metaTitle') ?? ''))) {
                            $set('metaTitle', Str::limit($title, 60));
                            $filled++;
                        }
                        if (empty(trim($get('metaDescription') ?? ''))) {
                            $meta = $ai->generateContent($title, 'text', 'Write a concise SEO meta description for this construction project. Max 155 characters.');
                            if ($meta) {
                                $set('metaDescription', Str::limit(trim(strip_tags($meta)), 155));
                                $filled++;
                            }
                        }
                    }

                    if ($filled > 0) {
                        Notification::make()
                            ->success()
                            ->title(__(':count fields auto-filled!', ['count' => $filled]))
                            ->send();
                    } else {
                        Notification::make()
                            ->info()
                            ->title(__('All fields already filled'))
                            ->body(__('Nothing to generate — fields already have content.'))
                            ->send();
                    }
                } catch (\Exception $e) {
                    // Fallback: generate basic content locally without AI
                    $filled = self::localAutoFill($type, $get, $set);

                    if ($filled > 0) {
                        Notification::make()
                            ->warning()
                            ->title(__(':count fields auto-filled (no AI)', ['count' => $filled]))
                            ->body(__('AI is not configured. Used basic auto-generation instead. You can edit the content.'))
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title(__('Auto-Fill Failed'))
                            ->body(__('AI is not configured and all fields already have content.'))
                            ->send();
                    }
                }
            });
    }

    /**
     * Local fallback auto-fill when AI is not available.
     */
    protected static function localAutoFill(string $type, Get $get, Set $set): int
    {
        $title = trim(strip_tags($get('title') ?? ''));
        $filled = 0;

        if ($type === 'news') {
            $content = strip_tags($get('content') ?? '');

            if (empty(trim($get('excerpt') ?? '')) && $content) {
                $set('excerpt', Str::limit($content, 160));
                $filled++;
            }

            if (empty(trim($get('category') ?? ''))) {
                $set('category', 'Company News');
                $filled++;
            }

            $currentTags = $get('tags');
            if (empty($currentTags) && $title) {
                $words = array_filter(explode(' ', strtolower($title)), fn ($w) => strlen($w) > 3);
                $set('tags', array_slice(array_values($words), 0, 4));
                $filled++;
            }

            if (empty(trim($get('metaTitle') ?? ''))) {
                $set('metaTitle', Str::limit($title, 60));
                $filled++;
            }

            if (empty(trim($get('metaDescription') ?? ''))) {
                $desc = $content ? Str::limit($content, 155) : $title;
                $set('metaDescription', $desc);
                $filled++;
            }

        } elseif ($type === 'job') {
            if (empty(trim(strip_tags($get('responsibilities') ?? '')))) {
                $set('responsibilities', '<ul><li>Manage daily operations and tasks related to '.$title.'</li><li>Coordinate with team members and stakeholders</li><li>Ensure quality standards and safety compliance</li><li>Report progress and issues to management</li><li>Maintain documentation and records</li></ul>');
                $filled++;
            }

            if (empty(trim(strip_tags($get('requirements') ?? '')))) {
                $set('requirements', '<ul><li>Relevant degree or equivalent experience</li><li>2+ years experience in a similar role</li><li>Strong communication and teamwork skills</li><li>Proficiency in relevant software and tools</li><li>Problem-solving mindset</li></ul>');
                $filled++;
            }

            if (empty(trim(strip_tags($get('benefits') ?? '')))) {
                $set('benefits', '<ul><li>Competitive salary package</li><li>Health insurance coverage</li><li>Professional development opportunities</li><li>Friendly and supportive work environment</li><li>Performance bonuses</li></ul>');
                $filled++;
            }

        } elseif ($type === 'project') {
            if (empty(trim(strip_tags($get('description') ?? '')))) {
                $set('description', $title.' is a professional construction project delivered by Kimmex Construction & Investment Co., Ltd. with commitment to quality, safety, and timely completion.');
                $filled++;
            }

            if (empty(trim($get('metaTitle') ?? ''))) {
                $set('metaTitle', Str::limit($title.' | Kimmex', 60));
                $filled++;
            }

            if (empty(trim($get('metaDescription') ?? ''))) {
                $set('metaDescription', Str::limit($title.' - A construction project by Kimmex Construction & Investment.', 155));
                $filled++;
            }
        }

        return $filled;
    }

    protected static function containsKhmer(string $text): bool
    {
        return preg_match('/[\x{1780}-\x{17FF}]/u', $text) === 1;
    }
}
