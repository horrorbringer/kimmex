<section class="py-20 bg-titan-navy overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-6 mb-12">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 transition-all duration-1000">
            <div>
                <span
                    class="text-accent-orange font-bold uppercase tracking-widest text-sm mb-2 block">{{ __('Our Partners') }}</span>
                <h2 class="text-3xl md:text-4xl font-heading font-black text-white">
                    {{ __('Trusted By Leading Institutions') }}</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-center px-6 py-3 bg-white/10 rounded-lg">
                    <div class="text-2xl font-black text-accent-orange">50+</div>
                    <div class="text-[10px] uppercase tracking-widest text-white/60 font-bold">{{ __('Partners') }}
                    </div>
                </div>
                <div class="text-center px-6 py-3 bg-white/10 rounded-lg">
                    <div class="text-2xl font-heading font-black text-accent-orange">25+</div>
                    <div class="text-[10px] uppercase tracking-widest text-white/60 font-bold">{{ __('Years Trust') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $partnersDb = \App\Models\Partner::orderBy('orderIndex')->get();

        $fallbacks = [1, 2, 3, 4, 5, 6, 7, 9, 10, 11];
        
        $partners = $partnersDb->map(function ($p, $index) use ($fallbacks) {
            $fallbackLogo = "/patner/" . $fallbacks[$index % count($fallbacks)] . ".png";
            return [
                'name' => $p->name,
                'logo' => $p->logoUrl ? \Illuminate\Support\Facades\Storage::url($p->logoUrl) : $fallbackLogo
            ];
        })->toArray();

        // Fallback if no records in DB
        if (empty($partners)) {
            $partners = [];
            for ($i = 0; $i < count($fallbacks); $i++) {
                $partners[] = ['name' => "Partner " . ($i+1), 'logo' => "/patner/" . $fallbacks[$i] . ".png"];
            }
        }
    @endphp

    <!-- Partner Logos Row 1 (assuming public/patner/1.png exists based on React code) -->
    <div class="relative mb-6 flex overflow-x-hidden group">
        <!-- We use two identical divs for a seamless infinite marquee effect in Alpine/CSS -->
        <div class="flex animate-marquee group-hover:[animation-play-state:paused] whitespace-nowrap">
            @foreach($partners as $p)
                <div
                    class="w-44 h-20 mx-4 bg-white rounded-xl flex items-center justify-center p-4 hover:scale-105 transition-transform duration-300 cursor-pointer relative shrink-0">
                    <img src="{{ $p['logo'] }}" alt="{{ $p['name'] }}" title="{{ $p['name'] }}"
                        class="object-contain w-full h-full opacity-70 hover:opacity-100 transition-opacity p-2" />
                </div>
            @endforeach
            <!-- Duplicate for infinite scroll loop -->
            @foreach($partners as $p)
                <div
                    class="w-44 h-20 mx-4 bg-white rounded-xl flex items-center justify-center p-4 hover:scale-105 transition-transform duration-300 cursor-pointer relative shrink-0">
                    <img src="{{ $p['logo'] }}" alt="{{ $p['name'] }}" title="{{ $p['name'] }}"
                        class="object-contain w-full h-full opacity-70 hover:opacity-100 transition-opacity p-2" />
                </div>
            @endforeach
        </div>
    </div>

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(-50%);
            }

            /* since the track contains duplicated items, translate -50% shifts exactly the original sequence length */
        }

        .animate-marquee {
            animation: marquee 25s linear infinite;
            width: 200%;
            /* forces track to properly hold duplicated content inline */
        }
    </style>
</section>