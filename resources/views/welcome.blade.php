<x-layouts.app title="Home"
    :priority-image="$priorityHeroImage"
    :priority-image-srcset="$priorityHeroImageSrcset"
    description="Kimmex is a leading construction and engineering company in Cambodia delivering high-quality building and management solutions.">

    @push('head')
    <script type="application/ld+json">
    {!! json_encode([
        '@@context' => 'https://schema.org',
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

    <x-home.hero-carousel :slides="$heroSlides" />
    <x-home.trust-strip />
    <x-home.about :about-data="$aboutData" />
    <x-home.milestones :milestones-data="$milestonesData" />
    <x-home.process :processes="$processes" />
    <x-home.projects :projects="$projects" />
    <x-home.testimonials :testimonials="$testimonials" />
    <x-home.news :all-news="$allNews" />
    <x-home.cta />
    <x-home.partners :partners="$partners" />
</x-layouts.app>
