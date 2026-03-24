@props(['title' => null, 'description' => null, 'image' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    @php
        $siteName = config('app.name', 'Kimmex CMS');
        $pageTitle = $title ? "{$title} | {$siteName}" : $siteName;
        $pageDesc = $description ?? 'Kimmex is a leading construction and engineering company delivering high-quality building and management solutions.';
        $pageImage = $image ?? asset('images/logo.webp');
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    
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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;700;900&family=Kantumruy+Pro:wght@100..700&family=Hanuman:wght@400;700;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-titan-navy flex flex-col min-h-screen relative {{ app()->getLocale() === 'km' ? 'font-khmer' : 'font-sans' }}">

    <x-header />

    <main class="flex-grow {{ request()->routeIs(['home', 'about', 'services.index', 'services.show', 'projects.index', 'news.index', 'contact', 'careers']) ? '' : 'pt-[120px]' }}">
        {{ $slot }}
    </main>

    <x-footer />

</body>
</html>
