@props(['article'])

<header {{ $attributes->merge(['class' => 'border-b border-gray-200/80 bg-white']) }}>
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 py-4 sm:py-6 md:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-[1.05fr_0.95fr] gap-4 sm:gap-6 lg:gap-12 items-center">
            <div>
                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1.5 sm:gap-x-3 sm:gap-y-2 text-[11px] sm:text-xs text-titan-navy/55 mb-2.5 sm:mb-4 font-medium">
                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md bg-titan-red text-white text-[10px] sm:text-[11px] font-bold uppercase tracking-wider shadow-2xs">
                        {{ $article['category'] }}
                    </span>

                    @php
                        $displayDate = $article['dateRelative'] 
                            ?? (!empty($article['publishedAt']) 
                                ? \Illuminate\Support\Carbon::parse($article['publishedAt'])->locale(app()->getLocale())->diffForHumans() 
                                : ($article['date'] ?? ''));
                    @endphp
                    @if(!empty($displayDate))
                        <span class="inline-flex items-center gap-1 sm:gap-1.5 text-titan-navy/60">
                            <x-lucide-calendar class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-titan-navy/40" />
                            <span>{{ $displayDate }}</span>
                        </span>

                        <span class="text-gray-300">·</span>
                    @endif

                    <span class="inline-flex items-center gap-1 sm:gap-1.5 text-titan-navy/60">
                        <x-lucide-clock class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-titan-navy/40" />
                        <span>{{ $article['readTime'] }}</span>
                    </span>

                    <span class="text-gray-300">·</span>

                    <span class="inline-flex items-center gap-1 sm:gap-1.5 text-titan-navy/60">
                        <x-page-view-count class="text-titan-navy/60 font-medium normal-case tracking-normal text-[11px] sm:text-xs" />
                    </span>
                </div>

                <h1 class="!text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-titan-navy leading-snug tracking-tight">
                    {{ $article['title'] }}
                </h1>

                @if(!empty($article['excerpt']))
                    <p class="mt-1.5 sm:mt-2.5 text-xs sm:text-sm leading-relaxed text-titan-navy/65 font-normal">
                        {{ $article['excerpt'] }}
                    </p>
                @endif

                <div class="mt-3 sm:mt-5 flex flex-wrap items-center justify-between gap-3 pt-3 sm:pt-4 border-t border-gray-100">
                    {{-- Author Info --}}
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-tr from-titan-navy to-titan-navy/80 text-white flex items-center justify-center font-bold text-xs sm:text-sm shadow-xs ring-2 ring-white">
                            {{ strtoupper(substr($article['author'] ?? 'K', 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-titan-red">{{ __('Written by') }}</div>
                            <div class="text-xs sm:text-sm font-bold text-titan-navy leading-tight">{{ $article['author'] }}</div>
                        </div>
                    </div>

                    {{-- Read Action --}}
                    <div class="flex items-center gap-2">
                        <a href="#article-body" class="inline-flex items-center gap-1.5 sm:gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg bg-titan-navy/5 hover:bg-titan-red hover:!text-white text-titan-navy text-[11px] sm:text-xs font-semibold transition-all group">
                            <x-lucide-arrow-down-circle class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-titan-red group-hover:!text-white transition-colors" />
                            <span>{{ __('Start Reading') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="relative" x-data="{
                previewOpen: false,
                shareOpen: false,
                copied: false,
                copyUrl() {
                    navigator.clipboard.writeText(window.location.href);
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                }
            }">
                <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-gray-200/80 bg-slate-900 shadow-md sm:shadow-[0_16px_48px_-8px_rgba(0,0,0,0.12)] aspect-[16/9] flex items-center justify-center group">
                    @if($article['image'])
                        {{-- Subtle ambient glow --}}
                        <img src="{{ $article['image'] }}" alt="" class="absolute inset-0 w-full h-full object-cover blur-2xl opacity-25 scale-110 pointer-events-none" aria-hidden="true" />
                        <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="relative z-10 w-full h-full object-contain md:object-cover transition-transform duration-500 group-hover:scale-[1.01]" decoding="async" loading="lazy" />

                        {{-- Floating Action Bar (Top Right) --}}
                        <div class="absolute top-3 right-3 sm:top-4 sm:right-4 z-20 flex items-center gap-1.5 sm:gap-2">
                            <button type="button" 
                                @click="previewOpen = true" 
                                class="h-8 sm:h-8.5 px-2.5 sm:px-3 rounded-full bg-slate-950/70 hover:bg-slate-950 text-white backdrop-blur-md border border-white/20 text-[10px] sm:text-xs font-bold inline-flex items-center gap-1.5 transition-all shadow-md hover:scale-105 active:scale-95 cursor-pointer" 
                                title="{{ __('Preview Cover Image') }}">
                                <x-lucide-maximize-2 class="w-3.5 h-3.5 text-titan-red" />
                                <span>{{ __('Preview') }}</span>
                            </button>

                            <div class="relative" @click.outside="shareOpen = false">
                                <button type="button" 
                                    @click="shareOpen = !shareOpen" 
                                    class="h-8 sm:h-8.5 px-2.5 sm:px-3 rounded-full bg-slate-950/70 hover:bg-slate-950 text-white backdrop-blur-md border border-white/20 text-[10px] sm:text-xs font-bold inline-flex items-center gap-1.5 transition-all shadow-md hover:scale-105 active:scale-95 cursor-pointer"
                                    title="{{ __('Share Story') }}">
                                    <x-lucide-share-2 class="w-3.5 h-3.5 text-titan-red" />
                                    <span>{{ __('Share') }}</span>
                                </button>

                                {{-- Share Dropdown --}}
                                <div x-show="shareOpen" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                    class="absolute right-0 mt-2 w-48 rounded-xl bg-white/95 backdrop-blur-md p-2 shadow-xl border border-gray-100 text-titan-navy z-30 space-y-1"
                                    style="display: none;">
                                    
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/news/' . $article['slug'])) }}" 
                                        target="_blank" rel="noopener"
                                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-social-facebook/10 hover:text-social-facebook transition-colors">
                                        <x-social-icon network="facebook" class="w-4 h-4 text-social-facebook" />
                                        <span>Facebook</span>
                                    </a>
                                    <a href="https://t.me/share/url?url={{ urlencode(url('/news/' . $article['slug'])) }}&text={{ urlencode($article['title']) }}" 
                                        target="_blank" rel="noopener"
                                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-social-telegram/10 hover:text-social-telegram transition-colors">
                                        <x-social-icon network="telegram" class="w-4 h-4 text-social-telegram" />
                                        <span>Telegram</span>
                                    </a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url('/news/' . $article['slug'])) }}" 
                                        target="_blank" rel="noopener"
                                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-social-linkedin/10 hover:text-social-linkedin transition-colors">
                                        <x-social-icon network="linkedin" class="w-4 h-4 text-social-linkedin" />
                                        <span>LinkedIn</span>
                                    </a>
                                    <button type="button" 
                                        @click="copyUrl()" 
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-titan-red/10 hover:text-titan-red transition-colors text-left cursor-pointer">
                                        <div class="flex items-center gap-2.5">
                                            <x-lucide-link class="w-4 h-4 text-titan-red" />
                                            <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy Link') }}'"></span>
                                        </div>
                                        <x-lucide-check x-show="copied" class="w-3.5 h-3.5 text-emerald-600" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.16)_0%,transparent_50%)]">
                            <x-lucide-newspaper class="w-20 h-20 text-white/10" />
                        </div>
                    @endif
                </div>

                {{-- Fullscreen Lightbox Modal --}}
                @if($article['image'])
                    <template x-teleport="body">
                        <div x-show="previewOpen" 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            @keydown.escape.window="previewOpen = false"
                            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 backdrop-blur-md p-4 sm:p-6"
                            style="display: none;">
                            
                            <div class="relative max-w-5xl w-full bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-white/10"
                                @click.outside="previewOpen = false">
                                
                                {{-- Modal Header --}}
                                <div class="flex items-center justify-between px-5 py-4 border-b border-white/10 bg-slate-950/80">
                                    <div class="text-white font-bold text-sm sm:text-base truncate pr-4">
                                        {{ $article['title'] }}
                                    </div>
                                    <button type="button" 
                                        @click="previewOpen = false"
                                        class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors shrink-0 cursor-pointer">
                                        <x-lucide-x class="w-4 h-4" />
                                    </button>
                                </div>

                                {{-- Image View --}}
                                <div class="p-2 sm:p-4 bg-black flex items-center justify-center max-h-[75vh] overflow-hidden">
                                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="max-h-[70vh] w-auto max-w-full object-contain rounded-lg shadow-md" />
                                </div>

                                {{-- Modal Footer with Share links --}}
                                <div class="px-5 py-3.5 bg-slate-950/80 border-t border-white/10 flex flex-wrap items-center justify-between gap-3 text-xs">
                                    <span class="text-white/60 font-medium">{{ __('Share this story:') }}</span>
                                    <div class="flex items-center gap-2">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/news/' . $article['slug'])) }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-social-facebook text-white flex items-center justify-center hover:opacity-90 transition-opacity" title="Facebook">
                                            <x-social-icon network="facebook" class="w-4 h-4" />
                                        </a>
                                        <a href="https://t.me/share/url?url={{ urlencode(url('/news/' . $article['slug'])) }}&text={{ urlencode($article['title']) }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-social-telegram text-white flex items-center justify-center hover:opacity-90 transition-opacity" title="Telegram">
                                            <x-social-icon network="telegram" class="w-4 h-4" />
                                        </a>
                                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url('/news/' . $article['slug'])) }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-social-linkedin text-white flex items-center justify-center hover:opacity-90 transition-opacity" title="LinkedIn">
                                            <x-social-icon network="linkedin" class="w-4 h-4" />
                                        </a>
                                        <button type="button" @click="copyUrl()" class="h-8 px-3 rounded-lg bg-white/10 hover:bg-white/20 text-white font-medium inline-flex items-center gap-1.5 transition-colors cursor-pointer">
                                            <x-lucide-link class="w-3.5 h-3.5 text-titan-red" />
                                            <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy Link') }}'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                @endif
            </div>
        </div>
    </div>
</header>
