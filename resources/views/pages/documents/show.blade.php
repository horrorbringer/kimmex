@php
    use App\Models\Document;
    use Illuminate\Support\Facades\Cache;

    $locale = app()->getLocale();

    $doc = Cache::remember("document_show_data_{$slug}_{$locale}", now()->addHours(12), function() use ($slug, $locale) {
        $d = Document::with('documentCategory')->publiclyVisible()->where('slug', $slug)->first();
        if (!$d) return null;
        return [
            'id' => $d->id, 'document_category_id' => $d->document_category_id,
            'title' => $d->getTranslation('title', $locale),
            'description' => $d->getTranslation('description', $locale),
            'thumbnailUrl' => $d->thumbnailUrl, 'is_featured' => $d->is_featured,
            'fileUrl' => $d->fileUrl, 'fileType' => $d->fileType,
            'fileSize' => $d->fileSize, 'downloadCount' => $d->downloadCount,
            'date' => $d->created_at->format('M d, Y'),
            'categoryName' => $d->documentCategory ? $d->documentCategory->getTranslation('name', $locale) : $d->category
        ];
    });

    if (!$doc) { abort(404); }

    $relatedDocs = Cache::remember("document_related_{$doc['id']}_{$locale}", now()->addHours(12), function() use ($doc, $locale) {
        return Document::with('documentCategory')->publiclyVisible()
            ->where('document_category_id', $doc['document_category_id'])
            ->where('id', '!=', $doc['id'])->latest()->take(3)->get()
            ->map(fn($r) => [
                'slug' => $r->slug, 'title' => $r->getTranslation('title', $locale),
                'thumbnailUrl' => $r->thumbnailUrl, 'fileType' => $r->fileType,
                'fileSize' => $r->fileSize, 'date' => $r->created_at->format('M Y'),
                'categoryName' => $r->documentCategory ? $r->documentCategory->getTranslation('name', $locale) : $r->category
            ])->all();
    });

    $categoryName = $doc['categoryName'];
    $thumbnailUrl = \App\Support\PublicStorage::urlIfExists($doc['thumbnailUrl']);
    $fileUrl = \App\Support\PublicStorage::urlIfExists($doc['fileUrl']);
@endphp


