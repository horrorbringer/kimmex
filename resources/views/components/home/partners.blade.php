@php
    $fallbacks = [1, 2, 3, 4, 5, 6, 7, 9, 10, 11];

    $partners = \Illuminate\Support\Facades\Cache::remember('home_partners_array_' . app()->getLocale(), now()->addHours(12), function() use ($fallbacks) {
        $partnersDb = \App\Models\Partner::where('isActive', true)->orderBy('orderIndex')->get();
        return $partnersDb->map(function ($p, $index) use ($fallbacks) {
            $fallbackLogo = "/partners/" . $fallbacks[$index % count($fallbacks)] . ".png";
            $logo = $p->logoUrl;
            $logoUrl = \App\Support\PublicStorage::urlIfExists($logo, $fallbackLogo);
            return [
                'name' => $p->getTranslation('name', app()->getLocale()),
                'logo' => $logoUrl,
                'website' => $p->website
            ];
        })->toArray();
    });

    if (empty($partners)) {
        $partners = [];
        for ($i = 0; $i < count($fallbacks); $i++) {
            $partners[] = ['name' => "Partner " . ($i+1), 'logo' => "/partners/" . $fallbacks[$i] . ".png", 'website' => null];
        }
    }
@endphp

<section class="py-10 md:py-14 bg-white border-t border-gray-100 overflow-hidden">
    <div class="max-w-[1280px] mx-auto px-6 mb-10">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 transition-all duration-1000">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                    <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Our Partners') }}</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-heading font-black text-gray-900 tracking-tight">
                    {{ __('Trusted By Leading Institutions') }}
                </h2>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-center">
                    <div class="text-2xl font-black text-gray-900">50+</div>
                    <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">{{ __('Partners') }}</div>
                </div>
                <div class="w-px h-8 bg-gray-200"></div>
                <div class="text-center">
                    <div class="text-2xl font-black text-gray-900">25+</div>
                    <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">{{ __('Years') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Marquee --}}
    <div class="relative flex overflow-x-hidden">
        <div class="partner-marquee flex whitespace-nowrap">
            @foreach($partners as $p)
                @if($p['website'])
                    <a href="{{ $p['website'] }}" target="_blank" rel="noopener noreferrer"
                        class="w-40 h-16 mx-3 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center p-3 hover:border-gray-200 hover:shadow-sm transition-all duration-300 shrink-0">
                        <img src="{{ $p['logo'] }}" alt="{{ $p['name'] }}" title="{{ $p['name'] }}" width="160" height="64"
                            class="object-contain w-full h-full opacity-70 hover:opacity-100 transition-opacity" loading="lazy" decoding="async" />
                    </a>
                @else
                    <div class="w-40 h-16 mx-3 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center p-3 hover:border-gray-200 hover:shadow-sm transition-all duration-300 shrink-0">
                        <img src="{{ $p['logo'] }}" alt="{{ $p['name'] }}" title="{{ $p['name'] }}" width="160" height="64"
                            class="object-contain w-full h-full opacity-70 hover:opacity-100 transition-opacity" loading="lazy" decoding="async" />
                    </div>
                @endif
            @endforeach
            {{-- Duplicate for seamless loop --}}
            @foreach($partners as $p)
                @if($p['website'])
                    <a href="{{ $p['website'] }}" target="_blank" rel="noopener noreferrer"
                        class="w-40 h-16 mx-3 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center p-3 hover:border-gray-200 hover:shadow-sm transition-all duration-300 shrink-0">
                        <img src="{{ $p['logo'] }}" alt="{{ $p['name'] }}" title="{{ $p['name'] }}" width="160" height="64"
                            class="object-contain w-full h-full opacity-70 hover:opacity-100 transition-opacity" loading="lazy" decoding="async" />
                    </a>
                @else
                    <div class="w-40 h-16 mx-3 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center p-3 hover:border-gray-200 hover:shadow-sm transition-all duration-300 shrink-0">
                        <img src="{{ $p['logo'] }}" alt="{{ $p['name'] }}" title="{{ $p['name'] }}" width="160" height="64"
                            class="object-contain w-full h-full opacity-70 hover:opacity-100 transition-opacity" loading="lazy" decoding="async" />
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <style>
        @keyframes partnerScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .partner-marquee {
            animation: partnerScroll 30s linear infinite;
            width: 200%;
        }
        .partner-marquee:hover {
            animation-play-state: paused;
        }
    </style>
</section>
