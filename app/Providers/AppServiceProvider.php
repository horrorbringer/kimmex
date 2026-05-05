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
                ];
            });

            $view->with('globalSettings', $settings);
            $view->with('siteLocale', $lang);
        });

        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
            fn(): string => view('filament.components.language-switcher')->render(),
        );

        // Make all table columns globally toggleable by default
        \Filament\Tables\Columns\Column::configureUsing(function (\Filament\Tables\Columns\Column $column) {
            $column->toggleable();
        });
    }
}
