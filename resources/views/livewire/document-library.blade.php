<div class="min-h-screen bg-[#F7F8FA] text-titan-navy">

    <!-- === DOCUMENT HUB HERO === -->
    <section class="relative min-h-[440px] flex items-center overflow-hidden bg-titan-navy">
        <div class="absolute inset-0">
            <img src="{{ asset('images/heroes/documents-bg.png') }}" alt="Knowledge Hub"
                class="w-full h-full object-cover animate-slow-zoom" />
            <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/95 via-titan-navy/78 to-titan-navy/30"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-titan-navy via-transparent to-titan-navy/35"></div>
        </div>

        <div class="max-w-[1240px] mx-auto w-full px-6 relative z-20 py-20" x-data="{ shown: false }"
            x-init="setTimeout(() => shown = true, 100)">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-12 items-end">
                <div>
                    <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'"
                        class="transition-all duration-700 inline-flex items-center gap-3 border border-white/15 bg-white/10 px-4 py-2 rounded">
                        <x-lucide-library class="w-4 h-4 text-titan-red" />
                        <span class="text-[10px] font-black uppercase tracking-[0.26em] text-white/85">
                            {{ __('Kimmex Knowledge Hub') }}
                        </span>
                    </div>

                    <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-700 delay-150 mt-8 mb-6 font-black uppercase leading-[0.95] tracking-normal !text-white"
                        style="font-size: clamp(2rem, 5vw, 4rem) !important;">
                        {{ __('Technical Documents') }}
                    </h1>

                    <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-700 delay-300 text-white/78 text-base md:text-lg leading-relaxed max-w-2xl font-medium">
                        {{ __('Find engineering standards, company resources, case studies, and reference materials in one organized library.') }}
                    </p>
                </div>

                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-700 delay-500 grid grid-cols-2 gap-3">
                    <div class="border border-white/12 bg-white/10 rounded p-5">
                        <div class="text-3xl font-black text-white leading-none">{{ $totalDocuments }}</div>
                        <div class="mt-3 text-[10px] font-black uppercase tracking-[0.2em] text-white/50">
                            {{ __('Documents') }}
                        </div>
                    </div>
                    <div class="border border-white/12 bg-white/10 rounded p-5">
                        <div class="text-3xl font-black text-titan-red leading-none">{{ $totalCategories }}</div>
                        <div class="mt-3 text-[10px] font-black uppercase tracking-[0.2em] text-white/50">
                            {{ __('Categories') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- === SEARCH + FILTERS === -->
    <section class="border-b border-gray-200 bg-white/95 backdrop-blur-xl sticky top-0 z-30">
        <div class="max-w-[1240px] mx-auto px-6 py-4">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-4 items-center">
                <div class="relative">
                    <x-lucide-search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-titan-navy/35" />
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search by title, description, or keyword') }}"
                        class="w-full h-12 rounded border border-gray-200 bg-gray-50 pl-11 pr-12 text-sm font-semibold text-titan-navy placeholder:text-titan-navy/35 focus:bg-white focus:border-titan-red/40 focus:ring-2 focus:ring-titan-red/10 transition-all" />
                    @if($search)
                        <button type="button" wire:click="$set('search', '')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded bg-white border border-gray-200 flex items-center justify-center text-titan-navy/40 hover:text-titan-red hover:border-titan-red/30 transition-colors">
                            <x-lucide-x class="w-3.5 h-3.5" />
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 lg:pb-0" wire:ignore.self>
                    <button wire:click="setTab('all')"
                        class="h-11 px-4 rounded border text-[10px] font-black uppercase tracking-[0.16em] transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTabId === 'all' ? 'bg-titan-navy text-white border-titan-navy' : 'bg-white text-titan-navy/55 border-gray-200 hover:text-titan-navy hover:border-titan-navy/30' }}">
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
                            class="h-11 px-4 rounded border text-[10px] font-black uppercase tracking-[0.16em] transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTabId === $cat->id ? 'bg-titan-red text-white border-titan-red' : 'bg-white text-titan-navy/55 border-gray-200 hover:text-titan-navy hover:border-titan-navy/30' }}">
                            <x-dynamic-component :component="$iconName" class="w-3.5 h-3.5" />
                            {{ $cat->getTranslation('name', app()->getLocale()) }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- === DOCUMENTS === -->
    <section class="max-w-[1240px] mx-auto px-6 py-14 min-h-[620px]">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
            <div>
                <div class="text-[10px] font-black uppercase tracking-[0.24em] text-titan-red mb-2">
                    {{ __('Resource Library') }}
                </div>
                <h2 class="text-2xl md:text-3xl font-black uppercase tracking-normal text-titan-navy">
                    {{ __('Browse Documents') }}
                </h2>
            </div>
            <div class="text-xs font-bold text-titan-navy/45">
                {{ $documents->total() }} {{ __('items found') }}
            </div>
        </div>

        <div wire:loading.grid class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @for($i = 0; $i < 6; $i++)
                <div class="animate-pulse rounded border border-gray-200 bg-white p-5">
                    <div class="h-10 w-10 rounded bg-gray-100 mb-6"></div>
                    <div class="h-3 w-24 bg-gray-100 rounded mb-4"></div>
                    <div class="h-6 w-10/12 bg-gray-100 rounded mb-3"></div>
                    <div class="h-3 w-full bg-gray-100 rounded mb-2"></div>
                    <div class="h-3 w-8/12 bg-gray-100 rounded"></div>
                </div>
            @endfor
        </div>

        <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($documents as $doc)
                @php
                    $isFirstFeatured = $doc->is_featured && ($loop->first && $documents->currentPage() === 1);
                    $categoryName = $doc->documentCategory
                        ? $doc->documentCategory->getTranslation('name', app()->getLocale())
                        : ($doc->category ?: __('Resource'));
                    $fileType = strtoupper($doc->fileType ?: 'PDF');
                    $description = str(strip_tags($doc->description))->limit($isFirstFeatured ? 180 : 115);
                @endphp

                <article @class([
                    'group bg-white border border-gray-200 rounded overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-titan-red/25 hover:shadow-elevated',
                    'md:col-span-2 xl:col-span-3 grid grid-cols-1 lg:grid-cols-[360px_1fr] min-h-[300px]' => $isFirstFeatured,
                    'flex flex-col min-h-[320px]' => !$isFirstFeatured,
                ])>
                    <a href="/documents/{{ $doc->slug }}" @class([
                        'relative bg-titan-navy overflow-hidden flex items-center justify-center',
                        'min-h-[240px] lg:min-h-full' => $isFirstFeatured,
                        'h-36' => !$isFirstFeatured,
                    ])>
                        @if($doc->thumbnailUrl)
                            <img src="{{ \App\Support\PublicStorage::url($doc->thumbnailUrl) }}" alt="{{ $doc->title }}"
                                class="absolute inset-0 w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-700" />
                            <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/65 to-transparent"></div>
                        @else
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.18)_0%,transparent_46%)]"></div>
                            <x-lucide-file-text class="w-14 h-14 text-white/25 group-hover:text-titan-red transition-colors duration-300" />
                        @endif

                        <div class="absolute left-4 top-4 flex items-center gap-2">
                            <span class="rounded bg-white text-titan-navy px-2.5 py-1 text-[9px] font-black uppercase tracking-[0.16em]">
                                {{ $fileType }}
                            </span>
                            @if($doc->is_featured)
                                <span class="rounded bg-titan-red text-white px-2.5 py-1 text-[9px] font-black uppercase tracking-[0.16em]">
                                    {{ __('Featured') }}
                                </span>
                            @endif
                        </div>
                    </a>

                    <div class="flex flex-col flex-1 p-5 {{ $isFirstFeatured ? 'lg:p-8' : '' }}">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-[10px] font-black uppercase tracking-[0.16em] text-titan-red">
                                {{ $categoryName }}
                            </span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-titan-navy/35">
                                {{ $doc->fileSize ?: __('Document') }}
                            </span>
                        </div>

                        <a href="/documents/{{ $doc->slug }}" class="block">
                            <h3 @class([
                                'font-black text-titan-navy group-hover:text-titan-red transition-colors duration-200 leading-tight tracking-normal',
                                'text-2xl md:text-3xl max-w-3xl' => $isFirstFeatured,
                                'text-lg line-clamp-2' => !$isFirstFeatured,
                            ])>
                                {{ $doc->title }}
                            </h3>
                        </a>

                        <p @class([
                            'mt-4 text-titan-navy/60 leading-relaxed font-medium',
                            'text-sm max-w-3xl' => $isFirstFeatured,
                            'text-xs line-clamp-3' => !$isFirstFeatured,
                        ])>
                            {{ $description }}
                        </p>

                        <div class="mt-auto pt-6 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.14em] text-titan-navy/35">
                                <x-lucide-calendar class="w-3.5 h-3.5" />
                                {{ $doc->created_at->format('M Y') }}
                            </div>

                            <div class="flex items-center gap-2">
                                @if($doc->fileUrl)
                                    <a href="{{ \App\Support\PublicStorage::url($doc->fileUrl) }}" download
                                        class="w-9 h-9 rounded border border-gray-200 bg-white text-titan-navy/55 hover:bg-titan-red hover:text-white hover:border-titan-red flex items-center justify-center transition-all"
                                        aria-label="{{ __('Download document') }}">
                                        <x-lucide-download class="w-4 h-4" />
                                    </a>
                                @endif
                                <a href="/documents/{{ $doc->slug }}"
                                    class="h-9 px-4 rounded bg-titan-navy text-white hover:bg-titan-red inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.16em] transition-all">
                                    {{ __('View') }}
                                    <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-24 text-center bg-white rounded border border-dashed border-gray-300">
                    <x-lucide-file-x class="w-12 h-12 text-titan-navy/15 mx-auto mb-4" />
                    <h3 class="font-black uppercase tracking-normal text-titan-navy mb-2">{{ __('No documents found') }}</h3>
                    <p class="text-sm text-titan-navy/45 font-medium">
                        {{ __('Try another keyword or choose a different category.') }}
                    </p>
                </div>
            @endforelse
        </div>

        @if($documents->hasPages())
            <div class="mt-14">
                {{ $documents->links() }}
            </div>
        @endif
    </section>

    <!-- === CTA SECTION === -->
    <section class="bg-white border-t border-gray-200 py-16 px-6">
        <div class="max-w-[1240px] mx-auto grid grid-cols-1 md:grid-cols-[1fr_auto] gap-8 items-center">
            <div>
                <div class="text-[10px] font-black text-titan-red uppercase tracking-[0.28em] mb-3">
                    {{ __('Need Specific Files?') }}
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-titan-navy uppercase tracking-normal mb-3">
                    {{ __("Request technical support") }}
                </h2>
                <p class="text-titan-navy/55 text-sm md:text-base max-w-2xl leading-relaxed font-medium">
                    {{ __('Our team can help locate project references, technical documents, and case studies for your request.') }}
                </p>
            </div>
            <a href="/contact"
                class="h-12 px-6 rounded bg-titan-red hover:bg-titan-navy text-white inline-flex items-center justify-center gap-3 font-black text-xs uppercase tracking-[0.16em] transition-all shadow-lg shadow-titan-red/15">
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
