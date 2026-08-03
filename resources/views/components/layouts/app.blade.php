@props(['title' => null, 'description' => null, 'image' => null, 'imageAlt' => null, 'canonical' => null, 'ogType' => 'website', 'robots' => null, 'priorityImage' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
    x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val); val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark') }); if (darkMode) document.documentElement.classList.add('dark')"
    @toggle-dark-mode.window="darkMode = !darkMode"
    :class="{ 'dark': darkMode }">

<head>
    <script>if(localStorage.getItem('darkMode')==='true')document.documentElement.classList.add('dark')</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="FNWYdR92oYLYxH7Tc7wkW8v6nhkNPGmcNnz9gSPVcLw">

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
        $faviconUrl = \App\Support\PublicStorage::urlIfExists($favicon, asset('favicon.ico'));
        
        $absoluteUrl = function (?string $value, ?string $fallback = null) {
            $value = filled($value) ? $value : $fallback;

            if (! filled($value)) {
                return null;
            }

            return \Illuminate\Support\Str::startsWith($value, ['http://', 'https://'])
                ? $value
                : url($value);
        };

        $pageTitle = filled($title) ? "{$title} | {$siteName}" : $siteName;
        $pageDesc = $description ?? 'Kimmex is a leading construction and engineering company delivering high-quality building and management solutions.';
        $logoUrl = \App\Support\PublicStorage::urlIfExists($logo, asset('logo.png'));
        $rawPageImage = $image ?? $logoUrl;
        $pageImage = $absoluteUrl($rawPageImage, asset('logo.png'));
        $socialImage = \Illuminate\Support\Str::startsWith($pageImage, 'https://res.cloudinary.com/')
            ? preg_replace(
                '#/image/upload/#',
                '/image/upload/c_fill,g_auto,w_1200,h_630,f_jpg,q_auto/',
                $pageImage,
                1,
            )
            : $pageImage;
        $pageImageAlt = $imageAlt ?? $pageTitle;
        $canonicalUrl = $absoluteUrl($canonical, url()->current());
        $organizationLogo = $absoluteUrl($logoUrl, asset('logo.png'));
        $organizationSameAs = collect([
            $profile['facebook'] ?? null,
            $profile['linkedin'] ?? null,
            $profile['youtube'] ?? null,
            $profile['instagram'] ?? null,
            $profile['telegram'] ?? null,
        ])->filter(fn ($value) => filled($value) && $value !== '#')->values()->all();
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $profile[$siteLocale]['company_name'] ?? $profile['en']['company_name'] ?? $siteName,
            'url' => url('/'),
            'logo' => $organizationLogo,
            'email' => $profile['email'] ?? null,
            'telephone' => $profile['phone'] ?? null,
            'address' => $profile[$siteLocale]['address'] ?? $profile['en']['address'] ?? null,
            'sameAs' => $organizationSameAs,
        ];
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <meta name="application-name" content="{{ $siteName }}">
    <meta name="theme-color" content="{{ $theme['primary_color'] ?? '#ffffff' }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $siteName }}">
    <link rel="manifest" href="{{ url('manifest.json') }}">
    <meta name="robots" content="{{ $robots ?? 'index, follow' }}">

    {{-- Let the browser fetch the above-the-fold image before JavaScript starts. --}}
    @if(filled($priorityImage))
        <link rel="preload" as="image" href="{{ $priorityImage }}" fetchpriority="high">
        @if(\Illuminate\Support\Str::startsWith($priorityImage, ['http://', 'https://']))
            <link rel="preconnect" href="{{ parse_url($priorityImage, PHP_URL_SCHEME) . '://' . parse_url($priorityImage, PHP_URL_HOST) }}" crossorigin>
        @endif
    @endif

    <!-- Hreflang -->
    <link rel="alternate" hreflang="en" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="km" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

    @stack('head')

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:image" content="{{ $socialImage }}">
    <meta property="og:image:secure_url" content="{{ $socialImage }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $pageImageAlt }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta property="og:site_name" content="{{ $siteName }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $socialImage }}">
    <meta name="twitter:image:alt" content="{{ $pageImageAlt }}">
    @if(\Illuminate\Support\Str::startsWith($pageImage, ['http://', 'https://']))
        <link rel="preconnect" href="{{ parse_url($pageImage, PHP_URL_SCHEME) . '://' . parse_url($pageImage, PHP_URL_HOST) }}" crossorigin>
    @endif

    <script type="application/ld+json">
        {!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/projects?search={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Dynamic Theme Styles -->
    @php
        $theme = $globalSettings['theme'] ?? [];
        $primaryColor = $theme['primary_color'] ?? '#D4A017'; 
        $primaryHover = $theme['primary_color_hover'] ?? '#B8890F'; 
        $secondaryColor = $theme['secondary_color'] ?? '#0B2B5C'; 
        $secondaryHover = $theme['secondary_color_hover'] ?? '#0E3A7A'; 
        $fontEn = $theme['font_family_en'] ?? 'Droid Serif';
        $fontKm = $theme['font_family_km'] ?? 'Suwannaphum';
        $fontHeading = 'Droid Serif'; // Serif heading font
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
        $fontUrl = "https://fonts.googleapis.com/css2?" . $fontsToLoad->map(fn($f) => "family=" . str_replace(' ', '+', $f) . ":wght@400;500;600;700;800;900")->implode('&') . "&display=swap";
    @endphp

    {{-- Critical CSS: prevent FOUC on header/navbar while full CSS downloads --}}
    <style>
        /* Navbar critical styles — renders correctly before app.css arrives */
        body > header { position: fixed; top: 0; left: 0; width: 100%; z-index: 100; }
        /* Top bar — initial state only; Alpine/Tailwind takes over on scroll */
        body > header > div:first-child {
            box-sizing: border-box;
            height: 2rem;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.025em;
            background: #fff;
            color: #0B2B5C;
            border-bottom: 1px solid #f3f4f6;
            overflow: hidden;
        }
        body > header > div:first-child > div {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 0.75rem;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
        }
        body > header > div:first-child a {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            color: rgba(11, 43, 92, 0.6);
            text-decoration: none;
            white-space: nowrap;
        }
        body > header > div:first-child svg {
            width: 12px;
            height: 12px;
            flex-shrink: 0;
        }
        @media (min-width: 640px) {
            body > header > div:first-child > div {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }
        /* Main nav */
        body > header nav {
            background: #fff;
            border-bottom: 1px solid #f3f4f6;
            width: 100%;
        }
        body > header nav > div { max-width: 1600px; margin: 0 auto; padding: 0 1.5rem; }
        body > header nav > div > div { display: flex; justify-content: space-between; align-items: center; height: 5rem; }
        body > header nav img { height: 3.5rem; width: auto; }
        /* Utilities */
        .antialiased { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .flex-col { display: flex; flex-direction: column; }
        .min-h-screen { min-height: 100vh; }
        body { margin: 0; background: #fff; }
        /* Hide elements that need JS/Alpine until full CSS loads */
        [x-cloak] { display: none !important; }
    </style>

    {{-- Load compiled CSS early so the browser starts downloading immediately --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="{{ $fontUrl }}">
    <link href="{{ $fontUrl }}" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="{{ $fontUrl }}" rel="stylesheet"></noscript>

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --primary-color-hover: {{ $primaryHover }};
            --secondary-color: {{ $secondaryColor }};
            --secondary-color-hover: {{ $secondaryHover }};
            --font-en: '{{ $fontEn }}', 'Georgia', serif;
            --font-km: '{{ $fontKm }}', 'Kantumruy Pro', sans-serif;
            --font-heading: {{ app()->getLocale() === 'km' ? "'$fontKm', 'Suwannaphum', sans-serif" : "'Droid Serif', '$fontEn', serif" }};
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
        
        /* text-titan-navy: only color the element itself, do NOT force inheritance
           onto children (which would break text-white inside navy sections) */
        .text-titan-navy { color: var(--secondary-color); }
        a.text-titan-navy:hover, button.text-titan-navy:hover { color: var(--secondary-color-hover); }
        
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

        main > section,
        main > div > section {
            content-visibility: auto;
            contain-intrinsic-size: auto 900px;
        }

        /* Hero is always above the fold — never skip its rendering */
        main > section:first-child {
            content-visibility: visible;
            contain-intrinsic-size: auto;
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>

</head>

<body
    class="antialiased bg-white text-titan-navy flex flex-col min-h-screen relative {{ app()->getLocale() === 'km' ? 'font-khmer khmer-optimized' : 'font-sans' }}">

    <div id="page-loading-bar" aria-hidden="true"></div>

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

                const isHero = index === 0 || image.closest('header') || image.closest('[data-priority-image]');

                if (!image.hasAttribute('loading')) {
                    image.setAttribute('loading', isHero ? 'eager' : 'lazy');
                }

                // Keep below-the-fold images visually quiet until their native lazy load finishes.
                if (!isHero) {
                    image.style.opacity = '0';
                    image.style.transition = 'opacity 0.4s ease';
                }

                image.addEventListener('load', () => {
                    image.dataset.imageLoaded = 'true';
                    image.style.opacity = '1';
                }, { once: true });

                image.addEventListener('error', () => {
                    image.dataset.imageError = 'true';
                    image.style.opacity = '1';
                }, { once: true });

                // If already loaded (cached), show immediately
                if (image.complete && image.naturalHeight > 0) {
                    image.dataset.imageLoaded = 'true';
                    image.style.opacity = '1';
                }
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
    @livewireScripts
</body>

</html>