<x-layouts.app :title="$doc['title']" :description="strip_tags($doc['description'] ?? '')">

    <div class="min-h-screen bg-gray-50">

        <!-- ═══ HERO ═══ -->
        <section class="relative overflow-hidden" style="background: linear-gradient(135deg, #071A33, #0B2B5C);">
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>
            <div class="relative z-10 max-w-[1280px] mx-auto px-6 pt-32 pb-12 md:pt-36 md:pb-16">

                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-xs mb-8" style="color: rgba(255,255,255,0.4);">
                    <a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <a href="/documents" class="hover:text-white transition-colors">{{ __('Documents') }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <span style="color: rgba(255,255,255,0.8);">{{ \Illuminate\Support\Str::limit($doc['title'], 40) }}</span>
                </nav>

                <!-- Badges -->
                <div class="flex flex-wrap items-center gap-2 mb-5">
                    <span class="text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-md" style="background: var(--primary-color, #E31E24); color: #FFFFFF;">{{ $categoryName }}</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-md" style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.1);">{{ strtoupper($doc['fileType'] ?? 'PDF') }}</span>
                    @if($doc['is_featured'])
                        <span class="text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-md" style="background: rgba(255,255,255,0.08); color: var(--primary-color, #E31E24); border: 1px solid rgba(227,30,36,0.2);">{{ __('Featured') }}</span>
                    @endif
                </div>

                <!-- Title -->
                <h1 class="font-heading font-[900] leading-tight tracking-tight mb-6 max-w-4xl {{ app()->getLocale() === 'km' ? 'font-khmer' : 'uppercase' }}"
                    style="font-size: clamp(1.5rem, 4vw, 2.5rem); color: #FFFFFF;">
                    {{ $doc['title'] }}
                </h1>

                <!-- Meta -->
                <div class="flex flex-wrap items-center gap-4 text-sm" style="color: rgba(255,255,255,0.5);">
                    @if($doc['fileSize'])
                        <span class="flex items-center gap-1.5">
                            <x-lucide-hard-drive class="w-3.5 h-3.5" /> {{ $doc['fileSize'] }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1.5">
                        <x-lucide-calendar class="w-3.5 h-3.5" /> {{ $doc['date'] }}
                    </span>
                    @if(($doc['downloadCount'] ?? 0) > 0)
                        <span class="flex items-center gap-1.5">
                            <x-lucide-download class="w-3.5 h-3.5" /> {{ number_format($doc['downloadCount']) }} {{ __('downloads') }}
                        </span>
                    @endif
                </div>

                <!-- Action buttons -->
                <div class="flex flex-wrap gap-3 mt-8">
                    @if($fileUrl)
                        <a href="{{ $fileUrl }}" download target="_blank"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                            style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                            <x-lucide-download class="w-4 h-4" />
                            {{ __('Download File') }}
                        </a>
                    @endif
                    <a href="/documents"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all"
                        style="border: 2px solid rgba(255,255,255,0.15); color: #FFFFFF;">
                        <x-lucide-arrow-left class="w-4 h-4" />
                        {{ __('All Documents') }}
                    </a>
                </div>
            </div>
        </section>


        <!-- ═══ CONTENT ═══ -->
        <section class="py-12 md:py-16">
            <div class="max-w-[1280px] mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <!-- Main Content -->
                    <div class="lg:col-span-8">
                        <!-- Thumbnail Preview -->
                        @if($thumbnailUrl)
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-6 shadow-sm">
                            <div class="aspect-[16/9] overflow-hidden">
                                <img src="{{ $thumbnailUrl }}" alt="{{ $doc['title'] }}"
                                    class="w-full h-full object-cover" loading="lazy" decoding="async" />
                            </div>
                        </div>
                        @endif

                        <!-- Description -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="px-6 md:px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                     style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                                    <x-lucide-file-text class="w-4 h-4" style="color: var(--primary-color, #E31E24);" />
                                </div>
                                <h2 class="text-sm font-bold text-gray-900">{{ __('Document Description') }}</h2>
                            </div>
                            <div class="px-6 md:px-8 py-6 md:py-8">
                                <div class="prose prose-lg max-w-none text-gray-600 leading-[1.9]
                                    [&>p]:mb-4 [&>p]:text-[0.95rem]
                                    [&>ul]:space-y-2 [&>ul]:list-none [&>ul]:pl-0
                                    [&>ol]:space-y-2 [&>ol]:list-none [&>ol]:pl-0
                                    [&_li]:relative [&_li]:pl-5 [&_li]:text-[0.9rem]
                                    [&_li]:before:content-[''] [&_li]:before:absolute [&_li]:before:left-0 [&_li]:before:top-[0.6rem] [&_li]:before:w-1.5 [&_li]:before:h-1.5 [&_li]:before:rounded-full [&_li]:before:bg-titan-red/50
                                    [&_strong]:text-gray-900 [&_strong]:font-bold">
                                    {!! $doc['description'] !!}
                                </div>
                            </div>
                        </div>

                        <!-- Download CTA -->
                        @if($fileUrl)
                        <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                                    <x-lucide-download class="w-5 h-5" style="color: var(--primary-color, #E31E24);" />
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 mb-1">{{ __('Download This Document') }}</h3>
                                    <p class="text-sm text-gray-500">{{ strtoupper($doc['fileType'] ?? 'PDF') }}{{ $doc['fileSize'] ? ' · ' . $doc['fileSize'] : '' }}</p>
                                </div>
                            </div>
                            <a href="{{ $fileUrl }}" download target="_blank"
                                class="shrink-0 inline-flex items-center gap-2 h-11 px-6 rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5"
                                style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                                <x-lucide-download class="w-4 h-4" />
                                {{ __('Download') }}
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <aside class="lg:col-span-4 space-y-5 lg:sticky lg:top-28 h-fit">

                        <!-- Document Info -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-5 pb-3 border-b border-gray-100">{{ __('Document Info') }}</h4>
                            <div class="space-y-4">
                                @foreach([
                                    ['icon' => 'lucide-folder', 'label' => __('Category'), 'value' => $categoryName],
                                    ['icon' => 'lucide-file', 'label' => __('Format'), 'value' => strtoupper($doc['fileType'] ?? 'PDF')],
                                    ['icon' => 'lucide-hard-drive', 'label' => __('Size'), 'value' => $doc['fileSize'] ?: '-'],
                                    ['icon' => 'lucide-calendar', 'label' => __('Published'), 'value' => $doc['date']],
                                    ['icon' => 'lucide-download', 'label' => __('Downloads'), 'value' => number_format($doc['downloadCount'] ?? 0)],
                                ] as $info)
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 mt-0.5">
                                            <x-dynamic-component :component="$info['icon']" class="w-3.5 h-3.5 text-gray-400" stroke-width="1.8" />
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $info['label'] }}</p>
                                            <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $info['value'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Share -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">{{ __('Share') }}</h4>
                            <div class="flex gap-2" x-data="{ copied: false }">
                                <button @click="navigator.clipboard?.writeText(window.location.href); copied=true; setTimeout(()=>copied=false,2000)"
                                    class="flex-1 h-10 rounded-xl border flex items-center justify-center gap-2 text-xs font-bold transition-all"
                                    :class="copied ? 'bg-green-50 border-green-200 text-green-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
                                    <template x-if="!copied"><x-lucide-link class="w-3.5 h-3.5" /></template>
                                    <template x-if="copied"><x-lucide-check class="w-3.5 h-3.5" /></template>
                                    <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy Link') }}'"></span>
                                </button>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                    class="w-10 h-10 rounded-xl bg-social-facebook flex items-center justify-center text-white hover:brightness-110 transition-all">
                                    <x-social-icon network="facebook" class="w-4 h-4" />
                                </a>
                                <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                    class="w-10 h-10 rounded-xl bg-social-telegram flex items-center justify-center text-white hover:brightness-110 transition-all">
                                    <x-social-icon network="telegram" class="w-4 h-4" />
                                </a>
                            </div>
                        </div>

                        <!-- Back -->
                        <a href="/documents"
                            class="flex items-center justify-center gap-2 w-full h-11 rounded-xl border border-gray-200 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 transition-colors bg-white">
                            <x-lucide-arrow-left class="w-4 h-4" />
                            {{ __('Back to Library') }}
                        </a>
                    </aside>
                </div>
            </div>
        </section>

        <!-- ═══ RELATED DOCUMENTS ═══ -->
        @if(count($relatedDocs) > 0)
        <section class="py-12 md:py-16 bg-white border-t border-gray-100">
            <div class="max-w-[1280px] mx-auto px-6">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                    <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Related') }}</span>
                    <h2 class="text-xl md:text-2xl font-heading font-black text-gray-900 tracking-tight">{{ __('More in') }} {{ $categoryName }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach($relatedDocs as $rel)
                        @php $relThumb = \App\Support\PublicStorage::urlIfExists($rel['thumbnailUrl']); @endphp
                        <a href="/documents/{{ $rel['slug'] }}"
                            class="group bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden hover:bg-white hover:shadow-lg hover:border-gray-200 transition-all duration-300">
                            <div class="h-32 overflow-hidden flex items-center justify-center relative" style="background: #0B2B5C;">
                                @if($relThumb)
                                    <img src="{{ $relThumb }}" alt="{{ $rel['title'] }}" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                                @else
                                    <x-lucide-file-text class="w-10 h-10" style="color: rgba(255,255,255,0.15);" />
                                @endif
                                <div class="absolute top-3 left-3">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-white text-gray-700">{{ strtoupper($rel['fileType'] ?? 'PDF') }}</span>
                                </div>
                            </div>
                            <div class="p-5">
                                <span class="text-[11px] font-bold uppercase tracking-wider mb-2 block" style="color: var(--primary-color, #E31E24);">{{ $rel['categoryName'] }}</span>
                                <h3 class="text-sm font-bold text-gray-900 group-hover:text-titan-red transition-colors line-clamp-2 leading-snug mb-2">{{ $rel['title'] }}</h3>
                                <span class="text-xs text-gray-400">{{ $rel['fileSize'] ?? '' }} · {{ $rel['date'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    </div>

</x-layouts.app>
