<?php

namespace App\Filament\Support;

use App\Services\AIGeneratorService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
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
                    Notification::make()
                        ->danger()
                        ->title(__('AI Generation Failed'))
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }
}
