<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $lang = app()->getLocale();
            $settings = \Illuminate\Support\Facades\Cache::remember('global_settings_' . $lang, now()->addHours(12), function () use ($lang) {
                return [
                    'profile' => \App\Models\SystemSetting::get('organization_profile', []),
                    'brand' => \App\Models\SystemSetting::get('brand_identity', [])[$lang] 
                               ?? \App\Models\SystemSetting::get('brand_identity', [])['en'] 
                               ?? [],
                    'theme' => \App\Models\SystemSetting::get('theme_settings', []),
                    'integrations' => \App\Models\SystemSetting::get('integration_settings', []),
                ];
            });

            $view->with('globalSettings', $settings);
            $view->with('siteLocale', $lang);
            $view->with('hasPublicDocuments', \App\Models\Document::publicDocumentsExist());
        });

        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
            fn(): string => view('filament.components.language-switcher')->render(),
        );

        // Make all table columns globally toggleable by default
        \Filament\Tables\Columns\Column::configureUsing(function (\Filament\Tables\Columns\Column $column) {
            $column->toggleable();
        });

        \Filament\Forms\Components\FileUpload::configureUsing(function (\Filament\Forms\Components\FileUpload $upload) {
            $upload->getUploadedFileUsing(static function (\Filament\Forms\Components\BaseFileUpload $component, string $file, string | array | null $storedFileNames): ?array {
                $storage = $component->getDisk();
                $shouldFetchFileInformation = $component->shouldFetchFileInformation();

                if ($shouldFetchFileInformation) {
                    try {
                        if (! $storage->exists($file)) {
                            return null;
                        }
                    } catch (\League\Flysystem\UnableToCheckFileExistence) {
                        return null;
                    }
                }

                $url = null;

                if ($component->getDiskName() === \App\Support\PublicStorage::diskName()) {
                    $url = \App\Support\PublicStorage::url($file);
                }

                if (! $url && $component->getVisibility() === 'private') {
                    try {
                        $url = $storage->temporaryUrl(
                            $file,
                            now()->addMinutes(30)->endOfHour(),
                        );
                    } catch (\Throwable) {
                        //
                    }
                }

                $url ??= $storage->url($file);

                return [
                    'name' => ($component->isMultiple() ? ($storedFileNames[$file] ?? null) : $storedFileNames) ?? basename($file),
                    'size' => $shouldFetchFileInformation ? $storage->size($file) : 0,
                    'type' => $shouldFetchFileInformation ? $storage->mimeType($file) : null,
                    'url' => $url,
                ];
            });
        });

        // Global Auto-Translation for Translatable Models
        \Illuminate\Support\Facades\Event::listen('eloquent.saving: *', function (string $eventName, array $data) {
            $model = $data[0] ?? null;
            if ($model && in_array(\Spatie\Translatable\HasTranslations::class, class_uses_recursive($model))) {
                if (property_exists($model, 'translatable')) {
                    $translator = app(\App\Services\AutoTranslateService::class);
                    foreach ($model->translatable as $field) {
                        $translations = $model->getTranslations($field);
                        $currentEn = $translations['en'] ?? null;
                        
                        // Check original database value to see if English text changed
                        $original = $model->getOriginal($field);
                        $originalEn = null;
                        if ($original) {
                            $originalArray = is_string($original) ? json_decode($original, true) : $original;
                            $originalEn = $originalArray['en'] ?? null;
                        }

                        // We translate if:
                        // 1. Khmer is completely empty OR
                        // 2. The English text was just modified/updated by the user
                        $khmerIsEmpty = empty($translations['km']);
                        $englishChanged = !empty($currentEn) && ($currentEn !== $originalEn);

                        $shouldTranslate = $model instanceof \App\Models\ProjectCategory
                            ? $khmerIsEmpty
                            : ($khmerIsEmpty || $englishChanged);

                        if (!empty($currentEn) && $shouldTranslate) {
                            // Translate English content to Khmer automatically
                            $translated = $translator->translate($currentEn, 'km');
                            if ($translated) {
                                $model->setTranslation($field, 'km', $translated);
                            }
                        }
                    }
                }
            }
        });
    }
}
