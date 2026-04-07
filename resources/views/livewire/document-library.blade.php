<div class="min-h-screen bg-white text-titan-navy">

    <!-- === HERO / HEADER SECTION === -->
    <section class="relative bg-titan-navy pt-[140px] pb-24 px-6 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[radial-gradient(#ffffff15_1px,transparent_1px)] [background-size:20px_20px] opacity-20"></div>
        <!-- Decorative Glow -->
        <div class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 bg-accent-orange/20 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="max-w-[1200px] mx-auto relative z-10">
            <!-- Student Research Hub Badge -->
            <div
                class="inline-flex items-center gap-2 bg-white/5 border border-white/10 backdrop-blur-md rounded-full px-5 py-2.5 mb-10 shadow-xl">
                <x-lucide-award class="w-4 h-4 text-accent-orange" />
                <span
                    class="text-[11px] font-black uppercase tracking-[0.25em] text-white/90">{{ __('Kimmex Knowledge Hub') }}</span>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-12">
                <!-- Title + Desc -->
                <div class="max-w-2xl">
                    <h1 class="font-black text-white uppercase leading-[0.9] tracking-tighter mb-6"
                        style="font-size: clamp(3.5rem, 8vw, 7rem);">
                        {{ __('DOC') }}<span class="text-accent-orange">.</span><br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-gray-300 to-white">{{ __('COLLECTION') }}</span>
                    </h1>
                    <p class="text-white/60 text-lg leading-relaxed max-w-lg font-medium">
                        {{ __('Access our centralized repository of engineering standards, research papers, case studies, and corporate resources.') }}
                    </p>
                </div>

                <!-- Stats -->
                <div class="flex items-end gap-10 shrink-0 bg-white/5 p-8 rounded-3xl backdrop-blur-sm border border-white/10">
                    <div>
                        <div class="text-6xl font-black text-accent-orange leading-none mb-2">{{ $totalDocuments }}<span class="text-white/30">+</span></div>
                        <div class="text-[11px] font-black uppercase tracking-[0.25em] text-white/50">
                            {{ __('Documents') }}
                        </div>
                    </div>
                    <div class="w-px h-16 bg-white/10"></div>
                    <div>
                        <div class="text-6xl font-black text-white leading-none mb-2">{{ $totalCategories }}</div>
                        <div class="text-[11px] font-black uppercase tracking-[0.25em] text-white/50">
                            {{ __('Categories') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- === RESEARCH RESOURCES === -->
    <section class="bg-gradient-to-b from-gray-50 to-white py-20 px-6">
        <div class="max-w-[1200px] mx-auto">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 mb-4">
                    <div class="w-8 h-px bg-accent-orange"></div>
                    <span class="text-[10px] font-black text-accent-orange uppercase tracking-[0.4em]">{{ __('Knowledge Library') }}</span>
                    <div class="w-8 h-px bg-accent-orange"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-titan-navy uppercase tracking-tighter">
                    {{ __('Research') }} <span class="text-accent-orange">{{ __('Resources') }}</span>
                </h2>
                <p class="text-titan-navy/40 text-sm mt-4 max-w-2xl mx-auto leading-relaxed">
                    {{ __('Kimmex is committed to advancing engineering education through open access to real-world project data, technical standards, and learning materials.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Case Studies -->
                <div class="group bg-white rounded-2xl border border-gray-100 p-8 hover:border-accent-orange/30 hover:shadow-lg transition-all duration-500 hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-xl bg-accent-orange/10 flex items-center justify-center mb-6 group-hover:bg-accent-orange/20 transition-colors">
                        <x-lucide-book-open class="w-6 h-6 text-accent-orange" />
                    </div>
                    <h3 class="text-sm font-black text-titan-navy uppercase tracking-tight mb-2">{{ __('Case Studies') }}</h3>
                    <p class="text-xs text-titan-navy/40 leading-relaxed mb-4">{{ __('Deep dives into real project challenges, solutions, and engineering outcomes.') }}</p>
                    <button wire:click="setTab('Case Study')" class="text-[10px] font-bold text-accent-orange uppercase tracking-widest hover:underline">{{ __('Browse Case Studies') }} →</button>
                </div>

                <!-- Engineering Standards -->
                <div class="group bg-white rounded-2xl border border-gray-100 p-8 hover:border-accent-orange/30 hover:shadow-lg transition-all duration-500 hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-xl bg-titan-navy/5 flex items-center justify-center mb-6 group-hover:bg-titan-navy/10 transition-colors">
                        <x-lucide-ruler class="w-6 h-6 text-titan-navy" />
                    </div>
                    <h3 class="text-sm font-black text-titan-navy uppercase tracking-tight mb-2">{{ __('Engineering Standards') }}</h3>
                    <p class="text-xs text-titan-navy/40 leading-relaxed mb-4">{{ __('SOPs, structural guidelines, and quality standards for educational reference.') }}</p>
                    <button wire:click="setTab('Engineering')" class="text-[10px] font-bold text-titan-navy/50 uppercase tracking-widest hover:text-accent-orange hover:underline transition-colors">{{ __('View Standards') }} →</button>
                </div>

                <!-- Safety Manuals -->
                <div class="group bg-white rounded-2xl border border-gray-100 p-8 hover:border-accent-orange/30 hover:shadow-lg transition-all duration-500 hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center mb-6 group-hover:bg-green-100 transition-colors">
                        <x-lucide-shield-check class="w-6 h-6 text-green-600" />
                    </div>
                    <h3 class="text-sm font-black text-titan-navy uppercase tracking-tight mb-2">{{ __('Safety Manuals') }}</h3>
                    <p class="text-xs text-titan-navy/40 leading-relaxed mb-4">{{ __('Standard safety protocols, PPE requirements, and emergency procedures.') }}</p>
                    <button wire:click="setTab('Safety')" class="text-[10px] font-bold text-green-600 uppercase tracking-widest hover:underline">{{ __('Read Manuals') }} →</button>
                </div>

                <!-- Internship Program -->
                <div class="group bg-white rounded-2xl border border-gray-100 p-8 hover:border-accent-orange/30 hover:shadow-lg transition-all duration-500 hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center mb-6 group-hover:bg-blue-100 transition-colors">
                        <x-lucide-briefcase class="w-6 h-6 text-blue-600" />
                    </div>
                    <h3 class="text-sm font-black text-titan-navy uppercase tracking-tight mb-2">{{ __('Internship Program') }}</h3>
                    <p class="text-xs text-titan-navy/40 leading-relaxed mb-4">{{ __('Hands-on learning opportunities for engineering students at active job sites.') }}</p>
                    <a href="/contact" class="text-[10px] font-bold text-blue-600 uppercase tracking-widest hover:underline">{{ __('Apply Now') }} →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- === FILTER + SEARCH BAR === -->
    <section class="sticky top-0 z-30 bg-white border-b border-gray-100 shadow-sm px-6">
        <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row items-stretch md:items-center gap-0">
            <!-- Tabs -->
            <div class="flex items-center gap-1 flex-wrap flex-1 py-3" wire:ignore.self>
                @foreach($tabs as $tab)
                    <button wire:click="setTab('{{ $tab }}')" 
                        class="px-4 py-2 rounded-lg text-[12px] font-bold uppercase tracking-wider transition-all duration-200 whitespace-nowrap {{ $activeTab === $tab ? 'bg-titan-navy text-white' : 'text-titan-navy/40 hover:text-titan-navy hover:bg-gray-100' }}">
                        {{ __($tab) }}
                    </button>
                @endforeach
            </div>

            <!-- Divider -->
            <div class="hidden md:block w-px h-10 bg-gray-100 mx-4 shrink-0"></div>

            <!-- Search -->
            <div class="relative py-3 md:w-64 shrink-0">
                <x-lucide-search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-titan-navy/25" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by title or keyword...') }}"
                    class="w-full bg-gray-50 border border-gray-100 rounded-xl pl-10 pr-4 py-2.5 text-sm text-titan-navy placeholder:text-titan-navy/25 focus:outline-none focus:ring-2 focus:ring-accent-orange/20 focus:border-accent-orange/30 transition-all" />
            </div>
        </div>
    </section>

    <!-- === DOCUMENT LIST === -->
    <section class="max-w-[1200px] mx-auto px-6 py-16 space-y-8 relative">
        <!-- Loading Overlay -->
        <div wire:loading class="absolute inset-0 z-10 bg-white/50 backdrop-blur-sm rounded-xl flex items-center justify-center">
            <div class="w-8 h-8 border-4 border-accent-orange border-t-transparent rounded-full animate-spin"></div>
        </div>

        @forelse($documents as $doc)
            <div>
                <!-- Section Label (Latest Release, etc) -->
                @if($doc->is_featured && $loop->first)
                    <div class="flex items-center gap-2 mb-5">
                        <span class="w-2 h-2 bg-accent-orange rounded-full inline-block"></span>
                        <span class="text-[11px] font-black uppercase tracking-[0.35em] text-titan-navy/50">
                            {{ __('LATEST RELEASE') }}
                        </span>
                    </div>
                @endif

                <!-- Card -->
                <a href="/documents/{{ $doc->slug }}"
                    class="cursor-pointer group flex flex-col md:flex-row rounded-2xl border border-gray-100 overflow-hidden hover:border-accent-orange/20 hover:shadow-lg transition-all duration-500 bg-white">

                    <!-- Image Side -->
                    <div class="relative w-full md:w-80 lg:w-96 min-h-[220px] md:min-h-full shrink-0 overflow-hidden bg-gray-50 flex flex-col justify-center">
                        @if($doc->thumbnailUrl)
                            <img src="{{ Storage::url($doc->thumbnailUrl) }}" alt="{{ $doc->title }}"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-[10s]" />
                        @else
                            <div class="absolute inset-0 w-full h-full bg-titan-navy/[0.03] flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-[radial-gradient(#00000010_1px,transparent_1px)] [background-size:10px_10px]"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-100/50 to-transparent"></div>
                                <x-lucide-file-text class="w-16 h-16 text-titan-navy/20 group-hover:scale-110 group-hover:text-accent-orange transition-all duration-700 relative z-10 drop-shadow-sm" />
                            </div>
                        @endif

                        <!-- Badge -->
                        @if($doc->is_featured)
                            <div class="absolute top-4 left-4 z-20">
                                <span class="bg-accent-orange text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg shadow-sm">
                                    {{ __('FEATURED') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Content Side -->
                    <div class="flex flex-col justify-between p-8 lg:p-10 flex-1">
                        <!-- Meta -->
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-[11px] font-black uppercase tracking-widest text-accent-orange bg-accent-orange/5 px-3 py-1.5 rounded-lg">
                                    {{ $doc->documentCategory ? $doc->documentCategory->name : $doc->category }}
                                </span>
                                <span class="text-[11px] text-titan-navy/30 font-bold">|</span>
                                <span class="text-[11px] font-bold text-titan-navy/30 uppercase tracking-wider">
                                    {{ $doc->fileType ?? 'PDF' }} {{ $doc->fileSize ? '· '.$doc->fileSize : '' }}
                                </span>
                            </div>

                            <h2 class="text-xl lg:text-2xl font-black text-titan-navy mb-4 group-hover:text-accent-orange transition-colors duration-300 leading-tight">
                                {{ $doc->title }}
                            </h2>
                            <p class="text-sm text-titan-navy/50 leading-relaxed max-w-3xl">
                                {{ str($doc->description)->limit(150) }}
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-50">
                            <div class="flex items-center gap-2">
                                <x-lucide-calendar class="w-3.5 h-3.5 text-titan-navy/25" />
                                <span class="text-[11px] font-bold text-titan-navy/30 uppercase tracking-wider">
                                    {{ $doc->created_at->format('F Y') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                @if($doc->fileUrl)
                                    <a href="{{ Storage::url($doc->fileUrl) }}" download
                                        class="inline-flex items-center justify-center w-10 h-10 bg-gray-50 hover:bg-accent-orange text-titan-navy hover:text-white rounded-xl transition-all duration-300"
                                        title="{{ __('Download') }}">
                                        <x-lucide-download class="w-4 h-4" />
                                    </a>
                                @endif
                                <div class="inline-flex items-center gap-2 bg-titan-navy group-hover:bg-accent-orange text-white text-[12px] font-black uppercase tracking-wider px-5 py-2.5 rounded-xl transition-all duration-300">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                    {{ __('View Detail') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <!-- Empty State -->
            <div class="text-center py-24">
                <x-lucide-file-x class="w-12 h-12 text-titan-navy/10 mx-auto mb-4" />
                <p class="text-titan-navy/30 font-bold text-sm uppercase tracking-widest">{{ __('No documents found') }}</p>
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="mt-8">
            {{ $documents->links() }}
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
