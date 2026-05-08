@php
    use App\Models\Document;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Cache;

    /** @var string $slug */
    $locale = app()->getLocale();

    $doc = Cache::remember("document_show_data_{$slug}_{$locale}", now()->addHours(12), function() use ($slug, $locale) {
        $d = Document::with('documentCategory')
            ->where('isActive', true)
            ->where('slug', $slug)
            ->where('isPublic', true)
            ->whereHas('documentCategory', function ($q) {
                $q->where('isActive', true);
            })
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
            ->where('document_category_id', $doc['document_category_id'])
            ->where('id', '!=', $doc['id'])
            ->where('isPublic', true)
            ->where('isActive', true)
            ->whereHas('documentCategory', function ($q) {
                $q->where('isActive', true);
            })
            ->latest()
            ->take(4)
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

    <div class="bg-white min-h-screen text-titan-navy font-sans antialiased" x-data="{ 
        scrolled: false, 
        progress: 0,
        scrollY: 0,
        ticking: false
    }" x-init="
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    scrollY = window.scrollY;
                    scrolled = window.scrollY > 400;
                    const scrollTotal = document.documentElement.scrollHeight - window.innerHeight;
                    progress = scrollTotal > 0 ? (window.scrollY / scrollTotal) * 100 : 0;
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    ">

        <!-- READING PROGRESS & STICKY NAV -->
        <div class="fixed top-0 left-0 w-full z-[100] transition-transform duration-500"
            :class="scrolled ? 'translate-y-0' : '-translate-y-full'">
            <div class="h-1 bg-gray-100 w-full relative">
                <div class="h-full bg-titan-red absolute left-0 top-0 transition-all duration-150"
                    :style="'width: ' + progress + '%'"></div>
            </div>
            <div class="bg-white/95 backdrop-blur-md border-b border-gray-100 h-12 flex items-center px-6">
                <div class="max-w-[1240px] mx-auto w-full flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span
                            class="text-[9px] font-black text-titan-red uppercase tracking-widest hidden md:block">{{ __('Resource:') }}</span>
                        <span
                            class="text-[11px] font-black text-titan-navy truncate max-w-[200px] md:max-w-md uppercase tracking-tight">{{ $doc['title'] }}</span>
                    </div>
                    <div class="flex items-center gap-6">
                        @if($doc['fileUrl'])
                            <a href="{{ Storage::url($doc['fileUrl']) }}" download
                                class="hidden sm:flex items-center gap-2 bg-titan-red text-white px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-titan-navy transition-all">
                                <x-lucide-download class="w-3.5 h-3.5" />
                                {{ __('Download') }}
                            </a>
                        @endif
                        <a href="/documents"
                            class="w-8 h-8 bg-titan-navy text-white rounded-lg flex items-center justify-center hover:bg-titan-red transition-all"><x-lucide-arrow-left
                                class="w-4 h-4" /></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- === PREMIUM DOCUMENT HERO === -->
        <header class="relative h-[60vh] min-h-[500px] flex items-center justify-center overflow-hidden bg-titan-navy shadow-2xl">
            {{-- Background Zoom Animation --}}
            <div class="absolute inset-0">
                @if($doc['thumbnailUrl'])
                    <img src="{{ Storage::url($doc['thumbnailUrl']) }}" alt="{{ $doc['title'] }}" class="w-full h-full object-cover opacity-100 animate-slow-zoom" />
                @else
                    <div class="w-full h-full bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.15)_0%,transparent_50%)]"></div>
                @endif
                {{-- Deep multi-stage gradient for maximum text contrast --}}
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/60 via-transparent to-titan-navy/90"></div>
                <div class="absolute inset-0 bg-black/20"></div>
            </div>

            <div class="relative z-20 text-center max-w-5xl px-6 pt-10" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-8'"
                    class="transition-all duration-1000 delay-100 inline-flex items-center gap-3 px-6 py-3 glass-premium rounded-full text-white text-[10px] font-black uppercase tracking-[0.3em] mb-12">
                    <x-lucide-file-text class="w-4 h-4 text-titan-red animate-pulse" />
                    <span>{{ strtoupper($categoryName) }}</span>
                </div>

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-1000 delay-300 font-black text-white mb-8 leading-[1.1] tracking-tighter uppercase"
                    style="font-size: clamp(2rem, 6vw, 3.5rem);">
                    {{ $doc['title'] }}
                </h1>

                <div :class="shown ? 'opacity-100' : 'opacity-0'" class="transition-all duration-1000 delay-500 flex items-center justify-center gap-6">
                    <div class="h-[1px] w-12 bg-titan-red"></div>
                    <p class="text-[10px] md:text-xs text-white/90 font-bold uppercase tracking-[0.4em]">
                        {{ $doc['fileType'] ?? 'PDF' }} · {{ $doc['fileSize'] ?? '' }} · {{ $doc['created_at_formatted'] }}
                    </p>
                    <div class="h-[1px] w-12 bg-titan-red"></div>
                </div>
            </div>

        </header>

        <!-- MAIN ARCHITECTURE -->
        <div class="max-w-[1240px] mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 py-20 relative">



            <!-- MAIN CONTENT AREA -->
            <div class="lg:col-span-10 lg:col-start-2 xl:col-span-8 xl:col-start-3 space-y-16">
                <div class="reveal-up space-y-8">
                    <div class="flex flex-wrap items-center justify-center gap-y-4 gap-x-12 pt-8 border-t border-gray-100">
                        <div class="flex items-center gap-3 group/meta">
                            <div class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center group-hover/meta:bg-titan-red/10 transition-all duration-300">
                                <x-lucide-file-text class="w-4 h-4 text-titan-red" />
                            </div>
                            <span class="text-[10px] font-black text-titan-navy uppercase tracking-[0.2em]">{{ $doc['fileType'] ?? 'PDF' }}</span>
                        </div>
                        <div class="flex items-center gap-3 group/meta">
                            <div class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center group-hover/meta:bg-titan-red/10 transition-all duration-300">
                                <x-lucide-database class="w-4 h-4 text-titan-red" />
                            </div>
                            <span class="text-[10px] font-black text-titan-navy uppercase tracking-[0.2em]">{{ $doc['fileSize'] ?? '2.4 MB' }}</span>
                        </div>
                        <div class="flex items-center gap-3 group/meta">
                            <div class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center group-hover/meta:bg-titan-red/10 transition-all duration-300">
                                <x-lucide-calendar class="w-4 h-4 text-titan-red" />
                            </div>
                            <span class="text-[10px] font-black text-titan-navy uppercase tracking-[0.2em]">{{ $doc['created_at_formatted'] }}</span>
                        </div>
                        <div class="flex items-center gap-3 group/meta">
                            <div class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center group-hover/meta:bg-titan-red/10 transition-all duration-300">
                                <x-lucide-download class="w-4 h-4 text-titan-red" />
                            </div>
                            <span class="text-[10px] font-black text-titan-navy uppercase tracking-[0.2em]">{{ number_format($doc['downloadCount']) }} {{ __('Downloads') }}</span>
                        </div>
                    </div>

                    <div class="prose prose-lg md:prose-xl prose-slate max-w-none prose-p:text-titan-navy/70 prose-p:leading-[1.8] prose-p:font-medium prose-headings:font-black prose-headings:uppercase prose-headings:tracking-tighter prose-headings:text-titan-navy">
                        {!! $doc['description'] !!}
                    </div>
                </div>
                @if($doc['fileUrl'])
                    <!-- PREMIUM DOWNLOAD BOX -->
                    <div class="reveal-up mt-12 p-10 rounded-3xl bg-titan-navy flex flex-col items-center justify-center gap-8 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-titan-red/10 rounded-full blur-[80px] pointer-events-none group-hover:scale-110 transition-transform duration-1000"></div>
                        <div class="relative z-10 text-center">
                            <div class="text-[10px] font-black text-titan-red uppercase tracking-[0.4em] mb-3">
                                {{ __('Official Document') }}</div>
                            <h3 class="text-white font-black text-xl md:text-2xl uppercase tracking-tight mb-3">
                                {{ __('Secure Access') }}</h3>
                            <p class="text-white/40 text-sm max-w-md">
                                {{ __('This resource is available for download in its original format. Verified and secured by Kimmex Engineering.') }}
                            </p>
                        </div>
                        <a href="{{ Storage::url($doc['fileUrl']) }}" download target="_blank"
                            class="relative z-10 shrink-0 inline-flex items-center gap-3 bg-titan-red border border-titan-red/20 hover:bg-white hover:text-titan-navy text-white px-10 py-5 rounded-2xl font-black text-sm uppercase tracking-widest transition-all duration-300 shadow-xl hover:shadow-titan-red/20 group/dl">
                            <x-lucide-download class="w-5 h-5 transition-transform group-hover/dl:scale-110" />
                            {{ __('Download Now') }}
                        </a>
                    </div>
                @endif
                <!-- CENTERED SOCIAL SHARING -->
                <div class="reveal-up pt-12 flex flex-col items-center gap-6">
                    <div class="text-[10px] font-black text-titan-navy/20 uppercase tracking-[0.4em]">{{ __('Share this Document') }}</div>
                    <div class="flex items-center gap-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                            class="w-12 h-12 bg-social-facebook rounded-2xl flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-lg group/fb">
                            <x-lucide-facebook class="w-5 h-5 transition-transform group-hover/fb:scale-110" />
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                            class="w-12 h-12 bg-social-linkedin rounded-2xl flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-lg group/li">
                            <x-lucide-linkedin class="w-5 h-5 transition-transform group-hover/li:scale-110" />
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($doc['title']) }}" target="_blank" rel="noopener"
                            class="w-12 h-12 bg-social-telegram rounded-2xl flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-lg group/tg">
                            <x-lucide-send class="w-5 h-5 transition-transform group-hover/tg:scale-110" />
                        </a>
                        <div x-data="{ 
                            copied: false, 
                            copyLink() {
                                navigator.clipboard.writeText(window.location.href);
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            }
                        }" class="relative">
                            <button @click="copyLink()"
                                class="w-12 h-12 bg-white border border-gray-100 rounded-2xl flex items-center justify-center text-titan-navy hover:bg-titan-navy hover:text-white transition-all transform hover:-translate-y-1 shadow-lg group/link">
                                <x-lucide-link class="w-5 h-5" x-show="!copied" />
                                <x-lucide-check class="w-5 h-5 text-green-500" x-show="copied" x-cloak />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDEBAR (PREVIEW & INFO) -->
            <aside class="lg:col-span-3 space-y-12">
                <div class="sticky top-32 space-y-12">
                    <!-- Preview Image Card -->
                    <div class="reveal-up space-y-6">
                        <div class="text-[10px] font-black text-titan-navy/10 uppercase tracking-[0.4em] border-b border-gray-100 pb-4">
                            {{ __('Resource Preview') }}
                        </div>
                        <div class="rounded-3xl overflow-hidden border border-gray-100 shadow-2xl aspect-[3/4] bg-gray-50 relative group flex items-center justify-center">
                            @if($doc['thumbnailUrl'])
                                <img src="{{ Storage::url($doc['thumbnailUrl']) }}" alt="{{ $doc['title'] }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-[10s]" loading="lazy" />
                            @else
                                <div class="absolute inset-0 bg-[radial-gradient(rgba(11,43,92,0.03)_1px,transparent_1px)] [background-size:16px_16px]"></div>
                                <x-lucide-file-text class="w-20 h-20 text-titan-navy/5 group-hover:scale-110 group-hover:text-titan-red/20 transition-all duration-700" />
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/80 via-transparent to-transparent flex items-end p-8">
                                <div class="text-white/80 text-[10px] font-black uppercase tracking-[0.2em]">
                                    {{ $doc['fileType'] ?? 'DOCUMENT' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Small Info Box -->
                    <div class="reveal-up p-6 bg-gray-50 rounded-2xl border border-gray-100 space-y-4">
                        <div class="flex items-center gap-3">
                            <x-lucide-shield-check class="w-4 h-4 text-titan-red" />
                            <span class="text-[10px] font-black text-titan-navy uppercase tracking-widest">{{ __('Verified Resource') }}</span>
                        </div>
                        <p class="text-[11px] text-titan-navy/40 leading-relaxed">
                            {{ __('This document is an official publication of Kimmex Construction & Investment Co., Ltd. All rights reserved.') }}
                        </p>
                    </div>
                </div>
            </aside>
        </div>

        <!-- RELATED DOCUMENTS -->
        @if(count($relatedDocs) > 0)
            <section class="bg-gray-50 py-16 px-6">
                <div class="max-w-[1200px] mx-auto">
                    <div class="flex items-center gap-3 mb-10">
                        <span class="w-6 h-[2px] bg-titan-red"></span>
                        <span
                            class="text-[11px] font-black uppercase tracking-[0.35em] text-titan-navy/40">{{ __('Also Relevant') }}</span>
                    </div>
                    <h2 class="text-xl font-black text-titan-navy uppercase tracking-tight mb-8">
                        {{ __('Related Documents') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($relatedDocs as $rel)
                            @php $relCatName = $rel['categoryName']; @endphp
                            <a href="/documents/{{ $rel['slug'] }}"
                                class="group flex items-start gap-5 p-6 bg-white rounded-2xl border border-gray-100 hover:border-titan-red/20 hover:shadow-md transition-all duration-300">
                                <div
                                    class="w-14 h-14 rounded-xl overflow-hidden shrink-0 bg-gray-50 flex items-center justify-center relative">
                                    @if($rel['thumbnailUrl'])
                                        <img src="{{ Storage::url($rel['thumbnailUrl']) }}"
                                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy" />
                                    @else
                                        <x-lucide-file-text
                                            class="w-6 h-6 text-titan-navy/20 relative z-10 group-hover:text-titan-red transition-colors" />
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] font-black text-titan-red uppercase tracking-widest mb-1">
                                        {{ $relCatName }}</div>
                                    <h4
                                        class="font-bold text-sm text-titan-navy group-hover:text-titan-red transition-colors leading-snug">
                                        {{ $rel['title'] }}</h4>
                                    <div class="text-[11px] text-titan-navy/30 mt-1">{{ $rel['fileType'] ?? 'PDF' }}
                                        {{ $rel['fileSize'] ? '· ' . $rel['fileSize'] : '' }}</div>
                                </div>
                                <x-lucide-arrow-right
                                    class="w-4 h-4 text-titan-navy/20 group-hover:text-titan-red group-hover:translate-x-1 transition-all duration-300 shrink-0 mt-1" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

    <!-- REFINED STYLES -->
    <style>
        @keyframes revealUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reveal-up {
            animation: revealUp 0.8s ease-out forwards;
            opacity: 0;
        }

        @supports (view-timeline-name: --revealing) {
            .reveal-up {
                animation: revealUp both;
                animation-timeline: view();
                animation-range: entry 5% cover 25%;
            }
        }
    </style>

</x-layouts.app>