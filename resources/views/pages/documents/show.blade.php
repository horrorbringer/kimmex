@php
    use App\Models\Document;
    use Illuminate\Support\Facades\Cache;

    $locale = app()->getLocale();

    $doc = Cache::remember("document_show_data_{$slug}_{$locale}", now()->addHours(12), function() use ($slug, $locale) {
        $d = Document::with('documentCategory')->publiclyVisible()->where('slug', $slug)->first();
        if (!$d) return null;
        return [
            'id' => $d->id, 'document_category_id' => $d->document_category_id,
            'title' => $d->getTranslation('title', $locale) ?: $d->getTranslation('title', 'en'),
            'description' => $d->getTranslation('description', $locale) ?: $d->getTranslation('description', 'en'),
            'thumbnailUrl' => $d->thumbnailUrl, 'is_featured' => $d->is_featured,
            'fileUrl' => $d->fileUrl, 'fileType' => $d->fileType,
            'fileSize' => $d->fileSize, 'downloadCount' => $d->downloadCount,
            'date' => $d->created_at->format('M d, Y'),
            'categoryName' => $d->documentCategory ? ($d->documentCategory->getTranslation('name', $locale) ?: $d->documentCategory->getTranslation('name', 'en')) : $d->category
        ];
    });

    if (!$doc) { abort(404); }

    $relatedDocs = Cache::remember("document_related_{$doc['id']}_{$locale}", now()->addHours(12), function() use ($doc, $locale) {
        return Document::with('documentCategory')->publiclyVisible()
            ->where('document_category_id', $doc['document_category_id'])
            ->where('id', '!=', $doc['id'])->latest()->take(3)->get()
            ->map(fn($r) => [
                'slug' => $r->slug, 
                'title' => $r->getTranslation('title', $locale) ?: $r->getTranslation('title', 'en'),
                'thumbnailUrl' => $r->thumbnailUrl, 'fileType' => $r->fileType,
                'fileSize' => $r->fileSize, 'date' => $r->created_at->format('M Y'),
                'categoryName' => $r->documentCategory ? ($r->documentCategory->getTranslation('name', $locale) ?: $r->documentCategory->getTranslation('name', 'en')) : $r->category
            ])->all();
    });

    $categoryName = $doc['categoryName'];
    $thumbnailUrl = \App\Support\PublicStorage::urlIfExists($doc['thumbnailUrl']);
    $fileUrl = \App\Support\PublicStorage::urlIfExists($doc['fileUrl']);

    $isExternal = filled($fileUrl) && \Illuminate\Support\Str::startsWith($fileUrl, ['http://', 'https://']);
    $host = $isExternal ? (parse_url($fileUrl, PHP_URL_HOST) ?? '') : '';
    $cloudProvider = null;
    $embedUrl = null;

    if ($isExternal) {
        if (str_contains($host, 'drive.google.com') || str_contains($host, 'docs.google.com')) {
            $cloudProvider = 'Google Drive';
            // Convert /view to /preview for clean inline embedding
            if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $fileUrl, $matches)) {
                $embedUrl = "https://drive.google.com/file/d/{$matches[1]}/preview";
            }
        } elseif (str_contains($host, 'dropbox.com')) {
            $cloudProvider = 'Dropbox';
        } elseif (str_contains($host, 'onedrive') || str_contains($host, 'sharepoint')) {
            $cloudProvider = 'Microsoft OneDrive';
        }
    } elseif (filled($fileUrl) && str_ends_with(strtolower($fileUrl), '.pdf')) {
        $embedUrl = $fileUrl;
    }
@endphp

