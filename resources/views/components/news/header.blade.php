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

            <div class="relative">
                <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-gray-200/80 bg-slate-900 shadow-md sm:shadow-[0_16px_48px_-8px_rgba(0,0,0,0.12)] aspect-[16/9] flex items-center justify-center group">
                    @if($article['image'])
                        {{-- Subtle ambient glow --}}
                        <img src="{{ $article['image'] }}" alt="" class="absolute inset-0 w-full h-full object-cover blur-2xl opacity-25 scale-110 pointer-events-none" aria-hidden="true" />
                        <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="relative z-10 w-full h-full object-contain md:object-cover transition-transform duration-500 group-hover:scale-[1.01]" decoding="async" loading="lazy" />
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.16)_0%,transparent_50%)]">
                            <x-lucide-newspaper class="w-20 h-20 text-white/10" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>
