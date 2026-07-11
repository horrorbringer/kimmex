<div class="min-h-screen bg-gray-50">

    <!-- ═══ HERO ═══ -->
    <section class="relative h-[400px] md:h-[460px] flex items-end overflow-hidden" style="background: #0B2B5C;">
        <div class="absolute inset-0">
            <img src="{{ asset('images/heroes/documents-bg.png') }}" alt="{{ __('Documents') }}"
                class="w-full h-full object-cover opacity-35" decoding="async" loading="eager" />
            <div class="absolute inset-0 bg-gradient-to-t from-[#071A33]/95 via-[#0B2B5C]/50 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#071A33]/60 via-transparent to-transparent"></div>
        </div>
        <div class="relative z-10 w-full max-w-[1280px] mx-auto px-6 pb-14 md:pb-18">
            <nav class="flex items-center gap-2 text-xs mb-6" style="color: rgba(255,255,255,0.5);">
                <a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span style="color: rgba(255,255,255,0.9);">{{ __('Documents') }}</span>
            </nav>
            <h1 class="font-heading font-[900] uppercase leading-[1] tracking-tight mb-4"
                style="font-size: clamp(2rem, 5vw, 3.2rem); color: #FFFFFF;">
                {{ __('Document') }} <span style="color: var(--primary-color, #E31E24);">{{ __('Library') }}</span>
            </h1>
            <p class="max-w-lg leading-relaxed mb-6" style="color: rgba(255,255,255,0.6); font-size: 1rem;">
                {{ __('Engineering standards, research papers, case studies, and technical resources.') }}
            </p>
            <div class="flex items-center gap-5">
                <div class="text-center">
                    <div class="text-xl font-black" style="color: #FFFFFF;">{{ $totalDocuments }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-wider" style="color: rgba(255,255,255,0.35);">{{ __('Documents') }}</div>
                </div>
                <div class="w-px h-8" style="background: rgba(255,255,255,0.15);"></div>
                <div class="text-center">
                    <div class="text-xl font-black" style="color: var(--primary-color, #E31E24);">{{ $totalCategories }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-wider" style="color: rgba(255,255,255,0.35);">{{ __('Categories') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ SEARCH + FILTERS ═══ -->
    <section class="sticky top-20 z-30 bg-white/95 backdrop-blur-lg border-b border-gray-100 shadow-sm">
        <div class="max-w-[1280px] mx-auto px-6 py-4">
            <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center">
                <!-- Search -->
                <div class="relative flex-grow">
                    <x-lucide-search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" />
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search documents...') }}"
                        class="w-full h-11 rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-12 text-sm text-gray-900 placeholder:text-gray-400 focus:bg-white focus:border-gray-300 focus:outline-none focus:ring-0 transition" />
                    @if($search)
                        <button type="button" wire:click="$set('search', '')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                            <x-lucide-x class="w-3.5 h-3.5" />
                        </button>
                    @endif
                </div>

                <!-- Category Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar" wire:ignore.self>
                    <button wire:click="setTab('all')"
                        @class(['h-9 px-4 rounded-full border text-xs font-bold transition-all shrink-0',
                            'bg-gray-900 text-white border-gray-900 shadow-sm' => $activeTabId === 'all',
                            'bg-white text-gray-500 border-gray-200 hover:border-gray-300' => $activeTabId !== 'all'])>
                        {{ __('All') }}
                    </button>
                    @foreach($categories as $cat)
                        <button wire:click="setTab('{{ $cat['id'] }}')"
                            @class(['h-9 px-4 rounded-full border text-xs font-bold transition-all shrink-0',
                                'bg-gray-900 text-white border-gray-900 shadow-sm' => $activeTabId === $cat['id'],
                                'bg-white text-gray-500 border-gray-200 hover:border-gray-300' => $activeTabId !== $cat['id']])>
                            {{ $cat['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>


    <!-- ═══ DOCUMENTS GRID ═══ -->
    <section class="max-w-[1280px] mx-auto px-6 py-12 md:py-16 min-h-[500px]">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                    <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Library') }}</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-heading font-black text-gray-900 tracking-tight">{{ __('Browse Documents') }}</h2>
            </div>
            <p class="text-sm text-gray-400">{{ $documents->total() }} {{ __('documents found') }}</p>
        </div>

        <!-- Loading skeleton -->
        <div wire:loading.grid class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @for($i = 0; $i < 6; $i++)
                <div class="animate-pulse bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="h-32 bg-gray-100 rounded-xl mb-5"></div>
                    <div class="h-3 w-20 bg-gray-100 rounded mb-3"></div>
                    <div class="h-5 w-3/4 bg-gray-100 rounded mb-3"></div>
                    <div class="h-3 w-full bg-gray-100 rounded"></div>
                </div>
            @endfor
        </div>

        <!-- Document Cards -->
        <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($documents as $doc)
                @php
                    $isFirstFeatured = $doc->is_featured && ($loop->first && $documents->currentPage() === 1);
                    $categoryName = $doc->documentCategory
                        ? $doc->documentCategory->getTranslation('name', app()->getLocale())
                        : ($doc->category ?: __('Resource'));
                    $fileType = strtoupper($doc->fileType ?: 'PDF');
                    $description = str(strip_tags($doc->description))->limit($isFirstFeatured ? 200 : 120);
                    $thumbnailUrl = \App\Support\PublicStorage::urlIfExists($doc->thumbnailUrl);
                    $fileUrl = \App\Support\PublicStorage::urlIfExists($doc->fileUrl);
                @endphp

                <article @class([
                    'group bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all duration-300 hover:border-gray-200 hover:shadow-lg',
                    'md:col-span-2 xl:col-span-3' => $isFirstFeatured,
                    'flex flex-col' => !$isFirstFeatured,
                ])>
                    @if($isFirstFeatured)
                        {{-- Featured: horizontal layout --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 min-h-[280px]">
                            <a href="/documents/{{ $doc->slug }}" class="relative overflow-hidden flex items-center justify-center" style="background: #0B2B5C;">
                                @if($thumbnailUrl)
                                    <img src="{{ $thumbnailUrl }}" alt="{{ $doc->title }}"
                                        class="absolute inset-0 w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent to-black/20"></div>
                                @else
                                    <x-lucide-file-text class="w-16 h-16" style="color: rgba(255,255,255,0.15);" />
                                @endif
                                <div class="absolute top-4 left-4 flex items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md" style="background: var(--primary-color, #E31E24); color: #FFFFFF;">{{ __('Featured') }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md bg-white text-gray-700">{{ $fileType }}</span>
                                </div>
                            </a>
                            <div class="p-7 md:p-9 flex flex-col justify-center">
                                <span class="text-[11px] font-bold uppercase tracking-wider mb-3" style="color: var(--primary-color, #E31E24);">{{ $categoryName }}</span>
                                <a href="/documents/{{ $doc->slug }}">
                                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 group-hover:text-titan-red transition-colors leading-snug mb-4">{{ $doc->title }}</h3>
                                </a>
                                <p class="text-sm text-gray-500 leading-relaxed mb-6">{{ $description }}</p>
                                <div class="flex items-center gap-3">
                                    <a href="/documents/{{ $doc->slug }}" class="inline-flex items-center gap-2 h-10 px-5 rounded-xl text-xs font-bold transition-all" style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent); color: var(--primary-color, #E31E24);">
                                        {{ __('View Details') }} <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                    </a>
                                    @if($fileUrl)
                                        <a href="{{ $fileUrl }}" download class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 text-gray-400 hover:text-titan-red hover:border-red-200 transition-colors">
                                            <x-lucide-download class="w-4 h-4" />
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Standard: vertical card --}}
                        <a href="/documents/{{ $doc->slug }}" class="relative h-36 md:h-40 overflow-hidden flex items-center justify-center" style="background: #0B2B5C;">
                            @if($thumbnailUrl)
                                <img src="{{ $thumbnailUrl }}" alt="{{ $doc->title }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-85 group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            @else
                                <x-lucide-file-text class="w-12 h-12" style="color: rgba(255,255,255,0.15);" />
                            @endif
                            <div class="absolute top-3 left-3 flex items-center gap-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md bg-white text-gray-700">{{ $fileType }}</span>
                                @if($doc->is_featured)
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md" style="background: var(--primary-color, #E31E24); color: #FFFFFF;">{{ __('Featured') }}</span>
                                @endif
                            </div>
                        </a>
                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--primary-color, #E31E24);">{{ $categoryName }}</span>
                                <span class="text-[11px] text-gray-300">·</span>
                                <span class="text-[11px] text-gray-400">{{ $doc->fileSize ?: $doc->created_at->format('M Y') }}</span>
                            </div>
                            <a href="/documents/{{ $doc->slug }}">
                                <h3 class="text-base font-bold text-gray-900 group-hover:text-titan-red transition-colors leading-snug mb-3 line-clamp-2">{{ $doc->title }}</h3>
                            </a>
                            <p class="text-xs text-gray-400 leading-relaxed line-clamp-2 mb-4 flex-grow">{{ $description }}</p>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <span class="text-xs text-gray-400">{{ $doc->created_at->format('M d, Y') }}</span>
                                <div class="flex items-center gap-2">
                                    @if($fileUrl)
                                        <a href="{{ $fileUrl }}" download class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 hover:text-titan-red hover:border-red-200 transition-colors" onclick="event.stopPropagation()">
                                            <x-lucide-download class="w-3.5 h-3.5" />
                                        </a>
                                    @endif
                                    <a href="/documents/{{ $doc->slug }}" class="inline-flex items-center gap-1.5 text-xs font-bold transition-all group-hover:gap-2" style="color: var(--primary-color, #E31E24);">
                                        {{ __('View') }} <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </article>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-2xl border border-dashed border-gray-200">
                    <x-lucide-file-x class="w-12 h-12 text-gray-200 mx-auto mb-4" />
                    <p class="text-base text-gray-400 mb-2">{{ __('No documents found.') }}</p>
                    <p class="text-sm text-gray-300">{{ __('Try a different search term or category.') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="mt-12">{{ $documents->links() }}</div>
        @endif
    </section>

    <!-- ═══ CTA ═══ -->
    <section class="py-16 md:py-20" style="background: linear-gradient(135deg, #071A33, #0B2B5C);">
        <div class="max-w-[1280px] mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-heading font-black leading-tight tracking-tight mb-3" style="color: #FFFFFF;">
                    {{ __('Need Specific Documents?') }}
                </h2>
                <p class="leading-relaxed max-w-lg" style="color: rgba(255,255,255,0.55); font-size: 0.95rem;">
                    {{ __('Our team can help locate project references, technical documents, and case studies.') }}
                </p>
            </div>
            <a href="/contact"
                class="shrink-0 inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-sm font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                <x-lucide-mail class="w-4 h-4" />
                {{ __('Contact Us') }}
            </a>
        </div>
    </section>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