<x-layouts.app :title="$doc['title']" :description="strip_tags($doc['description'] ?? '')" :image="$thumbnailUrl ?: '/images/heroes/documents-bg.png'" :image-alt="$doc['title']" :canonical="route('documents.show', ['slug' => $slug])">

    <div class="min-h-screen bg-slate-50/50">

        <!-- ═══ HERO ═══ -->
        <section class="relative pt-24 sm:pt-28 pb-10 sm:pb-12 overflow-hidden" style="background: #0B2B5C;">
            <!-- Background Texture -->
            <div class="absolute inset-0">
                <img src="/images/webp/hero/hero-3.webp" alt="{{ $doc['title'] }}"
                    class="w-full h-full object-cover opacity-25 mix-blend-luminosity" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#071A33]/95 via-[#0B2B5C]/70 to-[#071A33]/80"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-[#071A33]/80 via-transparent to-transparent"></div>
            </div>
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-titan-red/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-[1280px] mx-auto px-6">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs mb-4 text-white/50">
                    <a href="/" class="hover:text-white transition-colors flex items-center gap-1">
                        <x-lucide-home class="w-3.5 h-3.5" />
                        <span>{{ __('Home') }}</span>
                    </a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <a href="/documents" class="hover:text-white transition-colors">{{ __('Documents') }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <span class="text-white/80 font-medium truncate max-w-xs">{{ \Illuminate\Support\Str::limit($doc['title'], 35) }}</span>
                </nav>

                <!-- Badges -->
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-lg !text-white shadow-xs" style="background: var(--primary-color, #E31E24);">
                        {{ $categoryName }}
                    </span>
                    <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg bg-white/10 text-white/90 border border-white/15 backdrop-blur-md">
                        {{ strtoupper($doc['fileType'] ?? 'PDF') }}
                    </span>
                    @if($cloudProvider)
                        <span class="text-[11px] font-semibold tracking-wider px-2.5 py-1 rounded-lg bg-white/15 text-white border border-white/20 backdrop-blur-md">
                            🔗 {{ $cloudProvider }}
                        </span>
                    @endif
                    @if($doc['is_featured'])
                        <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            ★ {{ __('Featured') }}
                        </span>
                    @endif
                </div>

                <!-- Document Title -->
                <h1 class="font-heading font-[900] tracking-tight mb-4 max-w-4xl !text-white text-2xl sm:text-3xl md:text-4xl leading-[1.2] {{ app()->getLocale() === 'km' ? 'font-khmer' : '' }}">
                    {{ $doc['title'] }}
                </h1>

                <!-- Meta Details & CTAs Row -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 border-t border-white/10">
                    <div class="flex flex-wrap items-center gap-3.5 text-xs text-white/70">
                        @if($doc['fileSize'])
                            <span class="flex items-center gap-1.5 font-mono">
                                <x-lucide-hard-drive class="w-3.5 h-3.5 text-white/40" />
                                <span>{{ $doc['fileSize'] }}</span>
                            </span>
                            <span class="text-white/20">•</span>
                        @endif
                        <span class="flex items-center gap-1.5">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-white/40" />
                            <span>{{ $doc['date'] }}</span>
                        </span>
                        <span class="text-white/20">•</span>
                        <x-page-view-count light />
                        @if(($doc['downloadCount'] ?? 0) > 0)
                            <span class="text-white/20">•</span>
                            <span class="flex items-center gap-1.5">
                                <x-lucide-download class="w-3.5 h-3.5 text-white/40" />
                                <span>{{ number_format($doc['downloadCount']) }} {{ __('downloads') }}</span>
                            </span>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex items-center gap-2.5 self-start sm:self-auto shrink-0">
                        @if($fileUrl)
                            <a href="{{ $fileUrl }}" {{ $isExternal ? 'target="_blank" rel="noopener"' : 'download' }}
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 cursor-pointer"
                                style="background: var(--primary-color, #E31E24);">
                                @if($isExternal)
                                    <x-lucide-external-link class="w-3.5 h-3.5" />
                                    <span>{{ __('Open Document') }}</span>
                                @else
                                    <x-lucide-download class="w-3.5 h-3.5" />
                                    <span>{{ __('Download Document') }}</span>
                                @endif
                            </a>
                        @endif
                        <a href="/documents"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white/80 bg-white/10 hover:bg-white/15 hover:text-white border border-white/15 transition-all backdrop-blur-md">
                            <x-lucide-arrow-left class="w-3.5 h-3.5" />
                            <span>{{ __('Documents') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══ MAIN CONTENT ═══ -->
        <section class="py-10 md:py-14">
            <div class="max-w-[1280px] mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <!-- Left Column: Details & Document Previewer -->
                    <div class="lg:col-span-8 space-y-6">

                        <!-- Interactive Document Viewer / Preview -->
                        @if($embedUrl)
                            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden" x-data="{ isFullscreen: false }">
                                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-slate-50/70">
                                    <div class="flex items-center gap-2">
                                        <x-lucide-eye class="w-4 h-4 text-titan-red" />
                                        <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">{{ __('Interactive Document Viewer') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ $fileUrl }}" target="_blank" rel="noopener"
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-gray-600 hover:text-titan-navy transition-colors">
                                            <x-lucide-external-link class="w-3 h-3" />
                                            <span>{{ __('New Tab') }}</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-900 aspect-[4/3] sm:h-[550px] relative">
                                    <iframe src="{{ $embedUrl }}" class="w-full h-full border-0" allow="autoplay" loading="lazy"></iframe>
                                </div>
                            </div>
                        @elseif($thumbnailUrl)
                            <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-2xs">
                                <div class="aspect-[16/9] overflow-hidden bg-slate-900 flex items-center justify-center relative">
                                    <img src="{{ $thumbnailUrl }}" alt="{{ $doc['title'] }}"
                                        class="w-full h-full object-cover" loading="lazy" decoding="async" />
                                </div>
                            </div>
                        @endif

                        <!-- Description & Document Overview Card -->
                        @if(filled(trim(strip_tags($doc['description'] ?? ''))))
                            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2.5 bg-slate-50/60">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-red-50 text-titan-red">
                                        <x-lucide-file-text class="w-4 h-4" />
                                    </div>
                                    <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider">{{ __('Document Overview') }}</h2>
                                </div>
                                <div class="px-6 py-6 sm:px-8 sm:py-7">
                                    <div class="prose prose-slate max-w-none text-gray-700 leading-relaxed text-sm sm:text-base
                                        [&>p]:mb-4
                                        [&>ul]:space-y-2 [&>ul]:list-disc [&>ul]:pl-5 [&>ul]:mb-4
                                        [&>ol]:space-y-2 [&>ol]:list-decimal [&>ol]:pl-5 [&>ol]:mb-4
                                        [&_strong]:text-gray-900 [&_strong]:font-bold
                                        [&_a]:text-titan-red [&_a]:underline">
                                        {!! $doc['description'] !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Direct File Access Box -->
                        @if($fileUrl)
                            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 relative overflow-hidden">
                                <div class="flex items-center gap-4 relative z-10">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 bg-red-50 text-titan-red border border-red-100">
                                        @if($isExternal)
                                            <x-lucide-cloud class="w-6 h-6" />
                                        @else
                                            <x-lucide-file-down class="w-6 h-6" />
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-0.5">
                                            {{ $isExternal ? __('Access Cloud Document') : __('Download Document File') }}
                                        </h3>
                                        <p class="text-xs text-gray-500 font-mono">
                                            {{ strtoupper($doc['fileType'] ?? 'PDF') }}{{ filled($doc['fileSize']) ? ' · ' . $doc['fileSize'] : '' }}{{ $cloudProvider ? ' · ' . $cloudProvider : '' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5 w-full sm:w-auto relative z-10">
                                    <a href="{{ $fileUrl }}" {{ $isExternal ? 'target="_blank" rel="noopener"' : 'download' }}
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 h-11 px-6 rounded-xl text-xs font-bold text-white transition-all shadow-sm hover:shadow hover:-translate-y-0.5 cursor-pointer"
                                        style="background: var(--primary-color, #E31E24);">
                                        @if($isExternal)
                                            <x-lucide-external-link class="w-4 h-4" />
                                            <span>{{ __('Open in Cloud') }}</span>
                                        @else
                                            <x-lucide-download class="w-4 h-4" />
                                            <span>{{ __('Download File') }}</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column: Sidebar Specs & Actions -->
                    <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-24 h-fit">

                        <!-- Document Metadata Specifications -->
                        @php
                            $specs = array_filter([
                                ['icon' => 'lucide-folder', 'label' => __('Category'), 'value' => $categoryName],
                                ['icon' => 'lucide-file-type', 'label' => __('Format'), 'value' => filled($doc['fileType']) ? strtoupper($doc['fileType']) : null],
                                ['icon' => 'lucide-hard-drive', 'label' => __('File Size'), 'value' => filled($doc['fileSize']) ? $doc['fileSize'] : ($isExternal ? ($cloudProvider ? $cloudProvider . ' ' . __('Hosted') : __('Cloud Hosted')) : null)],
                                ['icon' => 'lucide-calendar', 'label' => __('Published Date'), 'value' => $doc['date']],
                                ['icon' => 'lucide-shield-check', 'label' => __('Verification'), 'value' => __('Official Kimmex Document')],
                                ['icon' => 'lucide-globe', 'label' => __('Storage Source'), 'value' => $isExternal ? ($cloudProvider ?: __('External Link')) : (filled($fileUrl) ? __('Direct Storage') : null)],
                            ], fn($item) => filled($item['value']));
                        @endphp

                        @if(!empty($specs))
                            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs p-5 sm:p-6">
                                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                                    <x-lucide-info class="w-4 h-4 text-titan-red" />
                                    <span>{{ __('Document Information') }}</span>
                                </h3>
                                <div class="space-y-3.5">
                                    @foreach($specs as $info)
                                        <div class="flex items-start gap-3 py-1 border-b border-gray-50 last:border-0">
                                            <div class="w-7 h-7 rounded-lg bg-slate-50 border border-gray-100 flex items-center justify-center shrink-0 text-gray-500 mt-0.5">
                                                <x-dynamic-component :component="$info['icon']" class="w-3.5 h-3.5" stroke-width="1.8" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $info['label'] }}</p>
                                                <p class="text-xs font-semibold text-gray-800 mt-0.5 truncate">{{ $info['value'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- 1-Click Share Widget -->
                        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs p-5 sm:p-6">
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <x-lucide-share-2 class="w-4 h-4 text-titan-red" />
                                <span>{{ __('Share Document') }}</span>
                            </h3>
                            <div class="flex gap-2" x-data="{ copied: false }">
                                <button @click="navigator.clipboard?.writeText(window.location.href); copied=true; setTimeout(()=>copied=false, 2000)"
                                    class="flex-1 h-10 rounded-xl border flex items-center justify-center gap-2 text-xs font-bold transition-all cursor-pointer"
                                    :class="copied ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'">
                                    <template x-if="!copied"><x-lucide-copy class="w-3.5 h-3.5 text-gray-400" /></template>
                                    <template x-if="copied"><x-lucide-check class="w-3.5 h-3.5 text-emerald-600" /></template>
                                    <span x-text="copied ? '{{ __('Document Link Copied!') }}' : '{{ __('Copy Link') }}'"></span>
                                </button>
                                <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($doc['title']) }}" target="_blank" rel="noopener"
                                    class="w-10 h-10 rounded-xl bg-[#229ED9] flex items-center justify-center text-white hover:brightness-110 transition-all shadow-xs"
                                    title="Share on Telegram">
                                    <x-social-icon network="telegram" class="w-4 h-4" />
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                    class="w-10 h-10 rounded-xl bg-[#1877F2] flex items-center justify-center text-white hover:brightness-110 transition-all shadow-xs"
                                    title="Share on Facebook">
                                    <x-social-icon network="facebook" class="w-4 h-4" />
                                </a>
                            </div>
                        </div>

                        <!-- Back to Document Library CTA -->
                        <a href="/documents"
                            class="flex items-center justify-center gap-2 w-full h-11 rounded-xl border border-gray-200/80 text-xs font-bold text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-all bg-white shadow-2xs">
                            <x-lucide-arrow-left class="w-4 h-4" />
                            <span>{{ __('Back to Document Library') }}</span>
                        </a>
                    </aside>
                </div>
            </div>
        </section>

        <!-- ═══ RELATED DOCUMENTS ═══ -->
        @if(count($relatedDocs) > 0)
            <section class="py-12 md:py-16 bg-white border-t border-gray-200/70">
                <div class="max-w-[1280px] mx-auto px-6">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-8 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Related') }}</span>
                        <h2 class="text-xl md:text-2xl font-heading font-black text-gray-900 tracking-tight">{{ __('More in') }} {{ $categoryName }}</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedDocs as $rel)
                            @php $relThumb = \App\Support\PublicStorage::urlIfExists($rel['thumbnailUrl']); @endphp
                            <a href="/documents/{{ $rel['slug'] }}"
                                class="group bg-slate-50/50 rounded-2xl border border-gray-200/80 overflow-hidden hover:bg-white hover:shadow-lg hover:border-titan-navy/30 transition-all duration-300 flex flex-col">
                                <div class="h-36 overflow-hidden flex items-center justify-center relative bg-slate-900">
                                    @if($relThumb)
                                        <img src="{{ $relThumb }}" alt="{{ $rel['title'] }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center" style="background: linear-gradient(135deg, #071A33 0%, #0B2B5C 100%);">
                                            <x-lucide-file-text class="w-8 h-8 text-white/60" />
                                        </div>
                                    @endif
                                    <div class="absolute top-3 left-3">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-white/95 text-slate-800 backdrop-blur-md shadow-xs">
                                            {{ strtoupper($rel['fileType'] ?? 'PDF') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-5 flex flex-col flex-1 justify-between">
                                    <div>
                                        <span class="text-[11px] font-bold uppercase tracking-wider mb-1.5 block" style="color: var(--primary-color, #E31E24);">
                                            {{ $rel['categoryName'] }}
                                        </span>
                                        <h3 class="text-sm font-bold text-gray-900 group-hover:text-titan-red transition-colors line-clamp-2 leading-snug mb-2">
                                            {{ $rel['title'] }}
                                        </h3>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-xs text-gray-400">
                                        <span>{{ $rel['fileSize'] ?? '' }}</span>
                                        <span class="font-medium text-titan-navy flex items-center gap-1 group-hover:text-titan-red transition-colors">
                                            {{ __('Read') }} ➔
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>

</x-layouts.app>
