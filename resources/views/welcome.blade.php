@php
    // This mirrors the hero's cached first slide so the browser can begin its
    // largest image request from the document head, before Alpine initializes.
    $heroLocale = app()->getLocale() === 'kh' ? 'km' : app()->getLocale();
    $priorityHeroImage = \Illuminate\Support\Facades\Cache::remember('hero_priority_image_'.$heroLocale, now()->addHours(6), function () {
        $image = \App\Models\Project::where('isFeatured', true)
            ->where('isActive', true)
            ->value('heroImage');

        return \App\Support\PublicStorage::urlIfExists($image, '/images/webp/hero/hero-1.webp');
    });
@endphp

<x-layouts.app title="Home"
    :priority-image="$priorityHeroImage"
    description="Kimmex is a leading construction and engineering company in Cambodia delivering high-quality building and management solutions.">

    @push('head')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => 'Kimmex Construction - Cambodia\'s Premier Construction Company',
        'description' => 'Kimmex Construction & Investment Co., Ltd is a leading construction and engineering company in Cambodia with over 25 years of experience, 150+ completed projects, and 500+ team members.',
        'speakable' => [
            '@type' => 'SpeakableSpecification',
            'cssSelector' => ['h1', 'h2', '.hero-copy-shadow', 'meta[name=description]'],
        ],
        'about' => [
            '@type' => 'Thing',
            'name' => 'Construction Services in Cambodia',
            'description' => 'Design & Build, Civil Construction, MEP Systems, Project Management, Infrastructure Development, and Engineering Consultancy.',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endpush

    <x-home.hero-carousel />
    <x-home.trust-strip />
    <x-home.about />
    <x-home.milestones />
    <!-- <x-home.services /> -->
    <x-home.process />
    <x-home.projects />
    <x-home.testimonials />
    <x-home.news />
    <x-home.cta />
    <x-home.partners />
</x-layouts.app>
