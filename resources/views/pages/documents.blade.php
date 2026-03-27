<x-layouts.app title="Doc Collection"
    description="Access our centralized repository of engineering standards, research papers, technical specifications, and corporate policies.">

    @php
        $documents = [
            [
                'slug' => 'engineering-standards-2026',
                'category' => 'Engineering',
                'type' => 'PDF',
                'size' => '15.4 MB',
                'featured' => true,
                'badge' => 'FEATURED',
                'label' => 'LATEST RELEASE',
                'title' => 'Kimmex Engineering Standards 2026: High-Rise Construction',
                'desc' => 'Comprehensive engineering standards covering structural design, load calculations, and quality requirements for high-rise construction projects in Cambodia.',
                'date' => 'March 2026',
                'image' => '/images/projects/Thumbnail-1.jpg',
            ],
            [
                'slug' => 'site-safety-risk-management',
                'category' => 'Safety',
                'type' => 'PDF',
                'size' => '4.2 MB',
                'featured' => false,
                'badge' => null,
                'label' => null,
                'title' => 'Site Safety & Risk Management Handbook',
                'desc' => 'Complete safety protocols, PPE requirements, and emergency response procedures for all active construction sites.',
                'date' => 'February 2026',
                'image' => '/images/projects/Thumbnail-2.jpg',
            ],
            [
                'slug' => 'company-profile-2026',
                'category' => 'Corporate',
                'type' => 'PDF',
                'size' => '3.5 MB',
                'featured' => false,
                'badge' => null,
                'label' => null,
                'title' => 'Kimmex Company Profile 2026',
                'desc' => 'Full corporate profile including company history, leadership, core services, and a showcase of landmark projects delivered.',
                'date' => 'January 2026',
                'image' => '/images/projects/Thumbnail-3.jpg',
            ],
            [
                'slug' => 'mep-technical-specifications',
                'category' => 'Technical',
                'type' => 'PDF',
                'size' => '8.1 MB',
                'featured' => false,
                'badge' => null,
                'label' => null,
                'title' => 'MEP Systems Technical Specifications',
                'desc' => 'Detailed technical specifications for Mechanical, Electrical, and Plumbing systems integration across all project types.',
                'date' => 'January 2026',
                'image' => '/images/projects/Thumbnail-4.jpg',
            ],
            [
                'slug' => 'sustainable-construction-sea',
                'category' => 'Research',
                'type' => 'PDF',
                'size' => '6.8 MB',
                'featured' => false,
                'badge' => null,
                'label' => null,
                'title' => 'Sustainable Construction Practices in Southeast Asia',
                'desc' => 'Research paper on eco-friendly materials, energy-efficient designs, and green building certifications applicable to the Cambodian market.',
                'date' => 'December 2025',
                'image' => '/images/projects/Thumbnail-5.jpg',
            ],
            [
                'slug' => 'mef-project-case-study',
                'category' => 'Case Study',
                'type' => 'PDF',
                'size' => '12.5 MB',
                'featured' => false,
                'badge' => null,
                'label' => null,
                'title' => 'Ministry of Economy & Finance — Project Case Study',
                'desc' => 'Detailed case study covering the design, construction, and delivery of the MEF government office complex in Phnom Penh.',
                'date' => 'November 2025',
                'image' => '/images/projects/Thumbnail-1.jpg',
            ],
        ];
        $tabs = ['All Types', 'Engineering', 'Safety', 'Research', 'Corporate', 'Technical', 'Case Study'];
    @endphp

    <div class="min-h-screen bg-white text-titan-navy" x-data="{
        activeTab: 'All Types',
        search: '',
        get filtered() {
            return {{ json_encode($documents) }}.filter(d => {
                const matchTab = this.activeTab === 'All Types' || d.category === this.activeTab;
                const matchSearch = this.search === '' || d.title.toLowerCase().includes(this.search.toLowerCase());
                return matchTab && matchSearch;
            });
        }
    }">

        <!-- === HERO / HEADER SECTION === -->
        <section class="bg-titan-navy pt-[120px] pb-16 px-6">
            <div class="max-w-[1200px] mx-auto">

                <!-- Resource Center Badge -->
                <div
                    class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-2 mb-8">
                    <x-lucide-library class="w-3.5 h-3.5 text-white/50" />
                    <span
                        class="text-[11px] font-bold uppercase tracking-[0.25em] text-white/60">{{ __('Resource Center') }}</span>
                </div>

                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10">
                    <!-- Title + Desc -->
                    <div class="max-w-2xl">
                        <h1 class="font-black text-white uppercase leading-none tracking-tighter mb-5"
                            style="font-size: clamp(3rem, 8vw, 6rem);">
                            {{ __('DOC') }} <span class="text-accent-orange">{{ __('COLLECTION') }}</span>
                        </h1>
                        <p class="text-white/40 text-base leading-relaxed max-w-lg">
                            {{ __('Access our centralized repository of engineering standards, research papers, technical specifications, and corporate policies.') }}
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-end gap-12 shrink-0">
                        <div>
                            <div class="text-5xl font-black text-white leading-none mb-1">120+</div>
                            <div class="text-[11px] font-bold uppercase tracking-[0.25em] text-white/30">
                                {{ __('Documents') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-5xl font-black text-white leading-none mb-1">15</div>
                            <div class="text-[11px] font-bold uppercase tracking-[0.25em] text-white/30">
                                {{ __('Categories') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- === FILTER + SEARCH BAR === -->
        <section class="sticky top-0 z-30 bg-white border-b border-gray-100 shadow-sm px-6">
            <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row items-stretch md:items-center gap-0">
                <!-- Tabs -->
                <div class="flex items-center gap-1 flex-wrap flex-1 py-3">
                    @foreach($tabs as $tab)
                        <button @click="activeTab = '{{ $tab }}'" :class="activeTab === '{{ $tab }}'
                                        ? 'bg-titan-navy text-white'
                                        : 'text-titan-navy/40 hover:text-titan-navy hover:bg-gray-100'"
                            class="px-4 py-2 rounded-lg text-[12px] font-bold uppercase tracking-wider transition-all duration-200 whitespace-nowrap">
                            {{ __($tab) }}
                        </button>
                    @endforeach
                </div>

                <!-- Divider -->
                <div class="hidden md:block w-px h-10 bg-gray-100 mx-4 shrink-0"></div>

                <!-- Search -->
                <div class="relative py-3 md:w-64 shrink-0">
                    <x-lucide-search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-titan-navy/25" />
                    <input type="text" x-model="search" placeholder="{{ __('Search by title or keyword...') }}"
                        class="w-full bg-gray-50 border border-gray-100 rounded-xl pl-10 pr-4 py-2.5 text-sm text-titan-navy placeholder:text-titan-navy/25 focus:outline-none focus:ring-2 focus:ring-accent-orange/20 focus:border-accent-orange/30 transition-all" />
                </div>
            </div>
        </section>

        <!-- === DOCUMENT LIST === -->
        <section class="max-w-[1200px] mx-auto px-6 py-16 space-y-8">

            <template x-for="(doc, i) in filtered" :key="i">
                <div>
                    <!-- Section Label (Latest Release, etc) -->
                    <div x-show="doc.label" class="flex items-center gap-2 mb-5">
                        <span class="w-2 h-2 bg-accent-orange rounded-full inline-block"></span>
                        <span class="text-[11px] font-black uppercase tracking-[0.35em] text-titan-navy/50"
                            x-text="doc.label"></span>
                    </div>

                    <!-- Card -->
                    <div @click="window.location.href = '/documents/' + doc.slug"
                        class="cursor-pointer group flex flex-col md:flex-row rounded-2xl border border-gray-100 overflow-hidden hover:border-accent-orange/20 hover:shadow-lg transition-all duration-500 bg-white">

                        <!-- Image Side -->
                        <div class="relative md:w-80 lg:w-96 h-56 md:h-auto shrink-0 overflow-hidden">
                            <img :src="doc.image" :alt="doc.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[10s]" />
                            <!-- Badge -->
                            <div x-show="doc.badge" class="absolute top-4 left-4">
                                <span
                                    class="bg-accent-orange text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg"
                                    x-text="doc.badge"></span>
                            </div>
                        </div>

                        <!-- Content Side -->
                        <div class="flex flex-col justify-between p-8 lg:p-10 flex-1">
                            <!-- Meta -->
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <span
                                        class="text-[11px] font-black uppercase tracking-widest text-accent-orange bg-accent-orange/5 px-3 py-1.5 rounded-lg"
                                        x-text="doc.category"></span>
                                    <span class="text-[11px] text-titan-navy/30 font-bold">|</span>
                                    <span class="text-[11px] font-bold text-titan-navy/30 uppercase tracking-wider"
                                        x-text="doc.type + ' · ' + doc.size"></span>
                                </div>

                                <h2 class="text-xl lg:text-2xl font-black text-titan-navy mb-4 group-hover:text-accent-orange transition-colors duration-300 leading-tight"
                                    x-text="doc.title"></h2>
                                <p class="text-sm text-titan-navy/50 leading-relaxed" x-text="doc.desc"></p>
                            </div>

                            <!-- Footer -->
                            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-50">
                                <div class="flex items-center gap-2">
                                    <x-lucide-calendar class="w-3.5 h-3.5 text-titan-navy/25" />
                                    <span class="text-[11px] font-bold text-titan-navy/30 uppercase tracking-wider"
                                        x-text="doc.date"></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a :href="'/docs/' + doc.slug + '.pdf'" download @click.stop
                                        class="inline-flex items-center justify-center w-10 h-10 bg-gray-50 hover:bg-accent-orange text-titan-navy hover:text-white rounded-xl transition-all duration-300"
                                        title="{{ __('Download') }}">
                                        <x-lucide-download class="w-4 h-4" />
                                    </a>
                                    <div
                                        class="inline-flex items-center gap-2 bg-titan-navy group-hover:bg-accent-orange text-white text-[12px] font-black uppercase tracking-wider px-5 py-2.5 rounded-xl transition-all duration-300">
                                        <x-lucide-eye class="w-3.5 h-3.5" />
                                        {{ __('View Detail') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="filtered.length === 0" class="text-center py-24">
                <x-lucide-file-x class="w-12 h-12 text-titan-navy/10 mx-auto mb-4" />
                <p class="text-titan-navy/30 font-bold text-sm uppercase tracking-widest">{{ __('No documents found') }}
                </p>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-titan-navy py-16 px-6">
            <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <div class="text-[10px] font-black text-accent-orange uppercase tracking-[0.4em] mb-3">
                        {{ __('Need More?') }}
                    </div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-2">
                        {{ __("Can't Find What You're Looking For?") }}
                    </h3>
                    <p class="text-white/40 text-sm max-w-md leading-relaxed">
                        {{ __('Contact us directly and our team will prepare the relevant documents for you.') }}
                    </p>
                </div>
                <a href="/contact"
                    class="shrink-0 inline-flex items-center gap-3 bg-accent-orange hover:bg-white hover:text-titan-navy text-white px-8 py-4 rounded-xl font-black text-sm uppercase tracking-widest transition-all duration-300 shadow-lg">
                    <x-lucide-mail class="w-4 h-4" />
                    {{ __('Contact Us') }}
                </a>
            </div>
        </section>

    </div>

</x-layouts.app>