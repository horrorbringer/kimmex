<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->passwordReset()
            ->brandName(function () {
                $profile = \App\Models\SystemSetting::get('organization_profile', []);

                return $profile['en']['website_title'] ?? $profile['en']['company_name'] ?? 'Kimmex Admin';
            })
            ->brandLogo(function() {
                $logo = \App\Models\SystemSetting::get('organization_profile', [])['logo'] ?? null;
                $url = \App\Support\PublicStorage::urlIfExists($logo, asset('logo.png'));
                return new \Illuminate\Support\HtmlString("<img src='{$url}' alt='Logo' style='height: 2.5rem; width: auto; object-fit: contain;'>");
            })
            ->favicon(function() {
                $profile = \App\Models\SystemSetting::get('organization_profile', []);
                $favicon = $profile['favicon'] ?? $profile['logo'] ?? null;
                return \App\Support\PublicStorage::urlIfExists($favicon, asset('favicon.ico'));
            })
            ->brandLogoHeight('2.5rem')
            ->homeUrl('/')
            ->darkMode()
            ->defaultThemeMode(ThemeMode::Light)
            ->navigationItems([
                \Filament\Navigation\NavigationItem::make('visit_website')
                    ->label(fn () => __('Visit Website'))
                    ->url('/')
                    ->icon('heroicon-o-globe-alt')
                    ->sort(-1),
            ])
            ->font('Suwannaphum')
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::hex(self::getThemeColor('primary_color', '#E31E24')),
                'secondary' => Color::hex(self::getThemeColor('secondary_color', '#1a1a2e')),
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->resources([
                \App\Filament\Resources\Subscribers\SubscriberResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                \App\Filament\Pages\ManageSettings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\LatestInquiriesWidget::class,
                \App\Filament\Widgets\LatestJobApplicationsWidget::class,
                \App\Filament\Widgets\InquiriesChartWidget::class,
                \App\Filament\Widgets\JobApplicationsChartWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                \App\Http\Middleware\SetLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                \LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin::make()
                    ->defaultLocales(['en', 'km']),
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => \Illuminate\Support\Facades\Blade::render('@livewire(\'ai-switcher\')'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): string => '<script src="' . \Illuminate\Support\Facades\Vite::asset('resources/js/admin-enhancements.js') . '"></script>',
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                function (): string {
                    $primaryHover = self::getThemeColor('primary_color_hover', '#C8151D');
                    $primaryColor = self::getThemeColor('primary_color', '#E31E24');

                    $hex2rgb = function ($hex) {
                        $hex = str_replace('#', '', $hex);
                        if (strlen($hex) == 3) {
                            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
                        } else {
                            $r = hexdec(substr($hex, 0, 2));
                            $g = hexdec(substr($hex, 2, 2));
                            $b = hexdec(substr($hex, 4, 2));
                        }
                        return "$r, $g, $b";
                    };

                    $primaryHoverRgb = $hex2rgb($primaryHover);
                    $primaryColorRgb = $hex2rgb($primaryColor);

                    return "<style>
                        :root {
                            /* Override primary color shades */
                            --primary-500: {$primaryColorRgb};
                            --primary-600: {$primaryHoverRgb};
                        }

                        /* Dynamic hover overrides for links and specific interactive components */
                        .fi-btn.fi-color-primary:not(.fi-btn-outline):hover {
                            background-color: {$primaryHover} !important;
                        }
                        .fi-btn.fi-color-primary:not(.fi-btn-outline) {
                            background-color: {$primaryColor} !important;
                            color: #ffffff !important;
                            border-color: {$primaryColor} !important;
                        }
                        .fi-page .fi-page-header-main-ctn .fi-btn,
                        .fi-page .fi-page-content .fi-ac .fi-btn,
                        .fi-page .fi-form-actions .fi-btn,
                        .fi-modal-footer-actions .fi-btn,
                        .fi-ta-actions .fi-btn {
                            background-color: #0f172a !important;
                            color: #ffffff !important;
                            border: 1px solid #0f172a !important;
                            box-shadow: 0 8px 18px -14px rgba(15, 23, 42, 0.9) !important;
                        }
                        .fi-page .fi-page-header-main-ctn .fi-btn:hover,
                        .fi-page .fi-page-header-main-ctn .fi-btn:focus,
                        .fi-page .fi-page-content .fi-ac .fi-btn:hover,
                        .fi-page .fi-page-content .fi-ac .fi-btn:focus,
                        .fi-page .fi-form-actions .fi-btn:hover,
                        .fi-page .fi-form-actions .fi-btn:focus,
                        .fi-modal-footer-actions .fi-btn:hover,
                        .fi-modal-footer-actions .fi-btn:focus,
                        .fi-ta-actions .fi-btn:hover,
                        .fi-ta-actions .fi-btn:focus {
                            background-color: {$primaryColor} !important;
                            color: #ffffff !important;
                            border-color: {$primaryColor} !important;
                        }
                        .fi-page .fi-page-header-main-ctn .fi-btn.fi-color-danger,
                        .fi-page .fi-page-content .fi-ac .fi-btn.fi-color-danger,
                        .fi-page .fi-form-actions .fi-btn.fi-color-danger,
                        .fi-modal-footer-actions .fi-btn.fi-color-danger,
                        .fi-ta-actions .fi-btn.fi-color-danger {
                            background-color: #dc2626 !important;
                            border-color: #dc2626 !important;
                            color: #ffffff !important;
                        }
                        .fi-page .fi-page-header-main-ctn .fi-btn *,
                        .fi-page .fi-page-content .fi-ac .fi-btn *,
                        .fi-page .fi-form-actions .fi-btn *,
                        .fi-modal-footer-actions .fi-btn *,
                        .fi-ta-actions .fi-btn * {
                            color: currentColor !important;
                        }
                        .fi-btn.fi-color-gray,
                        .fi-btn.fi-btn-color-gray,
                        .fi-btn.fi-color-neutral,
                        .fi-btn.fi-btn-color-neutral,
                        .fi-btn.fi-color-white,
                        .fi-btn.fi-btn-color-white,
                        .fi-btn:not([class*='fi-color-']):not([class*='fi-btn-color-']) {
                            background-color: #ffffff !important;
                            color: #334155 !important;
                            border: 1px solid #cbd5e1 !important;
                            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
                        }
                        .fi-btn.fi-color-gray:hover,
                        .fi-btn.fi-color-gray:focus,
                        .fi-btn.fi-btn-color-gray:hover,
                        .fi-btn.fi-btn-color-gray:focus,
                        .fi-btn.fi-color-neutral:hover,
                        .fi-btn.fi-color-neutral:focus,
                        .fi-btn.fi-btn-color-neutral:hover,
                        .fi-btn.fi-btn-color-neutral:focus,
                        .fi-btn.fi-color-white:hover,
                        .fi-btn.fi-color-white:focus,
                        .fi-btn.fi-btn-color-white:hover,
                        .fi-btn.fi-btn-color-white:focus,
                        .fi-btn:not([class*='fi-color-']):not([class*='fi-btn-color-']):hover,
                        .fi-btn:not([class*='fi-color-']):not([class*='fi-btn-color-']):focus {
                            background-color: #f8fafc !important;
                            color: #0f172a !important;
                            border-color: #94a3b8 !important;
                            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.1) !important;
                        }
                        .fi-btn.fi-color-gray .fi-btn-label,
                        .fi-btn.fi-btn-color-gray .fi-btn-label,
                        .fi-btn.fi-color-neutral .fi-btn-label,
                        .fi-btn.fi-btn-color-neutral .fi-btn-label,
                        .fi-btn.fi-color-white .fi-btn-label,
                        .fi-btn.fi-btn-color-white .fi-btn-label,
                        .fi-btn:not([class*='fi-color-']):not([class*='fi-btn-color-']) .fi-btn-label,
                        .fi-btn.fi-color-gray svg,
                        .fi-btn.fi-btn-color-gray svg,
                        .fi-btn.fi-color-neutral svg,
                        .fi-btn.fi-btn-color-neutral svg,
                        .fi-btn.fi-color-white svg,
                        .fi-btn.fi-btn-color-white svg,
                        .fi-btn:not([class*='fi-color-']):not([class*='fi-btn-color-']) svg {
                            color: currentColor !important;
                        }
                        .dark .fi-btn.fi-color-gray,
                        .dark .fi-btn.fi-btn-color-gray,
                        .dark .fi-btn.fi-color-neutral,
                        .dark .fi-btn.fi-btn-color-neutral,
                        .dark .fi-btn.fi-color-white,
                        .dark .fi-btn.fi-btn-color-white,
                        .dark .fi-btn:not([class*='fi-color-']):not([class*='fi-btn-color-']) {
                            background-color: #1e293b !important;
                            color: #e2e8f0 !important;
                            border-color: #475569 !important;
                        }
                        .dark .fi-btn.fi-color-gray:hover,
                        .dark .fi-btn.fi-color-gray:focus,
                        .dark .fi-btn.fi-btn-color-gray:hover,
                        .dark .fi-btn.fi-btn-color-gray:focus,
                        .dark .fi-btn.fi-color-neutral:hover,
                        .dark .fi-btn.fi-color-neutral:focus,
                        .dark .fi-btn.fi-btn-color-neutral:hover,
                        .dark .fi-btn.fi-btn-color-neutral:focus,
                        .dark .fi-btn.fi-color-white:hover,
                        .dark .fi-btn.fi-color-white:focus,
                        .dark .fi-btn.fi-btn-color-white:hover,
                        .dark .fi-btn.fi-btn-color-white:focus,
                        .dark .fi-btn:not([class*='fi-color-']):not([class*='fi-btn-color-']):hover,
                        .dark .fi-btn:not([class*='fi-color-']):not([class*='fi-btn-color-']):focus {
                            background-color: #334155 !important;
                            color: #f8fafc !important;
                            border-color: #64748b !important;
                        }
                        /* Keep focused fields and selected checkboxes visible on light surfaces. */
                        .fi-input-wrp {
                            background-color: #ffffff !important;
                            box-shadow: 0 0 0 1px #cbd5e1 !important;
                        }
                        .fi-input-wrp:focus-within {
                            box-shadow: 0 0 0 2px {$primaryColor}, 0 0 0 4px rgba({$primaryColorRgb}, 0.14) !important;
                        }
                        input[type='checkbox'].fi-checkbox-input {
                            background-color: #ffffff !important;
                            box-shadow: 0 0 0 1px #94a3b8 !important;
                        }
                        input[type='checkbox'].fi-checkbox-input:checked {
                            background-color: {$primaryColor} !important;
                            box-shadow: 0 0 0 1px {$primaryColor} !important;
                        }
                        input[type='checkbox'].fi-checkbox-input:focus-visible {
                            box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px {$primaryColor} !important;
                        }
                        .dark .fi-input-wrp {
                            background-color: rgb(30, 41, 59) !important;
                            box-shadow: 0 0 0 1px rgb(71, 85, 105) !important;
                        }
                        .dark .fi-input-wrp:focus-within {
                            box-shadow: 0 0 0 2px {$primaryColor}, 0 0 0 4px rgba({$primaryColorRgb}, 0.2) !important;
                        }
                        .dark input[type='checkbox'].fi-checkbox-input:not(:checked) {
                            background-color: rgb(30, 41, 59) !important;
                            box-shadow: 0 0 0 1px rgb(100, 116, 139) !important;
                        }
                        .fi-link.fi-color-primary:hover, .fi-link.fi-color-primary:focus {
                            color: {$primaryHover} !important;
                        }
                        .fi-sidebar-item-button:hover, .fi-sidebar-item-button:focus {
                            background-color: rgba({$primaryHoverRgb}, 0.08) !important;
                        }
                        .dark .fi-sidebar-item-button:hover,
                        .dark .fi-sidebar-item-button:focus {
                            background-color: rgba({$primaryHoverRgb}, 0.16) !important;
                        }
                        .fi-tabs-item:hover {
                            color: {$primaryHover} !important;
                            border-color: {$primaryHover} !important;
                        }

                        /* Make status/toggle switches visible against white dashboard surfaces. */
                        .fi-toggle.fi-toggle-on {
                            background-color: {$primaryColor} !important;
                            border-color: {$primaryColor} !important;
                            box-shadow: 0 0 0 1px rgba({$primaryColorRgb}, 0.18), 0 8px 18px -12px rgba({$primaryColorRgb}, 0.75) !important;
                        }
                        .fi-toggle.fi-toggle-on:hover,
                        .fi-toggle.fi-toggle-on:focus {
                            background-color: {$primaryHover} !important;
                            border-color: {$primaryHover} !important;
                        }
                        .fi-toggle.fi-toggle-off {
                            background-color: rgb(226, 232, 240) !important;
                            border-color: rgb(203, 213, 225) !important;
                            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
                        }
                        .dark .fi-toggle.fi-toggle-off {
                            background-color: rgb(71, 85, 105) !important;
                            border-color: rgb(100, 116, 139) !important;
                        }
                        .fi-toggle > div {
                            background-color: #ffffff !important;
                            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.2) !important;
                        }
                    </style>";
                }
            )
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn() => __('Organization'))
                    ->icon('heroicon-o-identification'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn() => __('Portfolio'))
                    ->icon('heroicon-o-briefcase'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn() => __('Communication'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->collapsed(),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn() => __('Governance'))
                    ->icon('heroicon-o-shield-check')
                    ->collapsed(),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn() => __('Administration'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected static function getThemeColor(string $key, string $default): string
    {
        try {
            return \App\Models\SystemSetting::get('theme_settings', [])[$key] ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
