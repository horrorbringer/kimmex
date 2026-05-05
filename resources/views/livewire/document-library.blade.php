<div class="min-h-screen bg-white text-titan-navy">

    <!-- === PREMIUM KNOWLEDGE HUB HERO === -->
    <section class="relative h-[65vh] min-h-[550px] flex items-center overflow-hidden bg-titan-navy shadow-2xl">
        {{-- Background Zoom Animation --}}
        <div class="absolute inset-0">
            <img src="{{ asset('images/heroes/documents-bg.png') }}" alt="Knowledge Hub" class="w-full h-full object-cover opacity-100 animate-slow-zoom" />
            {{-- Deep multi-stage gradient for maximum text contrast --}}
            <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/60 via-transparent to-titan-navy/90"></div>
            <div class="absolute inset-0 bg-black/20"></div>
        </div>

        <div class="max-w-[1240px] mx-auto w-full px-6 relative z-20" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">
            <!-- Premium Glassmorphism Badge -->
            <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-8'"
                class="transition-all duration-1000 delay-100 inline-flex items-center gap-3 glass-premium px-6 py-3 mb-12 rounded-full">
                <x-lucide-award class="w-4 h-4 text-titan-red animate-pulse" />
                <span
                    class="text-[10px] font-black uppercase tracking-[0.3em] text-white/90">{{ __('Kimmex Knowledge Hub') }}</span>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-12">
                <!-- Title + Desc -->
                <div class="max-w-3xl">
                    <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                        class="transition-all duration-1000 delay-300 font-black text-white uppercase leading-[0.9] tracking-tighter mb-8"
                        style="font-size: clamp(3.5rem, 8vw, 6.5rem);">
                        {{ __('KNOWLEDGE') }}<span class="text-titan-red">.</span><br />
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-gray-300 to-white">{{ __('RESOURCES') }}</span>
                    </h1>
                    <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 delay-500 border-l-4 border-titan-red pl-8">
                        <p class="text-white/80 text-base md:text-lg leading-relaxed max-w-lg font-medium">
                            {{ __('Access our centralized repository of engineering standards, research papers, and corporate resources.') }}
                        </p>
                    </div>
                </div>

                <!-- Stats Grid (Premium Design) -->
                <div :class="shown ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
                    class="transition-all duration-1000 delay-700 flex items-center gap-10 bg-white/5 p-10 rounded-[2.5rem] backdrop-blur-xl border border-white/10 shadow-2xl">
                    <div>
                        <div class="text-5xl font-black text-titan-red leading-none mb-3">{{ $totalDocuments }}<span
                                class="text-white/30 font-light">+</span></div>
                        <div class="text-[9px] font-black uppercase tracking-[0.3em] text-white/40">
                            {{ __('Documents') }}
                        </div>
                    </div>
                    <div class="w-px h-16 bg-white/10"></div>
                    <div>
                        <div class="text-5xl font-black text-white leading-none mb-3">{{ $totalCategories }}</div>
                        <div class="text-[9px] font-black uppercase tracking-[0.3em] text-white/40">
                            {{ __('Categories') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Decorative bottom edge --}}
        <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-white to-transparent z-10"></div>
    </section>

    <!-- === FILTER + SEARCH BAR (CLEAN) === -->
    <section class="sticky top-0 z-30 bg-white/95 backdrop-blur-xl border-b border-gray-100 px-6 py-2 shadow-sm">
        <div class="max-w-[1240px] mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Scrollable Tabs -->
            <div class="w-full md:w-auto flex items-center gap-1 overflow-x-auto no-scrollbar py-1" wire:ignore.self>
                <button wire:click="setTab('all')" 
                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] transition-all duration-300 flex items-center gap-2 shrink-0 {{ $activeTabId === 'all' ? 'bg-titan-navy text-white shadow-md' : 'text-titan-navy/40 hover:text-titan-navy hover:bg-gray-50' }}">
                    <x-lucide-layers class="w-3.5 h-3.5" />
                    {{ __('All') }}
                </button>
                @foreach($categories as $cat)
                    @php
                        $iconMap = [
                            'heroicon-o-academic-cap' => 'lucide-graduation-cap',
                            'heroicon-o-cog-6-tooth' => 'lucide-settings-2',
                            'heroicon-o-shield-check' => 'lucide-shield-check',
                            'heroicon-o-scale' => 'lucide-scale',
                            'heroicon-o-wrench-screwdriver' => 'lucide-wrench',
                            'heroicon-o-document-text' => 'lucide-file-text',
                            'heroicon-o-clipboard-document-list' => 'lucide-clipboard-list',
                            'heroicon-o-book-open' => 'lucide-book-open',
                        ];
                        $iconName = $iconMap[$cat->icon] ?? 'lucide-folder';
                    @endphp
                    <button wire:click="setTab('{{ $cat->id }}')" 
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] transition-all duration-300 flex items-center gap-2 shrink-0 {{ $activeTabId === $cat->id ? 'bg-titan-navy text-white shadow-md' : 'text-titan-navy/40 hover:text-titan-navy hover:bg-gray-50' }}">
                        <x-dynamic-component :component="$iconName" class="w-3.5 h-3.5" />
                        {{ $cat->getTranslation('name', app()->getLocale()) }}
                    </button>
                @endforeach
            </div>

            <!-- Integrated Search -->
            <div class="relative w-full md:w-72">
                <x-lucide-search class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/30" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search records...') }}"
                    class="w-full bg-titan-navy/[0.03] border-none rounded-xl pl-10 pr-4 py-2 text-xs text-titan-navy placeholder:text-titan-navy/20 focus:ring-1 focus:ring-titan-red/30 transition-all font-bold" />
            </div>
        </div>
    </section>

    <!-- === DOCUMENT GRID (ULTRA CLEAN) === -->
    <section class="max-w-[1240px] mx-auto px-6 py-16 relative min-h-[600px]">
        <!-- SKELETON LOADING GRID -->
        <div wire:loading.grid class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
            @for($i = 0; $i < 6; $i++)
                <div class="animate-pulse flex flex-col rounded-2xl border border-gray-100 bg-white overflow-hidden h-full">
                    <div class="aspect-video bg-gray-50"></div>
                    <div class="p-6 space-y-3">
                        <div class="h-3 w-20 bg-gray-100 rounded-full"></div>
                        <div class="h-6 w-full bg-gray-100 rounded-lg"></div>
                        <div class="h-3 w-2/3 bg-gray-50 rounded-lg"></div>
                        <div class="pt-4 flex justify-between">
                            <div class="h-4 w-12 bg-gray-50"></div>
                            <div class="h-4 w-20 bg-gray-100 rounded-full"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- ACTUAL CONTENT -->
        <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
            @forelse($documents as $doc)
                @php
                    $isFirstFeatured = $doc->is_featured && ($loop->first && $documents->currentPage() === 1);
                @endphp
                <div @class([
                    'flex flex-col overflow-hidden transition-all duration-500 bg-white group border border-gray-100 relative',
                    'md:col-span-2 lg:col-span-3 rounded-[2rem] border-titan-red/10 !flex-row min-h-[380px] shadow-lg bg-gradient-to-br from-white to-gray-50/30' => $isFirstFeatured,
                    'rounded-2xl h-full hover:shadow-xl hover:border-titan-red/10' => !$isFirstFeatured
                ])>

                    <!-- Image/Thumbnail Container -->
                    <div @class([
                        'relative overflow-hidden bg-gray-50 flex items-center justify-center shrink-0',
                        'w-2/5 min-h-[380px]' => $isFirstFeatured,
                        'aspect-video' => !$isFirstFeatured
                    ])>
                        @if($doc->thumbnailUrl)
                            <img src="{{ Storage::url($doc->thumbnailUrl) }}" alt="{{ $doc->title }}"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-[10s]" />
                        @else
                            <div class="absolute inset-0 w-full h-full bg-titan-navy/[0.01] flex items-center justify-center">
                                <div class="absolute inset-0 bg-[radial-gradient(#00000005_1px,transparent_1px)] [background-size:10px_10px]"></div>
                                <x-lucide-file-text @class([
                                    'text-titan-navy/10 group-hover:scale-110 group-hover:text-titan-red transition-all duration-700 relative z-10 opacity-30 drop-shadow-sm',
                                    'w-24 h-24' => $isFirstFeatured,
                                    'w-14 h-14' => !$isFirstFeatured
                                ]) />
                            </div>
                        @endif

                        <!-- Top Badges -->
                        <div class="absolute top-4 left-4 z-20">
                            @if($doc->is_featured)
                                <span class="bg-titan-red text-white text-[8px] font-black uppercase tracking-widest px-2.5 py-1.5 rounded-lg shadow-md">
                                    {{ __('FEATURED') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div @class([
                        'flex flex-col flex-1',
                        'p-10 justify-center' => $isFirstFeatured,
                        'p-6' => !$isFirstFeatured
                    ])>
                        <!-- Category Header -->
                        <div class="flex items-center gap-2.5 mb-3">
                            <span class="text-[9px] font-black uppercase tracking-[0.15em] text-titan-red">
                                {{ $doc->documentCategory ? $doc->documentCategory->getTranslation('name', app()->getLocale()) : ($doc->category ?: __('RESOURCE')) }}
                            </span>
                            <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                            <span class="text-[9px] font-bold text-titan-navy/30 uppercase tracking-[0.15em]">
                                {{ $doc->fileType ?? 'PDF' }} · {{ $doc->fileSize ?? '' }}
                            </span>
                        </div>

                        <!-- Title -->
                        <a href="/documents/{{ $doc->slug }}" class="block mb-3">
                            <h2 @class([
                                'font-black text-titan-navy group-hover:text-titan-red transition-colors duration-300 leading-[1.2] tracking-tight',
                                'text-3xl max-w-lg' => $isFirstFeatured,
                                'text-lg line-clamp-2' => !$isFirstFeatured
                            ])>
                                {{ $doc->title }}
                            </h2>
                        </a>

                        <!-- Excerpt -->
                        <p @class([
                            'text-titan-navy/50 leading-relaxed mb-6',
                            'text-base max-w-md' => $isFirstFeatured,
                            'text-xs line-clamp-2' => !$isFirstFeatured
                        ])>
                            {{ str(strip_tags($doc->description))->limit(120) }}
                        </p>

                        <!-- Footer -->
                        <div class="mt-auto flex items-center justify-between pt-5 border-t border-gray-50">
                            <div class="flex items-center gap-2">
                                <x-lucide-calendar class="w-3 h-3 text-titan-navy/20" />
                                <span class="text-[9px] font-bold text-titan-navy/20 uppercase tracking-widest">
                                    {{ $doc->created_at->format('M Y') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                @if($doc->fileUrl)
                                    <a href="{{ Storage::url($doc->fileUrl) }}" download @click.stop 
                                        class="w-8 h-8 rounded-lg bg-gray-50 text-titan-navy/30 hover:bg-titan-red hover:text-white flex items-center justify-center transition-all">
                                        <x-lucide-download class="w-3.5 h-3.5" />
                                    </a>
                                @endif
                                <a href="/documents/{{ $doc->slug }}" 
                                    class="inline-flex items-center gap-2 text-[10px] font-black text-titan-navy uppercase tracking-[0.2em] group/btn hover:text-titan-red transition-colors">
                                    <span>{{ __('View') }}</span>
                                    <x-lucide-arrow-right class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="col-span-full py-24 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-100">
                    <x-lucide-file-x class="w-12 h-12 text-titan-navy/10 mx-auto mb-4" />
                    <p class="text-titan-navy/30 font-black text-xs uppercase tracking-[0.3em]">{{ __('No documents found') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="mt-20 flex justify-center">
                {{ $documents->links() }}
            </div>
        @endif
    </section>

    <!-- === CTA SECTION === -->
    <section class="bg-titan-navy py-16 px-6">
        <div class="max-w-[1240px] mx-auto flex flex-col md:flex-row items-center justify-between gap-10">
            <div>
                <div class="text-[10px] font-black text-titan-red uppercase tracking-[0.4em] mb-4">
                    {{ __('Need Specific Files?') }}
                </div>
                <h3 class="text-3xl font-black text-white uppercase tracking-tight mb-3">
                    {{ __("Can't find what you need?") }}
                </h3>
                <p class="text-white/40 text-base max-w-md leading-relaxed font-medium">
                    {{ __('Our team can prepare custom technical documentation and case studies upon request.') }}
                </p>
            </div>
            <a href="/contact"
                class="inline-flex items-center gap-3 bg-titan-red hover:bg-white hover:text-titan-navy text-white px-10 py-5 rounded-2xl font-black text-xs uppercase tracking-widest transition-all duration-300 shadow-xl group">
                <x-lucide-mail class="w-4 h-4 group-hover:scale-110 transition-transform" />
                {{ __('Request Support') }}
            </a>
        </div>
    </section>

</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>