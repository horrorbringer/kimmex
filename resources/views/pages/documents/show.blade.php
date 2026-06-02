@php
    use App\Models\Document;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Cache;

    /** @var string $slug */
    $locale = app()->getLocale();

    $doc = Cache::remember("document_show_data_{$slug}_{$locale}", now()->addHours(12), function() use ($slug, $locale) {
        $d = Document::with('documentCategory')
            ->publiclyVisible()
            ->where('slug', $slug)
            ->first();

        if (!$d) return null;

        return [
            'id' => $d->id,
            'document_category_id' => $d->document_category_id,
            'title' => $d->getTranslation('title', $locale),
            'description' => $d->getTranslation('description', $locale),
            'thumbnailUrl' => $d->thumbnailUrl,
            'is_featured' => $d->is_featured,
            'fileUrl' => $d->fileUrl,
            'fileType' => $d->fileType,
            'fileSize' => $d->fileSize,
            'downloadCount' => $d->downloadCount,
            'date' => $d->created_at->format('M Y'),
            'created_at_formatted' => $d->created_at->format('M Y'),
            'categoryName' => $d->documentCategory ? $d->documentCategory->getTranslation('name', $locale) : $d->category
        ];
    });

    if (!$doc) {
        abort(404);
    }

    // For a related docs feel, just pull latest 4 from same category
    $relatedDocs = Cache::remember("document_related_{$doc['id']}_{$locale}", now()->addHours(12), function() use ($doc, $locale) {
        return Document::with('documentCategory')
            ->publiclyVisible()
            ->where('document_category_id', $doc['document_category_id'])
            ->where('id', '!=', $doc['id'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function($r) use ($locale) {
                return [
                    'slug' => $r->slug,
                    'title' => $r->getTranslation('title', $locale),
                    'thumbnailUrl' => $r->thumbnailUrl,
                    'fileType' => $r->fileType,
                    'fileSize' => $r->fileSize,
                    'categoryName' => $r->documentCategory ? $r->documentCategory->getTranslation('name', $locale) : $r->category
                ];
            })->all();
    });

    $categoryName = $doc['categoryName'];
@endphp

<x-layouts.app :title="$doc['title']" :description="$doc['description'] ?? ''">

    <div class="min-h-screen bg-[#F7F8FA] text-titan-navy font-sans antialiased">

        <!-- === COMPACT RESOURCE HEADER === -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-[1240px] mx-auto px-6 py-12 md:py-16">
                <a href="/documents"
                    class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.18em] text-titan-navy/45 hover:text-titan-red transition-colors mb-8">
                    <x-lucide-arrow-left class="w-4 h-4" />
                    {{ __('Back to Documents') }}
                </a>

                <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-10 items-start">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-5">
                            <span class="rounded bg-titan-red text-white px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.16em]">
                                {{ $categoryName }}
                            </span>
                            <span class="rounded border border-gray-200 bg-gray-50 text-titan-navy/60 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.16em]">
                                {{ strtoupper($doc['fileType'] ?? 'PDF') }}
                            </span>
                            @if($doc['is_featured'])
                                <span class="rounded border border-titan-red/20 bg-titan-red/5 text-titan-red px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.16em]">
                                    {{ __('Featured') }}
                                </span>
                            @endif
                        </div>

                        <h1 class="font-black uppercase tracking-normal leading-tight text-titan-navy max-w-4xl"
                            style="font-size: clamp(2rem, 5vw, 4rem) !important;">
                            {{ $doc['title'] }}
                        </h1>

                        <div class="mt-7 grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-3xl">
                            <div class="rounded border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-[9px] font-black uppercase tracking-[0.18em] text-titan-navy/35">{{ __('Type') }}</div>
                                <div class="mt-1 text-sm font-black text-titan-navy">{{ strtoupper($doc['fileType'] ?? 'PDF') }}</div>
                            </div>
                            <div class="rounded border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-[9px] font-black uppercase tracking-[0.18em] text-titan-navy/35">{{ __('Size') }}</div>
                                <div class="mt-1 text-sm font-black text-titan-navy">{{ $doc['fileSize'] ?: '-' }}</div>
                            </div>
                            <div class="rounded border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-[9px] font-black uppercase tracking-[0.18em] text-titan-navy/35">{{ __('Date') }}</div>
                                <div class="mt-1 text-sm font-black text-titan-navy">{{ $doc['date'] ?? $doc['created_at_formatted'] }}</div>
                            </div>
                            <div class="rounded border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-[9px] font-black uppercase tracking-[0.18em] text-titan-navy/35">{{ __('Downloads') }}</div>
                                <div class="mt-1 text-sm font-black text-titan-navy">{{ number_format($doc['downloadCount'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded border border-gray-200 bg-titan-navy p-5 text-white">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 rounded bg-white/10 flex items-center justify-center">
                                <x-lucide-shield-check class="w-5 h-5 text-titan-red" />
                            </div>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.18em] text-titan-red">
                                    {{ __('Verified Resource') }}
                                </div>
                                <div class="text-sm font-bold text-white/80">{{ __('Official Kimmex document') }}</div>
                            </div>
                        </div>

                        @if($doc['fileUrl'])
                            <a href="{{ \App\Support\PublicStorage::url($doc['fileUrl']) }}" download target="_blank"
                                class="w-full h-12 rounded bg-titan-red hover:bg-white hover:text-titan-navy text-white inline-flex items-center justify-center gap-3 font-black text-xs uppercase tracking-[0.16em] transition-all">
                                <x-lucide-download class="w-4 h-4" />
                                {{ __('Download File') }}
                            </a>
                        @else
                            <div class="rounded border border-white/10 bg-white/5 p-4 text-sm text-white/60">
                                {{ __('This document file is not available for download yet.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="max-w-[1240px] mx-auto px-6 py-12 md:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-[380px_1fr] gap-8 lg:gap-12 items-start">

                <!-- Preview + Actions -->
                <aside class="lg:sticky lg:top-28 space-y-5">
                    <div class="rounded border border-gray-200 bg-white overflow-hidden">
                        <div class="relative aspect-[4/5] bg-titan-navy flex items-center justify-center">
                            @if($doc['thumbnailUrl'])
                                <img src="{{ \App\Support\PublicStorage::url($doc['thumbnailUrl']) }}" alt="{{ $doc['title'] }}"
                                    class="absolute inset-0 w-full h-full object-cover" loading="lazy" decoding="async" />
                                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/70 via-transparent to-transparent"></div>
                            @else
                                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.16)_0%,transparent_48%)]"></div>
                                <x-lucide-file-text class="w-20 h-20 text-white/25" />
                            @endif
                            <div class="absolute left-5 bottom-5 rounded bg-white text-titan-navy px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.16em]">
                                {{ strtoupper($doc['fileType'] ?? 'Document') }}
                            </div>
                        </div>
                    </div>

                    <div class="rounded border border-gray-200 bg-white p-5">
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-titan-red mb-4">
                            {{ __('Share') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                class="w-10 h-10 rounded bg-social-facebook text-white flex items-center justify-center hover:brightness-110 transition-all">
                                <x-lucide-facebook class="w-4 h-4" />
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                class="w-10 h-10 rounded bg-social-linkedin text-white flex items-center justify-center hover:brightness-110 transition-all">
                                <x-lucide-linkedin class="w-4 h-4" />
                            </a>
                            <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($doc['title']) }}" target="_blank" rel="noopener"
                                class="w-10 h-10 rounded bg-social-telegram text-white flex items-center justify-center hover:brightness-110 transition-all">
                                <x-lucide-send class="w-4 h-4" />
                            </a>
                            <button x-data="{ 
                                    copied: false,
                                    copyLink() {
                                        const url = window.location.href;
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(url).catch(() => {});
                                        } else {
                                            const el = document.createElement('textarea');
                                            el.value = url;
                                            document.body.appendChild(el);
                                            el.select();
                                            document.execCommand('copy');
                                            document.body.removeChild(el);
                                        }
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 1600);
                                    }
                                }"
                                @click="copyLink()"
                                class="h-10 px-4 rounded border border-gray-200 bg-white text-titan-navy hover:text-titan-red hover:border-titan-red/30 inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.14em] transition-all">
                                <x-lucide-link class="w-4 h-4" />
                                <span x-text="copied ? '{{ __('Copied') }}' : '{{ __('Copy') }}'"></span>
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Detail Body -->
                <section class="rounded border border-gray-200 bg-white p-6 md:p-10">
                    <div class="text-[10px] font-black uppercase tracking-[0.22em] text-titan-red mb-5">
                        {{ __('Document Summary') }}
                    </div>

                    <article class="prose prose-lg prose-slate max-w-none prose-p:text-titan-navy/70 prose-p:leading-[1.8] prose-p:font-medium prose-headings:font-black prose-headings:uppercase prose-headings:tracking-normal prose-headings:text-titan-navy">
                        {!! $doc['description'] !!}
                    </article>

                    <div class="mt-10 pt-8 border-t border-gray-200">
                        <div class="rounded bg-gray-50 border border-gray-200 p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                            <div class="flex items-start gap-3">
                                <x-lucide-badge-check class="w-5 h-5 text-titan-red mt-0.5" />
                                <div>
                                    <h2 class="text-base font-black uppercase tracking-normal text-titan-navy">
                                        {{ __('Usage Notice') }}
                                    </h2>
                                    <p class="mt-1 text-sm text-titan-navy/55 leading-relaxed">
                                        {{ __('This document is an official publication of Kimmex Construction & Investment Co., Ltd. All rights reserved.') }}
                                    </p>
                                </div>
                            </div>

                            @if($doc['fileUrl'])
                                <a href="{{ \App\Support\PublicStorage::url($doc['fileUrl']) }}" download target="_blank"
                                    class="h-11 px-5 rounded bg-titan-navy hover:bg-titan-red text-white inline-flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-[0.16em] transition-all shrink-0">
                                    <x-lucide-download class="w-4 h-4" />
                                    {{ __('Download') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- RELATED DOCUMENTS -->
        @if(count($relatedDocs) > 0)
            <section class="bg-gray-50 py-14 px-6">
                <div class="max-w-[1200px] mx-auto">
                    <div class="flex items-end justify-between gap-4 mb-8">
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-[0.3em] text-titan-red mb-2">
                                {{ __('Related Documents') }}
                            </div>
                            <h2 class="text-xl md:text-2xl font-black uppercase tracking-normal text-titan-navy">
                                {{ __('More in this category') }}
                            </h2>
                        </div>
                        <div class="text-[10px] font-black uppercase tracking-[0.18em] text-titan-navy/35">
                            {{ __('Up to 3 results') }}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($relatedDocs as $rel)
                            @php $relCatName = $rel['categoryName']; @endphp
                            <a href="/documents/{{ $rel['slug'] }}"
                                class="group flex items-start gap-4 p-4 bg-white rounded border border-gray-200 hover:border-titan-red/25 hover:shadow-md transition-all duration-300">
                                <div
                                    class="w-12 h-12 rounded overflow-hidden shrink-0 bg-gray-50 flex items-center justify-center relative">
                                    @if($rel['thumbnailUrl'])
                                        <img src="{{ \App\Support\PublicStorage::url($rel['thumbnailUrl']) }}"
                                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy" decoding="async" />
                                    @else
                                        <x-lucide-file-text
                                            class="w-5 h-5 text-titan-navy/20 relative z-10 group-hover:text-titan-red transition-colors" />
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[9px] font-black text-titan-red uppercase tracking-[0.16em] mb-1">
                                        {{ $relCatName }}</div>
                                    <h4 class="font-black text-sm text-titan-navy group-hover:text-titan-red transition-colors leading-snug line-clamp-2">
                                        {{ $rel['title'] }}
                                    </h4>
                                    <div class="text-[10px] text-titan-navy/30 mt-1 font-semibold">
                                        {{ $rel['fileType'] ?? 'PDF' }}{{ $rel['fileSize'] ? ' · ' . $rel['fileSize'] : '' }}
                                    </div>
                                </div>
                                <x-lucide-arrow-right
                                    class="w-4 h-4 text-titan-navy/20 group-hover:text-titan-red group-hover:translate-x-1 transition-all duration-300 shrink-0 mt-1" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>

</x-layouts.app>
