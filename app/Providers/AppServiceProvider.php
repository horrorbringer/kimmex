<?php

namespace App\Providers;

use App\Filesystem\CloudinaryAdapter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Storage::extend('cloudinary', function ($app, array $config): FilesystemAdapter {
            $adapter = new CloudinaryAdapter($config);

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config,
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ─── Remember Me cookie expires after 2 hours ───
        \Illuminate\Auth\SessionGuard::rememberFor(now()->addHours(2));

        // ─── Rate Limiters for DDoS protection ───
        \Illuminate\Support\Facades\RateLimiter::for('global', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(120)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('forms', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('auth', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->ip());
        });
        // Register cache-busting observer on all public content models
        $cacheBuster = \App\Observers\CacheBusterObserver::class;
        \App\Models\Project::observe($cacheBuster);
        \App\Models\Service::observe($cacheBuster);
        \App\Models\NewsArticle::observe($cacheBuster);
        \App\Models\Partner::observe($cacheBuster);
        \App\Models\Testimonial::observe($cacheBuster);
        \App\Models\Milestone::observe($cacheBuster);
        \App\Models\OrgUnit::observe($cacheBuster);
        \App\Models\Employee::observe($cacheBuster);
        \App\Models\MethodologyStep::observe($cacheBuster);
        \App\Models\JobPosting::observe($cacheBuster);
        \App\Models\Document::observe($cacheBuster);
        \App\Models\DocumentCategory::observe($cacheBuster);
        \App\Models\ProjectCategory::observe($cacheBuster);
        \App\Models\SystemSetting::observe($cacheBuster);

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            // Only run on web HTTP requests — skip CLI, queue workers, and Filament internals
            if (! app()->runningInConsole() && request()->hasSession()) {
                $lang = app()->getLocale();
                static $settingsByLocale = [];
                static $hasPublicDocuments = null;

                $settings = $settingsByLocale[$lang] ??= \Illuminate\Support\Facades\Cache::remember('global_settings_' . $lang, now()->addHours(12), function () use ($lang) {
                    $brandIdentity = \App\Models\SystemSetting::get('brand_identity', []);

                    return [
                        'profile'      => \App\Models\SystemSetting::get('organization_profile', []),
                        'brand'        => $brandIdentity[$lang] ?? $brandIdentity['en'] ?? [],
                        'theme'        => \App\Models\SystemSetting::get('theme_settings', []),
                        'integrations' => \App\Models\SystemSetting::get('integration_settings', []),
                    ];
                });

                $hasPublicDocuments ??= \Illuminate\Support\Facades\Cache::remember('has_public_documents', now()->addHours(1), fn () => \App\Models\Document::publicDocumentsExist());

                $view->with('globalSettings', $settings);
                $view->with('siteLocale', $lang);
                $view->with('hasPublicDocuments', $hasPublicDocuments);
            }
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
                    } catch (\Throwable) {
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

                try {
                    $size = $shouldFetchFileInformation ? $storage->size($file) : 0;
                } catch (\Throwable) {
                    $size = 0;
                }

                try {
                    $type = $shouldFetchFileInformation ? $storage->mimeType($file) : null;
                } catch (\Throwable) {
                    $type = null;
                }

                return [
                    'name' => ($component->isMultiple() ? ($storedFileNames[$file] ?? null) : $storedFileNames) ?? basename($file),
                    'size' => $size,
                    'type' => $type,
                    'url' => $url,
                ];
            });

            $upload->deleteUploadedFileUsing(static function (string $file) use ($upload): void {
                if ($upload->getDiskName() === \App\Support\PublicStorage::diskName()) {
                    \App\Support\PublicStorage::delete($file);

                    return;
                }

                $upload->getDisk()->delete($file);
            });
        });

        // Auto-Translation: dispatch async job instead of blocking the save request
        \Illuminate\Support\Facades\Event::listen('eloquent.saved: *', function (string $eventName, array $data) {
            // Only when AI auto-translate is enabled
            $aiSettings = \App\Models\SystemSetting::get('ai_settings', []);
            if (! ($aiSettings['auto_translate'] ?? true)) {
                return;
            }

            $model = $data[0] ?? null;
            if (! $model || ! in_array(\Spatie\Translatable\HasTranslations::class, class_uses_recursive($model))) {
                return;
            }
            if (! property_exists($model, 'translatable') || empty($model->translatable)) {
                return;
            }

            // Capture original EN values to pass to the job for change detection
            $originals = [];
            foreach ($model->translatable as $field) {
                $original = $model->getOriginal($field);
                if ($original) {
                    $arr = is_string($original) ? json_decode($original, true) : $original;
                    $originals[$field] = $arr['en'] ?? null;
                }
            }

            \App\Jobs\AutoTranslateModel::dispatch(
                get_class($model),
                $model->getKey(),
                $model->translatable,
                json_encode($originals),
            )->onQueue('default');
        });

        // Register model observers
        \App\Models\JobApplication::observe(\App\Observers\JobApplicationObserver::class);

        // Register SEO meta auto-generation observer
        $seoMetaObserver = \App\Observers\SeoMetaObserver::class;
        \App\Models\NewsArticle::observe($seoMetaObserver);
        \App\Models\Service::observe($seoMetaObserver);
        \App\Models\Project::observe($seoMetaObserver);
        \App\Models\Inquiry::observe(\App\Observers\InquiryObserver::class);
    }
}
