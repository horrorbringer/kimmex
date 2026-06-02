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
use Filament\Widgets\FilamentInfoWidget;
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
            ->login()
            ->brandName(fn() => \App\Models\SystemSetting::get('organization_profile', [])['en']['website_title'] ?? \App\Models\SystemSetting::get('organization_profile', [])['en']['company_name'] ?? 'Kimmex Admin')
            ->brandLogo(function() {
                $logo = \App\Models\SystemSetting::get('organization_profile', [])['logo'] ?? null;
                $url = $logo ? (\Illuminate\Support\Str::startsWith($logo, 'http') ? $logo : \App\Support\PublicStorage::url($logo)) : asset('logo.png');
                return new \Illuminate\Support\HtmlString("<img src='{$url}' alt='Logo' style='height: 2.5rem; width: auto; object-fit: contain;'>");
            })
            ->favicon(function() {
                $profile = \App\Models\SystemSetting::get('organization_profile', []);
                $favicon = $profile['favicon'] ?? $profile['logo'] ?? null;
                return $favicon ? (\Illuminate\Support\Str::startsWith($favicon, 'http') ? $favicon : \App\Support\PublicStorage::url($favicon)) : asset('favicon.ico');
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
            ->font('Kantumruy Pro')
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::hex(self::getThemeColor('primary_color', '#E31E24')),
                'secondary' => Color::hex(self::getThemeColor('secondary_color', '#1a1a2e')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                \App\Filament\Pages\ManageSettings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\LatestInquiriesWidget::class,
                \App\Filament\Widgets\LatestJobApplicationsWidget::class,
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
