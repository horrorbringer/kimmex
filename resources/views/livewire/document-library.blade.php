<div class="min-h-screen bg-slate-50/50"
    x-data="{
        viewMode: localStorage.getItem('kimmex_doc_view') || 'list',
        setView(mode) {
            this.viewMode = mode;
            localStorage.setItem('kimmex_doc_view', mode);
        },
        previewModal: false,
        activeDoc: null,
        openPreview(doc) {
            this.activeDoc = doc;
            this.previewModal = true;
            document.body.classList.add('overflow-hidden');
        },
        closePreview() {
            this.previewModal = false;
            this.activeDoc = null;
            document.body.classList.remove('overflow-hidden');
        }
    }"
    @keydown.escape.window="closePreview()">

    <!-- ═══ HERO ═══ -->
    <section class="relative h-[280px] sm:h-[320px] md:h-[360px] flex items-end overflow-hidden" style="background: #0B2B5C;">
        <div class="absolute inset-0">
            <img src="/images/webp/hero/hero-3.webp" alt="{{ __('Document Library') }}"
                class="w-full h-full object-cover opacity-35" loading="eager" decoding="async" fetchpriority="high" />
            <div class="absolute inset-0 bg-gradient-to-t from-[#071A33]/95 via-[#0B2B5C]/60 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#071A33]/60 via-transparent to-transparent"></div>
        </div>

        <div class="relative z-10 w-full max-w-[1280px] mx-auto px-6 pb-8 md:pb-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs mb-3" style="color: rgba(255,255,255,0.5);">
                <a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span style="color: rgba(255,255,255,0.9);">{{ __('Documents') }}</span>
            </nav>

            <h1 class="font-heading font-[900] uppercase leading-none tracking-tight mb-2.5"
                style="font-size: clamp(1.8rem, 4.5vw, 2.8rem); color: #FFFFFF;">
                {{ __('Document') }} <span style="color: var(--primary-color, #E31E24);">{{ __('Library') }}</span>
            </h1>

            <p style="color: rgba(255,255,255,0.65);" class="text-xs sm:text-sm max-w-xl leading-relaxed mb-4">
                {{ __('Explore company technical specifications, guidelines, and compliance resources.') }}
            </p>

            <div class="flex items-center gap-5">
                <div class="text-center">
                    <div class="text-xl sm:text-2xl font-black text-white font-mono leading-none">{{ $totalDocuments }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-wider text-white/40 mt-1">{{ __('Documents') }}</div>
                </div>
                <div class="w-px h-7 bg-white/15"></div>
                <div class="text-center">
                    <div class="text-xl sm:text-2xl font-black font-mono leading-none" style="color: var(--primary-color, #E31E24);">{{ $totalCategories }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-wider text-white/40 mt-1">{{ __('Categories') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ CONTROLS (Search, Filter, Sort, View Mode) ═══ -->
    <section class="sticky top-16 md:top-20 z-30 bg-white/90 backdrop-blur-md border-b border-gray-200/80 shadow-2xs">
        <div class="max-w-[1280px] mx-auto px-6 py-3">
            <div class="flex flex-col lg:flex-row gap-3 items-stretch lg:items-center justify-between">
                <!-- Search Input -->
                <div class="relative flex-1 max-w-md">
                    <x-lucide-search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search by title, keyword, or summary...') }}"
                        class="w-full h-10 rounded-xl border border-gray-200 bg-gray-50/80 pl-10 pr-9 text-xs sm:text-sm text-gray-900 placeholder:text-gray-400 focus:bg-white focus:border-titan-navy focus:ring-2 focus:ring-titan-navy/10 focus:outline-none transition-all shadow-2xs" />
                    @if($search)
                        <button type="button" wire:click="$set('search', '')"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 rounded-md bg-gray-200/80 hover:bg-red-50 hover:text-titan-red flex items-center justify-center text-gray-500 transition-colors"
                            title="{{ __('Clear search') }}">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    @endif
                </div>

                <!-- Right Toolbar: Category Pills, Sort & View Mode -->
                <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap justify-between lg:justify-end">
                    <!-- Category Filter Pills -->
                    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5" wire:ignore.self>
                        <button wire:click="setTab('all')"
                            @class([
                                'h-8.5 px-3 rounded-lg text-xs font-bold transition-all duration-200 shrink-0 inline-flex items-center gap-1.5 cursor-pointer',
                                'bg-titan-navy text-white shadow-xs' => $activeTabId === 'all',
                                'bg-gray-100/90 text-gray-600 hover:bg-gray-200/90 hover:text-gray-900' => $activeTabId !== 'all'
                            ])>
                            <x-lucide-layers class="w-3.5 h-3.5 opacity-80" />
                            <span>{{ __('All') }}</span>
                        </button>
                        @foreach($categories as $cat)
                            <button wire:click="setTab('{{ $cat['id'] }}')"
                                @class([
                                    'h-8.5 px-3 rounded-lg text-xs font-bold transition-all duration-200 shrink-0 inline-flex items-center gap-1.5 cursor-pointer',
                                    'bg-titan-navy text-white shadow-xs' => $activeTabId === $cat['id'],
                                    'bg-gray-100/90 text-gray-600 hover:bg-gray-200/90 hover:text-gray-900' => $activeTabId !== $cat['id']
                                ])>
                                <span>{{ $cat['name'] }}</span>
                            </button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <!-- Sort Dropdown -->
                        <div class="relative">
                            <select wire:model.live="sortBy"
                                class="h-8.5 pl-2.5 pr-7 rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-1 focus:ring-titan-navy cursor-pointer shadow-2xs appearance-none">
                                <option value="latest">{{ __('Latest Published') }}</option>
                                <option value="oldest">{{ __('Oldest First') }}</option>
                                <option value="title_asc">{{ __('Title (A to Z)') }}</option>
                                <option value="title_desc">{{ __('Title (Z to A)') }}</option>
                            </select>
                            <x-lucide-arrow-down-up class="w-3 h-3 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                        </div>

                        <!-- Grid / List Toggle Switch -->
                        <div class="inline-flex items-center bg-gray-100/90 p-0.5 rounded-lg border border-gray-200/70 shadow-2xs">
                            <button type="button" @click="setView('grid')"
                                :class="viewMode === 'grid' ? 'bg-white text-titan-navy shadow-xs font-bold' : 'text-gray-500 hover:text-gray-900'"
                                class="p-1.5 px-2.5 rounded-md text-xs inline-flex items-center gap-1.5 transition-all cursor-pointer"
                                title="{{ __('Grid View') }}">
                                <x-lucide-layout-grid class="w-3.5 h-3.5" />
                                <span class="hidden sm:inline text-[11px]">{{ __('Grid') }}</span>
                            </button>
                            <button type="button" @click="setView('list')"
                                :class="viewMode === 'list' ? 'bg-white text-titan-navy shadow-xs font-bold' : 'text-gray-500 hover:text-gray-900'"
                                class="p-1.5 px-2.5 rounded-md text-xs inline-flex items-center gap-1.5 transition-all cursor-pointer"
                                title="{{ __('List View') }}">
                                <x-lucide-list class="w-3.5 h-3.5" />
                                <span class="hidden sm:inline text-[11px]">{{ __('List') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ DOCUMENTS DATA DISPLAY ═══ -->
    <section class="max-w-[1280px] mx-auto px-6 py-8 md:py-12 min-h-[500px]">

        <!-- Results Info Bar -->
        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" style="background: var(--primary-color, #E31E24);"></span>
                <h2 class="text-base md:text-lg font-heading font-bold text-gray-900 tracking-tight">
                    @if($activeTabId !== 'all')
                        {{ collect($categories)->firstWhere('id', $activeTabId)['name'] ?? __('Filtered Documents') }}
                    @else
                        {{ __('All Documents') }}
                    @endif
                </h2>
            </div>
            <span class="text-xs font-medium text-gray-500 bg-white border border-gray-200/80 px-2.5 py-0.5 rounded-full shadow-2xs">
                {{ $documents->total() }} {{ __('documents') }}
            </span>
        </div>

        <!-- Loading Skeleton -->
        <div wire:loading.grid class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @for($i = 0; $i < 6; $i++)
                <div class="animate-pulse bg-white rounded-2xl border border-gray-200/70 p-5 shadow-2xs">
                    <div class="h-40 bg-gray-200/60 rounded-xl mb-4"></div>
                    <div class="h-3 w-20 bg-gray-200/60 rounded mb-2"></div>
                    <div class="h-5 w-3/4 bg-gray-200/60 rounded mb-3"></div>
                    <div class="h-3 w-full bg-gray-100 rounded mb-4"></div>
                    <div class="h-9 w-full bg-gray-100 rounded-lg"></div>
                </div>
            @endfor
        </div>

        <!-- Document Content Container -->
        <div wire:loading.remove>
            @if($documents->isNotEmpty())
                <!-- 1. GRID VIEW MODE -->
                <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($documents as $doc)
                        @php
                            $categoryName = $doc->documentCategory
                                ? $doc->documentCategory->getTranslation('name', app()->getLocale())
                                : ($doc->category ?: __('Resource'));
                            $fileType = strtoupper($doc->fileType ?: 'PDF');
                            $description = str(strip_tags($doc->description))->limit(120);
                            $thumbnailUrl = \App\Support\PublicStorage::urlIfExists($doc->thumbnailUrl);
                            $fileUrl = \App\Support\PublicStorage::urlIfExists($doc->fileUrl);

                            $isExternal = filled($fileUrl) && \Illuminate\Support\Str::startsWith($fileUrl, ['http://', 'https://']);
                            $host = $isExternal ? (parse_url($fileUrl, PHP_URL_HOST) ?? '') : '';
                            $cloudProvider = null;
                            if ($isExternal) {
                                if (str_contains($host, 'drive.google.com') || str_contains($host, 'docs.google.com')) {
                                    $cloudProvider = 'Google Drive';
                                } elseif (str_contains($host, 'dropbox.com')) {
                                    $cloudProvider = 'Dropbox';
                                } elseif (str_contains($host, 'onedrive') || str_contains($host, 'sharepoint')) {
                                    $cloudProvider = 'OneDrive';
                                }
                            }

                            $docPayload = [
                                'id' => $doc->id,
                                'title' => $doc->title,
                                'slug' => $doc->slug,
                                'category' => $categoryName,
                                'fileType' => $fileType,
                                'fileSize' => $doc->fileSize ?: ($isExternal ? __('Cloud Hosted') : '-'),
                                'fileUrl' => $fileUrl,
                                'thumbnailUrl' => $thumbnailUrl,
                                'description' => $doc->description,
                                'date' => $doc->created_at->format('M d, Y'),
                                'isExternal' => $isExternal,
                                'cloudProvider' => $cloudProvider,
                            ];
                        @endphp

                        <article class="group bg-white rounded-2xl border border-gray-200/80 overflow-hidden transition-all duration-300 hover:border-titan-navy/40 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between">
                            <div>
                                <!-- Image or Blueprint Area -->
                                <div class="relative h-44 overflow-hidden flex items-center justify-center bg-slate-900">
                                    @if($thumbnailUrl)
                                        <img src="{{ $thumbnailUrl }}" alt="{{ $doc->title }}"
                                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 cursor-pointer"
                                            @click="openPreview({{ Js::from($docPayload) }})"
                                            loading="lazy" decoding="async" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center relative cursor-pointer"
                                            @click="openPreview({{ Js::from($docPayload) }})"
                                            style="background: linear-gradient(135deg, #071A33 0%, #0B2B5C 100%);">
                                            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 16px 16px;"></div>
                                            <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center mb-2 z-10">
                                                <x-lucide-file-text class="w-6 h-6 text-white/80" />
                                            </div>
                                            <span class="font-mono font-bold text-[11px] text-white/60 tracking-wider z-10">{{ $fileType }}</span>
                                        </div>
                                    @endif

                                    <!-- Overlay Badges -->
                                    <div class="absolute top-3 left-3 flex items-center gap-1.5 z-10">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-white/95 text-slate-800 backdrop-blur-md shadow-xs">
                                            {{ $fileType }}
                                        </span>
                                        @if($cloudProvider)
                                            <span class="text-[10px] font-bold tracking-wider px-2 py-0.5 rounded-md bg-titan-navy text-white shadow-xs">
                                                {{ $cloudProvider }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($doc->is_featured)
                                        <div class="absolute top-3 right-3 z-10">
                                            <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md text-white shadow-xs" style="background: var(--primary-color, #E31E24);">
                                                ★ {{ __('Featured') }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Quick Preview Action Button on Hover -->
                                    <button type="button" @click="openPreview({{ Js::from($docPayload) }})"
                                        class="absolute bottom-3 right-3 z-10 px-2.5 py-1 rounded-lg bg-black/75 hover:bg-black text-white text-[11px] font-semibold backdrop-blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center gap-1.5 cursor-pointer shadow-md">
                                        <x-lucide-eye class="w-3.5 h-3.5" />
                                        <span>{{ __('Quick Preview') }}</span>
                                    </button>
                                </div>

                                <!-- Card Content -->
                                <div class="p-5">
                                    <div class="flex items-center gap-2 mb-2">
                                        @if($categoryName)
                                            <span class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--primary-color, #E31E24);">
                                                {{ $categoryName }}
                                            </span>
                                        @endif
                                        @if($categoryName && $doc->fileSize)
                                            <span class="text-gray-300">·</span>
                                        @endif
                                        @if($doc->fileSize)
                                            <span class="text-[11px] font-mono text-gray-400">{{ $doc->fileSize }}</span>
                                        @endif
                                    </div>
                                    <a href="/documents/{{ $doc->slug }}">
                                        <h3 class="text-base font-bold text-gray-900 group-hover:text-titan-red transition-colors leading-snug line-clamp-2 mb-2">
                                            {{ $doc->title }}
                                        </h3>
                                    </a>
                                    @if(filled(trim(strip_tags($doc->description))))
                                        <p class="text-xs text-gray-500 leading-relaxed line-clamp-2">
                                            {{ $description }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Bottom Row -->
                            <div class="p-5 pt-0">
                                <div class="flex items-center justify-between pt-3.5 border-t border-gray-100">
                                    <span class="text-[11px] text-gray-400">{{ $doc->created_at->format('M d, Y') }}</span>
                                    <div class="flex items-center gap-2">
                                        <!-- Preview Button -->
                                        <button type="button" @click="openPreview({{ Js::from($docPayload) }})"
                                            class="w-8 h-8 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-600 transition-colors cursor-pointer"
                                            title="{{ __('Quick Preview') }}">
                                            <x-lucide-eye class="w-3.5 h-3.5" />
                                        </button>

                                        @if($fileUrl)
                                            <a href="{{ $fileUrl }}" {{ $isExternal ? 'target="_blank" rel="noopener"' : 'download' }}
                                                class="w-8 h-8 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-600 transition-colors shadow-2xs"
                                                title="{{ $isExternal ? ($cloudProvider ?: __('Open Link')) : __('Download') }}"
                                                onclick="event.stopPropagation()">
                                                @if($isExternal)
                                                    <x-lucide-external-link class="w-3.5 h-3.5" />
                                                @else
                                                    <x-lucide-download class="w-3.5 h-3.5" />
                                                @endif
                                            </a>
                                        @endif

                                        <a href="/documents/{{ $doc->slug }}" class="inline-flex items-center gap-1 text-xs font-bold transition-all group-hover:gap-1.5 ml-1" style="color: var(--primary-color, #E31E24);">
                                            <span>{{ __('View') }}</span>
                                            <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- 2. MINIMALIST CLEAN TABLE / LIST VIEW MODE -->
                <div x-show="viewMode === 'list'" class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs sm:text-sm">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-gray-200 text-gray-500 uppercase tracking-wider text-[11px] font-bold">
                                    <th class="py-3.5 px-4 sm:px-6">{{ __('Document') }}</th>
                                    <th class="py-3.5 px-4 hidden md:table-cell">{{ __('Category') }}</th>
                                    <th class="py-3.5 px-4 hidden sm:table-cell">{{ __('Type & Size') }}</th>
                                    <th class="py-3.5 px-4 hidden lg:table-cell">{{ __('Date') }}</th>
                                    <th class="py-3.5 px-4 sm:px-6 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($documents as $doc)
                                    @php
                                        $categoryName = $doc->documentCategory
                                            ? $doc->documentCategory->getTranslation('name', app()->getLocale())
                                            : ($doc->category ?: null);
                                        $fileType = strtoupper($doc->fileType ?: 'PDF');
                                        $thumbnailUrl = \App\Support\PublicStorage::urlIfExists($doc->thumbnailUrl);
                                        $fileUrl = \App\Support\PublicStorage::urlIfExists($doc->fileUrl);

                                        $isExternal = filled($fileUrl) && \Illuminate\Support\Str::startsWith($fileUrl, ['http://', 'https://']);
                                        $host = $isExternal ? (parse_url($fileUrl, PHP_URL_HOST) ?? '') : '';
                                        $cloudProvider = null;
                                        if ($isExternal) {
                                            if (str_contains($host, 'drive.google.com') || str_contains($host, 'docs.google.com')) {
                                                $cloudProvider = 'Google Drive';
                                            } elseif (str_contains($host, 'dropbox.com')) {
                                                $cloudProvider = 'Dropbox';
                                            } elseif (str_contains($host, 'onedrive') || str_contains($host, 'sharepoint')) {
                                                $cloudProvider = 'OneDrive';
                                            }
                                        }

                                        $docPayload = [
                                            'id' => $doc->id,
                                            'title' => $doc->title,
                                            'slug' => $doc->slug,
                                            'category' => $categoryName,
                                            'fileType' => $fileType,
                                            'fileSize' => $doc->fileSize ?: ($isExternal ? __('Cloud Hosted') : null),
                                            'fileUrl' => $fileUrl,
                                            'thumbnailUrl' => $thumbnailUrl,
                                            'description' => $doc->description,
                                            'date' => $doc->created_at->format('M d, Y'),
                                            'isExternal' => $isExternal,
                                            'cloudProvider' => $cloudProvider,
                                        ];
                                    @endphp
                                    <tr class="group hover:bg-slate-50/80 transition-colors">
                                        <!-- Document Info -->
                                        <td class="py-3 px-4 sm:px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 bg-slate-900 flex items-center justify-center border border-gray-200">
                                                    @if($thumbnailUrl)
                                                        <img src="{{ $thumbnailUrl }}" alt="" class="w-full h-full object-cover" loading="lazy" />
                                                    @else
                                                        <span class="font-mono font-bold text-[10px] text-white/80">{{ $fileType }}</span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <a href="/documents/{{ $doc->slug }}" class="font-semibold text-gray-900 group-hover:text-titan-red transition-colors block truncate max-w-xs sm:max-w-md">
                                                        {{ $doc->title }}
                                                    </a>
                                                    <div class="flex items-center gap-2 mt-0.5 md:hidden">
                                                        @if($categoryName)
                                                            <span class="text-[10px] font-bold text-titan-red">{{ $categoryName }}</span>
                                                        @endif
                                                        @if($categoryName && ($doc->fileSize || $fileType))
                                                            <span class="text-gray-300">·</span>
                                                        @endif
                                                        <span class="text-[10px] text-gray-400 font-mono">{{ $doc->fileSize ?: $fileType }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Category -->
                                        <td class="py-3 px-4 hidden md:table-cell">
                                            @if($categoryName)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">
                                                    {{ $categoryName }}
                                                </span>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>

                                        <!-- Type & Size -->
                                        <td class="py-3 px-4 hidden sm:table-cell">
                                            <div class="flex items-center gap-1.5">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono bg-slate-100 text-slate-800 border border-slate-200">
                                                    {{ $fileType }}
                                                </span>
                                                @if($cloudProvider)
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                        {{ $cloudProvider }}
                                                    </span>
                                                @endif
                                                @if($doc->fileSize)
                                                    <span class="text-xs text-gray-500 font-mono">{{ $doc->fileSize }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Date -->
                                        <td class="py-3 px-4 hidden lg:table-cell text-xs text-gray-500 whitespace-nowrap">
                                            {{ $doc->created_at->format('M d, Y') }}
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-3 px-4 sm:px-6 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button" @click="openPreview({{ Js::from($docPayload) }})"
                                                    class="h-8 px-2.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold inline-flex items-center gap-1 transition-colors cursor-pointer shadow-2xs"
                                                    title="{{ __('Quick Preview') }}">
                                                    <x-lucide-eye class="w-3.5 h-3.5 text-gray-500" />
                                                    <span class="hidden sm:inline">{{ __('Preview') }}</span>
                                                </button>

                                                @if($fileUrl)
                                                    <a href="{{ $fileUrl }}" {{ $isExternal ? 'target="_blank" rel="noopener"' : 'download' }}
                                                        class="h-8 px-2.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold inline-flex items-center gap-1 transition-colors shadow-2xs"
                                                        title="{{ $isExternal ? ($cloudProvider ?: __('Open Link')) : __('Download') }}">
                                                        @if($isExternal)
                                                            <x-lucide-external-link class="w-3.5 h-3.5 text-gray-500" />
                                                            <span class="hidden sm:inline">{{ $cloudProvider ?: __('Open') }}</span>
                                                        @else
                                                            <x-lucide-download class="w-3.5 h-3.5 text-gray-500" />
                                                            <span class="hidden sm:inline">{{ __('Download') }}</span>
                                                        @endif
                                                    </a>
                                                @endif

                                                <a href="/documents/{{ $doc->slug }}"
                                                    class="h-8 w-8 rounded-lg bg-gray-100 hover:bg-titan-red hover:text-white flex items-center justify-center text-gray-600 transition-colors"
                                                    title="{{ __('Document Details') }}">
                                                    <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="py-16 px-6 text-center bg-white rounded-2xl border border-dashed border-gray-300 shadow-2xs">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <x-lucide-file-x class="w-7 h-7" />
                    </div>
                    <h3 class="text-base font-bold text-gray-800 mb-1">{{ __('No documents found') }}</h3>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto mb-5">{{ __('Try searching for a different keyword or resetting the category filter.') }}</p>
                    @if($search || $activeTabId !== 'all')
                        <button type="button" wire:click="$set('search', ''); setTab('all');"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-titan-navy text-white text-xs font-bold hover:bg-slate-800 transition-colors cursor-pointer">
                            <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                            <span>{{ __('Clear filters') }}</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $documents->links() }}
            </div>
        @endif
    </section>

    <!-- ═══ INSTANT DOCUMENT PREVIEW MODAL ═══ -->
    <div x-show="previewModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <!-- Modal Backdrop -->
        <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm" @click="closePreview()"></div>

        <!-- Modal Card Container -->
        <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col max-h-[90vh] z-10"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4">

            <!-- Modal Header -->
            <div class="px-5 sm:px-6 py-4 bg-slate-900 text-white flex items-center justify-between gap-4 border-b border-slate-800 shrink-0">
                <div class="min-w-0 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center !text-white/80 shrink-0">
                        <x-lucide-file-text class="w-5 h-5" />
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-0.5">
                            <template x-if="activeDoc?.category">
                                <span class="!text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded !text-white bg-titan-red" x-text="activeDoc?.category"></span>
                            </template>
                            <template x-if="activeDoc?.fileType">
                                <span class="!text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-white/10 !text-white/80" x-text="activeDoc?.fileType"></span>
                            </template>
                            <template x-if="activeDoc?.fileSize">
                                <span class="!text-[11px] !text-white/50" x-text="activeDoc?.fileSize"></span>
                            </template>
                        </div>
                        <h3 class="!text-sm sm:!text-base font-bold !text-white truncate max-w-lg" x-text="activeDoc?.title"></h3>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <template x-if="activeDoc?.fileUrl">
                        <a :href="activeDoc?.fileUrl" :target="activeDoc?.isExternal ? '_blank' : '_self'" :download="!activeDoc?.isExternal"
                            class="h-9 px-3 rounded-lg bg-titan-red hover:bg-red-700 text-white text-xs font-bold inline-flex items-center gap-1.5 transition-colors shadow-xs">
                            <template x-if="activeDoc?.isExternal"><x-lucide-external-link class="w-3.5 h-3.5" /></template>
                            <template x-if="!activeDoc?.isExternal"><x-lucide-download class="w-3.5 h-3.5" /></template>
                            <span x-text="activeDoc?.isExternal ? (activeDoc?.cloudProvider || '{{ __('Open Link') }}') : '{{ __('Download') }}'"></span>
                        </a>
                    </template>
                    <button type="button" @click="closePreview()"
                        class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 text-white/70 hover:text-white flex items-center justify-center transition-colors cursor-pointer"
                        title="{{ __('Close') }}">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-5 bg-slate-50/50">
                <!-- Thumbnail Preview (if available) -->
                <template x-if="activeDoc?.thumbnailUrl">
                    <div class="rounded-xl overflow-hidden border border-gray-200 bg-slate-950 aspect-[16/9] max-h-72 w-full flex items-center justify-center shadow-inner">
                        <img :src="activeDoc?.thumbnailUrl" :alt="activeDoc?.title" class="w-full h-full object-cover" />
                    </div>
                </template>

                <!-- Document Description -->
                <template x-if="activeDoc?.description && activeDoc?.description.trim().length > 0">
                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-2xs">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2 flex items-center gap-1.5">
                            <x-lucide-info class="w-3.5 h-3.5 text-titan-red" />
                            <span>{{ __('Document Overview') }}</span>
                        </h4>
                        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed" x-html="activeDoc?.description"></div>
                    </div>
                </template>

                <!-- Access / Download Box -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-2xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-red-50 text-titan-red flex items-center justify-center shrink-0 border border-red-100">
                            <template x-if="activeDoc?.isExternal"><x-lucide-cloud class="w-5 h-5" /></template>
                            <template x-if="!activeDoc?.isExternal"><x-lucide-file-down class="w-5 h-5" /></template>
                        </div>
                        <div>
                            <h5 class="text-xs sm:text-sm font-bold text-gray-900" x-text="activeDoc?.isExternal ? '{{ __('External Cloud Document') }}' : '{{ __('Original Document File') }}'"></h5>
                            <p class="text-[11px] text-gray-500 font-mono" x-text="(activeDoc?.date || '') + (activeDoc?.fileSize ? ' · ' + activeDoc?.fileSize : '')"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <template x-if="activeDoc?.fileUrl">
                            <a :href="activeDoc?.fileUrl" :target="activeDoc?.isExternal ? '_blank' : '_self'" :download="!activeDoc?.isExternal"
                                class="flex-1 sm:flex-none h-9.5 px-4 rounded-xl bg-titan-navy hover:bg-slate-800 text-white text-xs font-bold inline-flex items-center justify-center gap-2 transition-colors">
                                <template x-if="activeDoc?.isExternal"><x-lucide-external-link class="w-3.5 h-3.5" /></template>
                                <template x-if="!activeDoc?.isExternal"><x-lucide-download class="w-3.5 h-3.5" /></template>
                                <span>{{ __('Open / Download') }}</span>
                            </a>
                        </template>
                        <a :href="'/documents/' + activeDoc?.slug"
                            class="flex-1 sm:flex-none h-9.5 px-4 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors">
                            <span>{{ __('View Full Page') }}</span>
                            <x-lucide-arrow-right class="w-3.5 h-3.5" />
                        </a>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-gray-500 shrink-0">
                <span class="text-[11px]">{{ __('Press ESC to close') }}</span>
                <button type="button" @click="closePreview()" class="font-bold text-gray-700 hover:text-titan-red cursor-pointer">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>

    <!-- ═══ CTA SECTION ═══ -->
    <section class="py-14 md:py-16 relative overflow-hidden" style="background: linear-gradient(135deg, #071A33 0%, #0B2B5C 100%);">
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>
        <div class="max-w-[1280px] mx-auto px-6 relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white/80 text-[11px] font-bold uppercase tracking-wider mb-3">
                    <x-lucide-help-circle class="w-3.5 h-3.5 text-titan-red" />
                    <span>{{ __('Custom Inquiries') }}</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-heading font-black leading-tight tracking-tight !text-white mb-2">
                    {{ __('Need Specific Technical Documents?') }}
                </h2>
                <p class="text-white/60 text-xs sm:text-sm max-w-xl leading-relaxed">
                    {{ __('Our engineering team can provide specialized project blueprints, compliance certifications, and material test reports upon request.') }}
                </p>
            </div>
            <a href="/contact"
                class="shrink-0 inline-flex items-center gap-2.5 px-6 py-3.5 rounded-xl text-sm font-bold text-white transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5 cursor-pointer"
                style="background: var(--primary-color, #E31E24);">
                <x-lucide-mail class="w-4 h-4" />
                <span>{{ __('Contact Us') }}</span>
            </a>
        </div>
    </section>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
