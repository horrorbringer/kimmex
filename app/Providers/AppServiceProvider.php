<?php

namespace App\Providers;

use App\Filesystem\CloudinaryAdapter;
use App\Jobs\AutoTranslateModel;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Employee;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\MethodologyStep;
use App\Models\Milestone;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\OrgUnit;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SystemSetting;
use App\Models\Testimonial;
use App\Observers\CacheBusterObserver;
use App\Observers\InquiryObserver;
use App\Observers\JobApplicationObserver;
use App\Observers\SeoMetaObserver;
use App\Support\PublicStorage;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Column;
use Filament\View\PanelsRenderHook;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem;
use Spatie\Translatable\HasTranslations;

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
        // ─── Remember Me cookie expires after 2 hours (120 minutes) ───
        Auth::setRememberDuration(120);

        // ─── Rate Limiters for DDoS protection ───
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        RateLimiter::for('forms', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });
        // Register cache-busting observer on all public content models
        $cacheBuster = CacheBusterObserver::class;
        Project::observe($cacheBuster);
        Service::observe($cacheBuster);
        NewsArticle::observe($cacheBuster);
        Partner::observe($cacheBuster);
        Testimonial::observe($cacheBuster);
        Milestone::observe($cacheBuster);
        OrgUnit::observe($cacheBuster);
        Employee::observe($cacheBuster);
        MethodologyStep::observe($cacheBuster);
        JobPosting::observe($cacheBuster);
        Document::observe($cacheBuster);
        DocumentCategory::observe($cacheBuster);
        ProjectCategory::observe($cacheBuster);
        SystemSetting::observe($cacheBuster);
        NewsCategory::observe($cacheBuster);

        View::composer('*', function ($view) {
            if (app()->runningInConsole() && ! app()->runningUnitTests()) {
                return;
            }

            if (request()->is('admin') || request()->is('admin/*')) {
                return;
            }

            $lang = app()->getLocale();
            static $settingsByLocale = [];
            static $hasPublicDocuments = null;

            $settings = $settingsByLocale[$lang] ??= Cache::remember('global_settings_'.$lang, now()->addHours(12), function () use ($lang) {
                $brandIdentity = SystemSetting::get('brand_identity', []);

                return [
                    'profile' => SystemSetting::get('organization_profile', []),
                    'brand' => $brandIdentity[$lang] ?? $brandIdentity['en'] ?? [],
                    'theme' => SystemSetting::get('theme_settings', []),
                    'integrations' => SystemSetting::get('integration_settings', []),
                ];
            });

            $hasPublicDocuments ??= Cache::remember('has_public_documents', now()->addHours(1), fn () => Document::publicDocumentsExist());

            $newsCategories = Cache::remember('news_categories_list_'.$lang, now()->addHours(12), function () use ($lang) {
                $dbCategories = NewsCategory::where('is_active', true)
                    ->orderBy('order_index')
                    ->get()
                    ->map(function ($cat) use ($lang) {
                        $name = $cat->getTranslation('name', $lang) ?: $cat->getTranslation('name', 'en');

                        return [
                            'name' => $name,
                            'slug' => $cat->slug,
                            'url' => '/news?category='.urlencode($cat->slug),
                        ];
                    })->toArray();

                if (! empty($dbCategories)) {
                    return $dbCategories;
                }

                $rawCategories = NewsArticle::where('isActive', true)
                    ->where('publishedAt', '<=', now())
                    ->get()
                    ->map(fn ($n) => $n->getTranslation('category', $lang) ?: $n->getTranslation('category', 'en'))
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                if (empty($rawCategories)) {
                    $rawCategories = [__('Building Construction'), __('General News')];
                }

                return array_map(fn ($cat) => [
                    'name' => $cat,
                    'slug' => Str::slug($cat),
                    'url' => '/news?category='.urlencode($cat),
                ], $rawCategories);
            });

            $view->with('globalSettings', $settings);
            $view->with('siteLocale', $lang);
            $view->with('hasPublicDocuments', $hasPublicDocuments);
            $view->with('newsCategories', $newsCategories);
        });

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => view('filament.components.language-switcher')->render(),
        );

        // Make all table columns globally toggleable by default
        Column::configureUsing(function (Column $column) {
            $column->toggleable();
        });

        FileUpload::configureUsing(function (FileUpload $upload) {
            $upload->getUploadedFileUsing(static function (BaseFileUpload $component, string $file, string|array|null $storedFileNames): ?array {
                $storage = $component->getDisk();
                $shouldFetchFileInformation = $component->shouldFetchFileInformation();
                $isRemotePublicUpload = $component->getDiskName() === PublicStorage::diskName()
                    && PublicStorage::isRemoteDisk();

                if ($shouldFetchFileInformation && ! $isRemotePublicUpload) {
                    try {
                        if (! $storage->exists($file)) {
                            return null;
                        }
                    } catch (\Throwable) {
                        return null;
                    }
                }

                $url = null;

                if ($component->getDiskName() === PublicStorage::diskName()) {
                    $url = PublicStorage::url($file);
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
                    $size = $shouldFetchFileInformation && ! $isRemotePublicUpload ? $storage->size($file) : 0;
                } catch (\Throwable) {
                    $size = 0;
                }

                try {
                    $type = $shouldFetchFileInformation && ! $isRemotePublicUpload ? $storage->mimeType($file) : null;
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
                if ($upload->getDiskName() === PublicStorage::diskName()) {
                    PublicStorage::delete($file);

                    return;
                }

                $upload->getDisk()->delete($file);
            });
        });

        // Auto-Translation: dispatch async job instead of blocking the save request
        Event::listen('eloquent.saved: *', function (string $eventName, array $data) {
            // Only when AI auto-translate is enabled
            $aiSettings = SystemSetting::get('ai_settings', []);
            if (! ($aiSettings['auto_translate'] ?? true)) {
                return;
            }

            $model = $data[0] ?? null;
            if (! $model || ! in_array(HasTranslations::class, class_uses_recursive($model))) {
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

            AutoTranslateModel::dispatchSync(
                get_class($model),
                $model->getKey(),
                $model->translatable,
                json_encode($originals),
            );
        });

        // Register model observers
        JobApplication::observe(JobApplicationObserver::class);

        // Register SEO meta auto-generation observer
        $seoMetaObserver = SeoMetaObserver::class;
        NewsArticle::observe($seoMetaObserver);
        Service::observe($seoMetaObserver);
        Project::observe($seoMetaObserver);
        Inquiry::observe(InquiryObserver::class);
    }
}
