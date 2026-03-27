@php
/** @var string $slug */
$allDocs = [
    'engineering-standards-2026' => [
        'slug' => 'engineering-standards-2026', 'category' => 'Engineering', 'type' => 'PDF', 'size' => '15.4 MB',
        'badge' => 'FEATURED', 'title' => 'Kimmex Engineering Standards 2026: High-Rise Construction',
        'desc' => 'Comprehensive engineering standards covering structural design, load calculations, and quality requirements for high-rise construction projects in Cambodia.',
        'date' => 'March 2026', 'image' => '/images/projects/Thumbnail-1.jpg',
        'pages' => 248, 'version' => 'v4.2', 'language' => 'English',
        'highlights' => [
            'Structural load calculations for buildings over 20 stories',
            'Seismic design requirements for the Cambodian climate zone',
            'Material quality standards for reinforced concrete',
            'Foundation design and pile testing protocols',
            'Quality inspection checklists and sign-off procedures',
        ],
        'related' => ['site-safety-risk-management', 'mep-technical-specifications'],
    ],
    'site-safety-risk-management' => [
        'slug' => 'site-safety-risk-management', 'category' => 'Safety', 'type' => 'PDF', 'size' => '4.2 MB',
        'badge' => null, 'title' => 'Site Safety & Risk Management Handbook',
        'desc' => 'Complete safety protocols, PPE requirements, and emergency response procedures for all active construction sites.',
        'date' => 'February 2026', 'image' => '/images/projects/Thumbnail-2.jpg',
        'pages' => 96, 'version' => 'v2.1', 'language' => 'English / Khmer',
        'highlights' => [
            'Personal Protective Equipment (PPE) requirements by zone',
            'Daily site safety inspection checklists',
            'Emergency response and evacuation procedures',
            'Incident reporting and escalation protocols',
            'Subcontractor safety onboarding guidelines',
        ],
        'related' => ['engineering-standards-2026', 'company-profile-2026'],
    ],
    'company-profile-2026' => [
        'slug' => 'company-profile-2026', 'category' => 'Corporate', 'type' => 'PDF', 'size' => '3.5 MB',
        'badge' => null, 'title' => 'Kimmex Company Profile 2026',
        'desc' => 'Full corporate profile including company history, leadership, core services, and a showcase of landmark projects delivered.',
        'date' => 'January 2026', 'image' => '/images/projects/Thumbnail-3.jpg',
        'pages' => 64, 'version' => 'v1.0', 'language' => 'English / Khmer',
        'highlights' => [
            'Company history and founding milestones since 1998',
            'Leadership team and organizational structure',
            'Complete service offerings overview',
            'Portfolio of 120+ completed projects',
            'Certifications and industry partnerships',
        ],
        'related' => ['mef-project-case-study', 'sustainable-construction-sea'],
    ],
    'mep-technical-specifications' => [
        'slug' => 'mep-technical-specifications', 'category' => 'Technical', 'type' => 'PDF', 'size' => '8.1 MB',
        'badge' => null, 'title' => 'MEP Systems Technical Specifications',
        'desc' => 'Detailed technical specifications for Mechanical, Electrical, and Plumbing systems integration across all project types.',
        'date' => 'January 2026', 'image' => '/images/projects/Thumbnail-4.jpg',
        'pages' => 180, 'version' => 'v3.0', 'language' => 'English',
        'highlights' => [
            'HVAC system design standards and load calculations',
            'Electrical panel sizing and distribution requirements',
            'Plumbing fixture specifications and pressure ratings',
            'Fire protection and suppression system standards',
            'BMS integration and smart building requirements',
        ],
        'related' => ['engineering-standards-2026', 'site-safety-risk-management'],
    ],
    'sustainable-construction-sea' => [
        'slug' => 'sustainable-construction-sea', 'category' => 'Research', 'type' => 'PDF', 'size' => '6.8 MB',
        'badge' => null, 'title' => 'Sustainable Construction Practices in Southeast Asia',
        'desc' => 'Research paper on eco-friendly materials, energy-efficient designs, and green building certifications.',
        'date' => 'December 2025', 'image' => '/images/projects/Thumbnail-5.jpg',
        'pages' => 112, 'version' => 'v1.2', 'language' => 'English',
        'highlights' => [
            'Overview of LEED and EDGE certification pathways',
            'Locally sourced sustainable material analysis',
            'Passive cooling strategies for tropical climates',
            'Rainwater harvesting and greywater recycling systems',
            'Carbon footprint reduction benchmarks in construction',
        ],
        'related' => ['company-profile-2026', 'mef-project-case-study'],
    ],
    'mef-project-case-study' => [
        'slug' => 'mef-project-case-study', 'category' => 'Case Study', 'type' => 'PDF', 'size' => '12.5 MB',
        'badge' => null, 'title' => 'Ministry of Economy & Finance — Project Case Study',
        'desc' => 'Detailed case study covering the design, construction, and delivery of the MEF government office complex in Phnom Penh.',
        'date' => 'November 2025', 'image' => '/images/projects/Thumbnail-1.jpg',
        'pages' => 76, 'version' => 'v1.0', 'language' => 'English / Khmer',
        'highlights' => [
            'Project scope: 50,000 sqm government office complex',
            'Design-build delivery methodology overview',
            'Challenges encountered and engineering solutions applied',
            'Timeline: 36-month delivery from groundbreaking to handover',
            'Client testimonial and post-occupancy review',
        ],
        'related' => ['engineering-standards-2026', 'company-profile-2026'],
    ],
];

