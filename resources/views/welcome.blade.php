<x-layouts.app title="Home"
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
    <x-home.about />
    <x-home.services />
    <x-home.process />
    <x-home.projects />
    <x-home.testimonials />
    <x-home.news />
    <x-home.cta />
    <x-home.partners />
</x-layouts.app>