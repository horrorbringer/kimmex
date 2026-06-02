@props(['title' => null, 'description' => null, 'image' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $profile = $globalSettings['profile'] ?? [];
        $siteLocale = $siteLocale ?? app()->getLocale();
        $siteName = collect([
            $profile[$siteLocale]['website_title'] ?? null,
            $profile['en']['website_title'] ?? null,
            $profile[$siteLocale]['company_name'] ?? null,
            $profile['en']['company_name'] ?? null,
            config('app.name'),
            'KIMMEX',
        ])->first(fn ($value) => filled($value));
        $logo = $profile['logo'] ?? null;
        
        $favicon = $profile['favicon'] ?? null;
        $faviconUrl = $favicon ? (\Illuminate\Support\Str::startsWith($favicon, 'http') ? $favicon : \App\Support\PublicStorage::url($favicon)) : asset('favicon.ico');
        
        $pageTitle = filled($title) ? "{$title} | {$siteName}" : $siteName;
        $pageDesc = $description ?? 'Kimmex is a leading construction and engineering company delivering high-quality building and management solutions.';
        $pageImage = $image ?? ($logo ? (\Illuminate\Support\Str::startsWith($logo, 'http') ? $logo : \App\Support\PublicStorage::url($logo)) : asset('logo.png'));
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <meta name="application-name" content="{{ $siteName }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:image" content="{{ $pageImage }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="{{ $pageTitle }}">
    <meta property="twitter:description" content="{{ $pageDesc }}">
    <meta property="twitter:image" content="{{ $pageImage }}">
    @if(\Illuminate\Support\Str::startsWith($pageImage, ['http://', 'https://']))
        <link rel="preconnect" href="{{ parse_url($pageImage, PHP_URL_SCHEME) . '://' . parse_url($pageImage, PHP_URL_HOST) }}" crossorigin>
    @endif

    <!-- Dynamic Theme Styles -->
    @php
        $theme = $globalSettings['theme'] ?? [];
        $primaryColor = $theme['primary_color'] ?? '#D4A017'; 
        $primaryHover = $theme['primary_color_hover'] ?? '#B8890F'; 
        $secondaryColor = $theme['secondary_color'] ?? '#0B2B5C'; 
        $secondaryHover = $theme['secondary_color_hover'] ?? '#0E3A7A'; 
        $fontEn = $theme['font_family_en'] ?? 'Plus Jakarta Sans';
        $fontKm = $theme['font_family_km'] ?? 'Kantumruy Pro';
        $fontHeading = 'Montserrat'; // High-impact geometric heading font
        $footerBg     = filled($theme['footer_bg_color'] ?? null) ? $theme['footer_bg_color'] : '#071A33';
        $footerAccent = $theme['footer_accent_color']  ?? '#ED1C24';

        $footerHex = ltrim($footerBg, '#');
        if (strlen($footerHex) === 3) {
            $footerHex = collect(str_split($footerHex))->map(fn($c) => $c.$c)->implode('');
        }
        $footerRgb = strlen($footerHex) === 6
            ? [hexdec(substr($footerHex, 0, 2)), hexdec(substr($footerHex, 2, 2)), hexdec(substr($footerHex, 4, 2))]
            : [255, 255, 255];
        $footerLuminance = (($footerRgb[0] * 299) + ($footerRgb[1] * 587) + ($footerRgb[2] * 114)) / 1000;
        $isLightFooter = $footerLuminance > 170;
        $footerText = $isLightFooter ? '#0B2B5C' : '#FFFFFF';
        $footerMuted = $isLightFooter ? 'rgba(11, 43, 92, 0.64)' : 'rgba(255, 255, 255, 0.56)';
        $footerSubtle = $isLightFooter ? 'rgba(11, 43, 92, 0.42)' : 'rgba(255, 255, 255, 0.42)';
        $footerBorder = $isLightFooter ? 'rgba(11, 43, 92, 0.10)' : 'rgba(255, 255, 255, 0.10)';
        $footerSurface = $isLightFooter ? 'rgba(11, 43, 92, 0.035)' : 'rgba(255, 255, 255, 0.06)';
        
        $fontsToLoad = collect([$fontEn, $fontKm, $fontHeading])->unique()->filter();
        $fontUrl = "https://fonts.googleapis.com/css2?" . $fontsToLoad->map(fn($f) => "family=" . str_replace(' ', '+', $f) . ":wght@300;400;500;600;700;800;900")->implode('&') . "&display=swap";
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $fontUrl }}" rel="stylesheet">

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --primary-color-hover: {{ $primaryHover }};
            --secondary-color: {{ $secondaryColor }};
            --secondary-color-hover: {{ $secondaryHover }};
            --font-en: '{{ $fontEn }}', 'Inter', sans-serif;
            --font-km: '{{ $fontKm }}', 'Noto Sans Khmer', sans-serif;
            --font-heading: {{ app()->getLocale() === 'km' ? "'$fontKm', 'Montserrat', sans-serif" : "'Montserrat', '$fontEn', sans-serif" }};
        }
        
        /* ─── FOOTER-ONLY color variables — isolated from global brand colors ─── */
        footer {
            --footer-bg:     {{ $footerBg }};
            --footer-accent: {{ $footerAccent }};
            --footer-text:   {{ $footerText }};
            --footer-muted:  {{ $footerMuted }};
            --footer-subtle: {{ $footerSubtle }};
            --footer-border: {{ $footerBorder }};
            --footer-surface: {{ $footerSurface }};
        }
        
        .font-sans { font-family: var(--font-en); }
        .font-khmer { font-family: var(--font-km); }
        .font-heading { font-family: var(--font-heading); }
        
        /* Overwrite specific brand colors and hover states */
        .text-titan-red { color: var(--primary-color) !important; }
        a.text-titan-red:hover, button.text-titan-red:hover { color: var(--primary-color-hover) !important; }
        
        .bg-titan-red { background-color: var(--primary-color) !important; }
        a.bg-titan-red:hover, button.bg-titan-red:hover { background-color: var(--primary-color-hover) !important; }
        
        .border-titan-red { border-color: var(--primary-color) !important; }
        a.border-titan-red:hover, button.border-titan-red:hover { border-color: var(--primary-color-hover) !important; }
        
        .text-titan-navy { color: var(--secondary-color) !important; }
        a.text-titan-navy:hover, button.text-titan-navy:hover { color: var(--secondary-color-hover) !important; }
        
        .bg-titan-navy { background-color: var(--secondary-color) !important; }
        a.bg-titan-navy:hover, button.bg-titan-navy:hover { background-color: var(--secondary-color-hover) !important; }

        img[loading="lazy"] {
            background-color: rgba(11, 43, 92, 0.04);
        }

        img[data-image-loaded="true"] {
            background-color: transparent;
        }

        img[data-image-error="true"] {
            opacity: 0.7;
            background:
                linear-gradient(135deg, rgba(11, 43, 92, 0.08), rgba(237, 28, 36, 0.08));
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="antialiased bg-white text-titan-navy flex flex-col min-h-screen relative {{ app()->getLocale() === 'km' ? 'font-khmer khmer-optimized' : 'font-sans' }}">

    <x-header />

    <main
        class="flex-grow {{ request()->routeIs(['home', 'about', 'services.index', 'services.show', 'projects.index', 'projects.show', 'news.index', 'news.show', 'contact', 'careers', 'careers.show']) ? '' : 'pt-[120px]' }}">
        {{ $slot }}
    </main>

    <x-footer />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('img').forEach((image, index) => {
                if (!image.hasAttribute('decoding')) {
                    image.setAttribute('decoding', 'async');
                }

                if (!image.hasAttribute('loading')) {
                    const isLikelyHero = index === 0 || image.closest('header') || image.closest('[data-priority-image]');
                    image.setAttribute('loading', isLikelyHero ? 'eager' : 'lazy');
                }

                image.addEventListener('load', () => {
                    image.dataset.imageLoaded = 'true';
                }, { once: true });

                image.addEventListener('error', () => {
                    image.dataset.imageError = 'true';
                }, { once: true });
            });
        });
    </script>

    <!-- Scroll to Top Button -->
    <div x-data="{
            show: false,
            scrollToTop() {
                const start = window.pageYOffset || document.documentElement.scrollTop || 0;
                const duration = 700;
                const startTime = performance.now();
                const easeInOutCubic = (t) => t < 0.5
                    ? 4 * t * t * t
                    : 1 - Math.pow(-2 * t + 2, 3) / 2;

                const step = (now) => {
                    const progress = Math.min((now - startTime) / duration, 1);
                    const eased = easeInOutCubic(progress);
                    window.scrollTo(0, start * (1 - eased));

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    }
                };

                requestAnimationFrame(step);
            }
        }" 
         @scroll.window="show = window.pageYOffset > 500" 
         class="fixed bottom-8 right-8 z-[90]">
        <button @click="scrollToTop()" 
                x-show="show" style="display: none;"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-8"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300 transform"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-8"
                class="w-12 h-12 bg-titan-red hover:bg-red-700 text-white rounded-full flex items-center justify-center shadow-xl shadow-titan-red/30 transition-colors focus:outline-none focus:ring-4 focus:ring-titan-red/50 group"
                aria-label="Scroll to top">
            <x-lucide-arrow-up class="w-5 h-5 group-hover:-translate-y-1 transition-transform duration-300" />
        </button>
    </div>

</body>

</html>
