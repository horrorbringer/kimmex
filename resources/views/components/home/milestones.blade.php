@php
    $locale = app()->getLocale() === 'kh' ? 'km' : app()->getLocale();
    $milestones = \Illuminate\Support\Facades\Cache::remember('home_milestones_'.$locale, now()->addHours(12), function () use ($locale) {
        return \App\Models\Milestone::query()
            ->where('isActive', true)
            ->orderBy('sortOrder')
            ->get()
            ->map(function (\App\Models\Milestone $milestone, int $index) use ($locale): array {
                $fallbackImage = '/images/webp/projects/Thumbnail-'.(($index % 6) + 1).'.webp';

                return [
                    'year' => $milestone->year,
                    'title' => $milestone->getTranslation('title', $locale, false) ?: $milestone->getTranslation('title', 'en'),
                    'description' => \Illuminate\Support\Str::limit(trim(strip_tags($milestone->getTranslation('description', $locale, false) ?: $milestone->getTranslation('description', 'en'))), 88),
                    'image' => \App\Support\PublicStorage::urlIfExists($milestone->image, $fallbackImage),
                ];
            })
            ->values()
            ->all();
    });

    $roadColors = [
        ['text' => 'text-[#7C3AAD]', 'dot' => 'bg-[#7C3AAD]', 'border' => 'border-[#7C3AAD]/20'],
        ['text' => 'text-[#5130B6]', 'dot' => 'bg-[#5130B6]', 'border' => 'border-[#5130B6]/20'],
        ['text' => 'text-[#184FBA]', 'dot' => 'bg-[#184FBA]', 'border' => 'border-[#184FBA]/20'],
        ['text' => 'text-[#1686D9]', 'dot' => 'bg-[#1686D9]', 'border' => 'border-[#1686D9]/20'],
        ['text' => 'text-[#14A592]', 'dot' => 'bg-[#14A592]', 'border' => 'border-[#14A592]/20'],
        ['text' => 'text-[#16A34A]', 'dot' => 'bg-[#16A34A]', 'border' => 'border-[#16A34A]/20'],
        ['text' => 'text-[#E7A400]', 'dot' => 'bg-[#E7A400]', 'border' => 'border-[#E7A400]/20'],
        ['text' => 'text-[#F16F24]', 'dot' => 'bg-[#F16F24]', 'border' => 'border-[#F16F24]/20'],
        ['text' => 'text-[#D51B59]', 'dot' => 'bg-[#D51B59]', 'border' => 'border-[#D51B59]/20'],
    ];
    $roadPositions = [
        'lg:col-start-1 lg:row-start-1', 'lg:col-start-2 lg:row-start-1', 'lg:col-start-3 lg:row-start-1', 'lg:col-start-4 lg:row-start-1', 'lg:col-start-5 lg:row-start-1',
        'lg:col-start-5 lg:row-start-2', 'lg:col-start-4 lg:row-start-2', 'lg:col-start-3 lg:row-start-2', 'lg:col-start-2 lg:row-start-2',
    ];
@endphp

@if (! empty($milestones))
    <section class="overflow-hidden border-y border-titan-navy/10 bg-white py-16 md:py-24">
        <div class="mx-auto max-w-[1400px] px-6">
            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                class="mx-auto mb-12 max-w-2xl text-center transition-all duration-700 ease-out motion-reduce:transition-none lg:mb-16">
                <div class="mb-4 flex items-center justify-center gap-3">
                    <span class="h-px w-10 bg-titan-red"></span>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-titan-red">{{ __('Our Journey') }}</span>
                    <span class="h-px w-10 bg-titan-red"></span>
                </div>
                <h2 class="font-heading text-3xl font-black tracking-tight text-titan-navy md:text-5xl">{{ __('A legacy built milestone by milestone') }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-titan-navy/60">{{ __('From our first foundation to the projects shaping Cambodia today.') }}</p>
            </div>

            <div class="home-milestone-road relative mx-auto max-w-[1320px]">
                <svg class="pointer-events-none absolute inset-x-0 top-20 hidden h-[360px] w-full lg:block" viewBox="0 0 1320 360" fill="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="milestone-road-gradient" x1="30" y1="0" x2="1290" y2="0" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#8A35B5" />
                            <stop offset="0.3" stop-color="#2E34BC" />
                            <stop offset="0.52" stop-color="#1686D9" />
                            <stop offset="0.7" stop-color="#16B35B" />
                            <stop offset="0.84" stop-color="#F7A600" />
                            <stop offset="1" stop-color="#D51B59" />
                        </linearGradient>
                    </defs>
                    <path d="M68 82H1190C1264 82 1276 150 1200 176L205 280C106 290 75 340 146 350H1248" stroke="#0B2B5C" stroke-opacity="0.1" stroke-width="40" stroke-linecap="round" />
                    <path d="M68 82H1190C1264 82 1276 150 1200 176L205 280C106 290 75 340 146 350H1248" stroke="url(#milestone-road-gradient)" stroke-width="28" stroke-linecap="round" />
                </svg>

                <div class="relative grid gap-7 border-l border-titan-navy/10 pl-6 sm:grid-cols-2 lg:min-h-[430px] lg:grid-cols-5 lg:grid-rows-2 lg:gap-x-5 lg:gap-y-12 lg:border-l-0 lg:pl-0">
                    @foreach ($milestones as $index => $milestone)
                        @php
                            $color = $roadColors[$index % count($roadColors)];
                            $position = $roadPositions[$index] ?? 'lg:row-start-2';
                        @endphp
                        <article x-data="{ shown: false }" x-intersect.once="shown = true"
                            style="transition-delay: {{ min($index * 70, 420) }}ms"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
                            class="home-milestone-stop relative transition-all duration-700 ease-out motion-reduce:transition-none {{ $position }} {{ $index >= 5 ? 'lg:mt-12' : '' }}">
                            <span class="absolute -left-[31px] top-2 h-3 w-3 rounded-full ring-4 ring-white {{ $color['dot'] }} lg:left-1/2 lg:top-[75px] lg:z-10 lg:-translate-x-1/2"></span>
                            <div class="rounded-2xl border bg-white p-4 shadow-[0_14px_30px_-25px_rgba(11,43,92,0.45)] transition-[border-color,box-shadow] duration-300 hover:shadow-[0_20px_35px_-22px_rgba(11,43,92,0.35)] {{ $color['border'] }} lg:bg-white/95">
                                @if ($milestone['image'])
                                    <div class="mb-4 aspect-[16/7] overflow-hidden rounded-xl bg-titan-navy/5">
                                        <img src="{{ $milestone['image'] }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                                    </div>
                                @endif
                                <p class="font-heading text-xl font-black tracking-tight {{ $color['text'] }}">{{ $milestone['year'] }}</p>
                                <h3 class="mt-1 font-heading text-lg font-black tracking-tight text-titan-navy {{ app()->getLocale() === 'km' ? 'font-khmer text-xl' : '' }}">{{ $milestone['title'] }}</h3>
                                <p class="mt-2 text-xs leading-relaxed text-titan-navy/60">{{ $milestone['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="mt-10 text-center lg:mt-12">
                <a href="{{ url('/about#milestones') }}" class="group inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.16em] text-titan-red">
                    {{ __('Explore our complete history') }}
                    <x-lucide-arrow-right class="h-4 w-4 transition-transform duration-300 ease-out group-hover:translate-x-1 motion-reduce:transform-none" />
                </a>
            </div>
        </div>
    </section>
@endif
