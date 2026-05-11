@props(['title' => null, 'description' => null, 'image' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $profile = $globalSettings['profile'] ?? [];
        $siteLocale = $siteLocale ?? app()->getLocale();
        $siteName = $profile[$siteLocale]['company_name'] ?? $profile['en']['company_name'] ?? config('app.name', 'KIMMEX');
        $logo = $profile['logo'] ?? null;
        $faviconUrl = $logo ? (\Illuminate\Support\Str::startsWith($logo, 'http') ? $logo : \Illuminate\Support\Facades\Storage::url($logo)) : asset('favicon.ico');
        
        $pageTitle = $title ? "{$title} | {$siteName}" : $siteName;
        $pageDesc = $description ?? 'Kimmex is a leading construction and engineering company delivering high-quality building and management solutions.';
        $pageImage = $image ?? ($logo ? (\Illuminate\Support\Str::startsWith($logo, 'http') ? $logo : \Illuminate\Support\Facades\Storage::url($logo)) : asset('logo.png'));
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="icon" href="{{ $faviconUrl }}">

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

    <!-- Dynamic Theme Styles -->
    @php
        $theme = $globalSettings['theme'] ?? [];
        $primaryColor = $theme['primary_color'] ?? '#D4A017'; 
        $secondaryColor = $theme['secondary_color'] ?? '#0B2B5C'; 
        $fontEn = $theme['font_family_en'] ?? 'Inter';
        $fontKm = $theme['font_family_km'] ?? 'Kantumruy Pro';
        $fontHeading = 'Montserrat'; // High-impact geometric heading font
        
        $fontsToLoad = collect([$fontEn, $fontKm, $fontHeading])->unique()->filter();
        $fontUrl = "https://fonts.googleapis.com/css2?" . $fontsToLoad->map(fn($f) => "family=" . str_replace(' ', '+', $f) . ":wght@300;400;500;600;700;800;900")->implode('&') . "&display=swap";
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $fontUrl }}" rel="stylesheet">

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
            --font-en: '{{ $fontEn }}', sans-serif;
            --font-km: '{{ $fontKm }}', sans-serif;
            --font-heading:  "Montserrat", "Outfit", "Inter", "Noto Sans Khmer", sans-serif;
        }
        
        .font-sans { font-family: var(--font-en); }
        .font-khmer { font-family: var(--font-km); }
        .font-heading { font-family: var(--font-heading); }
        
        /* Overwrite specific brand colors */
        .text-titan-red { color: var(--primary-color) !important; }
        .bg-titan-red { background-color: var(--primary-color) !important; }
        .border-titan-red { border-color: var(--primary-color) !important; }
        .text-titan-navy { color: var(--secondary-color) !important; }
        .bg-titan-navy { background-color: var(--secondary-color) !important; }
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

    <!-- Scroll to Top Button -->
    <div x-data="{ show: false }" 
         @scroll.window="show = window.pageYOffset > 500" 
         class="fixed bottom-8 right-8 z-[90]">
        <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })" 
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