$doc = $allDocs[$slug] ?? null;
if (!$doc) abort(404);

$relatedDocs = array_filter($allDocs, fn($d) => in_array($d['slug'], $doc['related']));
@endphp

<x-layouts.app :title="$doc['title']" :description="$doc['desc']">

<div class="min-h-screen bg-white text-titan-navy">

    <!-- DARK HERO -->
    <section class="relative bg-titan-navy pt-[120px] pb-20 px-6 overflow-hidden">
        <div class="absolute inset-0 opacity-30">
            <img src="{{ $doc['image'] }}" class="w-full h-full object-cover" alt="" />
            <div class="absolute inset-0 bg-gradient-to-r from-titan-navy via-titan-navy/90 to-titan-navy/50"></div>
        </div>

        <div class="relative z-10 max-w-[1200px] mx-auto">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-3 text-[11px] font-bold uppercase tracking-widest text-white/30 mb-10">
                <a href="/documents" class="hover:text-accent-orange transition-colors">{{ __('Documents') }}</a>
                <span class="w-1 h-1 rounded-full bg-white/20"></span>
                <span class="text-white/50">{{ $doc['category'] }}</span>
            </nav>

            <div class="flex flex-col lg:flex-row gap-16 items-start">
                <!-- Left -->
                <div class="flex-1">
                    @if($doc['badge'])
                        <div class="inline-flex items-center gap-2 bg-accent-orange text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg mb-6">
                            <x-lucide-star class="w-3 h-3" />
                            {{ $doc['badge'] }}
                        </div>
                    @endif
                    <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 text-white/50 text-[11px] font-bold uppercase tracking-widest px-4 py-2 rounded-lg mb-6 {{ $doc['badge'] ? 'ml-3' : '' }}">
                        {{ $doc['category'] }}
                    </div>

                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tighter mb-6 max-w-2xl">
                        {{ $doc['title'] }}
                    </h1>
                    <p class="text-white/50 text-base leading-relaxed max-w-xl mb-10">{{ $doc['desc'] }}</p>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="/docs/{{ $doc['slug'] }}.pdf" download class="inline-flex items-center gap-3 bg-accent-orange hover:bg-white hover:text-titan-navy text-white px-8 py-4 rounded-xl font-black text-sm uppercase tracking-widest transition-all duration-300 shadow-lg">
                            <x-lucide-download class="w-4 h-4" />
                            {{ __('Download PDF') }}
                        </a>
                        <a href="/documents" class="inline-flex items-center gap-3 bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white px-6 py-4 rounded-xl font-bold text-sm uppercase tracking-widest transition-all duration-300">
                            <x-lucide-arrow-left class="w-4 h-4" />
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>

                <!-- Right: Meta card -->
                <div class="lg:w-72 shrink-0 bg-white/5 border border-white/10 rounded-2xl p-8 backdrop-blur-sm">
                    <h3 class="text-[10px] font-black text-white/30 uppercase tracking-[0.4em] mb-6">{{ __('Document Info') }}</h3>
                    <div class="space-y-5">
                        @foreach([
                            ['icon' => 'lucide-file-text', 'label' => 'Type', 'value' => $doc['type']],
                            ['icon' => 'lucide-hard-drive', 'label' => 'File Size', 'value' => $doc['size']],
                            ['icon' => 'lucide-book-open', 'label' => 'Pages', 'value' => $doc['pages'] . ' pages'],
                            ['icon' => 'lucide-tag', 'label' => 'Version', 'value' => $doc['version']],
                            ['icon' => 'lucide-globe', 'label' => 'Language', 'value' => $doc['language']],
                            ['icon' => 'lucide-calendar', 'label' => 'Published', 'value' => $doc['date']],
                        ] as $meta)
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0">
                                    <x-dynamic-component :component="$meta['icon']" class="w-3.5 h-3.5 text-accent-orange" />
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-white/25 uppercase tracking-wider">{{ __($meta['label']) }}</div>
                                    <div class="text-sm font-bold text-white">{{ $meta['value'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HIGHLIGHTS SECTION -->
    <section class="py-20 px-6 max-w-[1200px] mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Content highlights -->
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-8">
                    <span class="w-6 h-[2px] bg-accent-orange"></span>
                    <span class="text-[11px] font-black uppercase tracking-[0.35em] text-titan-navy/40">{{ __('What\'s Inside') }}</span>
                </div>
                <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tight mb-8">{{ __('Document Highlights') }}</h2>

                <div class="space-y-4">
                    @foreach($doc['highlights'] as $i => $highlight)
                        <div class="flex items-start gap-5 p-5 rounded-xl border border-gray-100 hover:border-accent-orange/20 hover:bg-gray-50/50 transition-all duration-300 group">
                            <div class="w-8 h-8 rounded-full bg-accent-orange/5 text-accent-orange flex items-center justify-center text-[11px] font-black shrink-0 group-hover:bg-accent-orange group-hover:text-white transition-all duration-300">
                                0{{ $i + 1 }}
                            </div>
                            <p class="text-sm text-titan-navy/60 leading-relaxed group-hover:text-titan-navy transition-colors font-medium pt-1">{{ $highlight }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- Download CTA box -->
                <div class="mt-10 p-8 rounded-2xl bg-titan-navy flex flex-col sm:flex-row items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-accent-orange/10 rounded-full blur-[60px] pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="text-[10px] font-black text-accent-orange uppercase tracking-[0.4em] mb-2">{{ __('Free Download') }}</div>
                        <p class="text-white font-bold text-sm max-w-xs">{{ __('Get immediate access to this document in PDF format.') }}</p>
                    </div>
                    <a href="/docs/{{ $doc['slug'] }}.pdf" download class="relative z-10 shrink-0 inline-flex items-center gap-3 bg-accent-orange hover:bg-white hover:text-titan-navy text-white px-7 py-3.5 rounded-xl font-black text-sm uppercase tracking-widest transition-all duration-300">
                        <x-lucide-download class="w-4 h-4" />
                        {{ __('Download Now') }}
                    </a>
                </div>
            </div>

            <!-- Preview image -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="w-6 h-[2px] bg-accent-orange"></span>
                        <span class="text-[11px] font-black uppercase tracking-[0.35em] text-titan-navy/40">{{ __('Preview') }}</span>
                    </div>
                    <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-lg aspect-[3/4] bg-gray-50 relative group">
                        <img src="{{ $doc['image'] }}" alt="{{ $doc['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[10s]" />
                        <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/60 to-transparent flex items-end p-6">
                            <div class="text-white text-xs font-bold uppercase tracking-widest opacity-80">{{ $doc['type'] }} · {{ $doc['pages'] }} {{ __('pages') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- RELATED DOCUMENTS -->
    @if(count($relatedDocs) > 0)
        <section class="bg-gray-50 py-16 px-6">
            <div class="max-w-[1200px] mx-auto">
                <div class="flex items-center gap-3 mb-10">
                    <span class="w-6 h-[2px] bg-accent-orange"></span>
                    <span class="text-[11px] font-black uppercase tracking-[0.35em] text-titan-navy/40">{{ __('Also Relevant') }}</span>
                </div>
                <h2 class="text-xl font-black text-titan-navy uppercase tracking-tight mb-8">{{ __('Related Documents') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($relatedDocs as $rel)
                        <a href="/documents/{{ $rel['slug'] }}" class="group flex items-start gap-5 p-6 bg-white rounded-2xl border border-gray-100 hover:border-accent-orange/20 hover:shadow-md transition-all duration-300">
                            <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 bg-gray-100">
                                <img src="{{ $rel['image'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-black text-accent-orange uppercase tracking-widest mb-1">{{ $rel['category'] }}</div>
                                <h4 class="font-bold text-sm text-titan-navy group-hover:text-accent-orange transition-colors leading-snug truncate">{{ $rel['title'] }}</h4>
                                <div class="text-[11px] text-titan-navy/30 mt-1">{{ $rel['type'] }} · {{ $rel['size'] }}</div>
                            </div>
                            <x-lucide-arrow-right class="w-4 h-4 text-titan-navy/20 group-hover:text-accent-orange group-hover:translate-x-1 transition-all duration-300 shrink-0 mt-1" />
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</div>

</x-layouts.app>
