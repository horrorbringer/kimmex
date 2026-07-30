@php
    $locale = app()->getLocale() === 'kh' ? 'km' : app()->getLocale();
    $milestones = \Illuminate\Support\Facades\Cache::remember('home_milestones_'.$locale, now()->addHours(12), function () use ($locale) {
        return \App\Models\Milestone::query()
            ->where('isActive', true)
            ->orderBy('sortOrder')
            ->select(['year', 'title', 'description', 'detailed_description', 'image'])
            ->get()
            ->map(function (\App\Models\Milestone $milestone, int $index) use ($locale): array {
                $fallbackImage = '/images/webp/projects/Thumbnail-'.(($index % 6) + 1).'.webp';

                return [
                    'year' => $milestone->year,
                    'title' => $milestone->getTranslation('title', $locale, false) ?: $milestone->getTranslation('title', 'en'),
                    'description' => \Illuminate\Support\Str::limit(trim(strip_tags($milestone->getTranslation('description', $locale, false) ?: $milestone->getTranslation('description', 'en'))), 64),
                    'detail' => $milestone->getTranslation('detailed_description', $locale, false) ?: $milestone->getTranslation('detailed_description', 'en'),
                    'image' => \App\Support\PublicStorage::urlIfExists($milestone->image, $fallbackImage),
                ];
            })
            ->values()
            ->all();
    });

    $roadColors = [
        ['text' => 'text-[#7C3AAD]', 'hex' => '#7C3AAD', 'border' => 'border-[#7C3AAD]/20'],
        ['text' => 'text-[#5130B6]', 'hex' => '#5130B6', 'border' => 'border-[#5130B6]/20'],
        ['text' => 'text-[#184FBA]', 'hex' => '#184FBA', 'border' => 'border-[#184FBA]/20'],
        ['text' => 'text-[#1686D9]', 'hex' => '#1686D9', 'border' => 'border-[#1686D9]/20'],
        ['text' => 'text-[#14A592]', 'hex' => '#14A592', 'border' => 'border-[#14A592]/20'],
        ['text' => 'text-[#16A34A]', 'hex' => '#16A34A', 'border' => 'border-[#16A34A]/20'],
        ['text' => 'text-[#E7A400]', 'hex' => '#E7A400', 'border' => 'border-[#E7A400]/20'],
        ['text' => 'text-[#F16F24]', 'hex' => '#F16F24', 'border' => 'border-[#F16F24]/20'],
        ['text' => 'text-[#D51B59]', 'hex' => '#D51B59', 'border' => 'border-[#D51B59]/20'],
    ];
    $roadLanes = [
        ['start' => 10, 'end' => 74, 'y' => 8],
        ['start' => 76, 'end' => 24, 'y' => 50],
        ['start' => 24, 'end' => 87, 'y' => 92],
    ];
    $yearsPerRoad = 4;
    $roadHeight = max(540, (int) ceil(count($milestones) / $yearsPerRoad) * 180);
@endphp

@if (! empty($milestones))
    <section class="overflow-visible border-y border-titan-navy/10 bg-white py-16 md:py-24">
        <div class="mx-auto max-w-[1400px] px-6">
            <div class="mx-auto mb-12 max-w-2xl text-center lg:mb-16">
                <div class="mb-4 flex items-center justify-center gap-3">
                    <span class="h-px w-10 bg-titan-red"></span>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-titan-red">{{ __('Our Journey') }}</span>
                    <span class="h-px w-10 bg-titan-red"></span>
                </div>
                <h2 class="font-heading text-3xl font-black tracking-tight text-titan-navy md:text-5xl">{{ __('A legacy built milestone by milestone') }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-titan-navy/60">{{ __('From our first foundation to the projects shaping Cambodia today.') }}</p>
            </div>

            <div x-data="{
                    active: false,
                    observer: null,
                    init() {
                        this.observer = new IntersectionObserver(([entry]) => {
                            if (entry.isIntersecting) {
                                this.active = true;
                                this.observer?.disconnect();
                            }
                        }, { threshold: 0.18 });
                        this.observer.observe(this.$el);
                    },
                    destroy() {
                        this.observer?.disconnect();
                    }
                }"
                x-init="init()"
                :class="active ? 'home-milestone-route-active' : ''"
                class="home-milestone-route relative mx-auto w-full pb-20 lg:pb-24">
                <svg class="pointer-events-none absolute inset-x-0 top-0 hidden w-full lg:block" style="height: {{ $roadHeight }}px" viewBox="0 0 1140 1000" preserveAspectRatio="none" fill="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="milestone-road-gradient" x1="0" y1="0" x2="1140" y2="1000" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#8A35B5" />
                            <stop offset="0.3" stop-color="#2E34BC" />
                            <stop offset="0.52" stop-color="#1686D9" />
                            <stop offset="0.7" stop-color="#16B35B" />
                            <stop offset="0.84" stop-color="#F7A600" />
                            <stop offset="1" stop-color="#D51B59" />
                        </linearGradient>
                    </defs>
                    <path d="M108 80H850C1045 80 1045 500 850 500H274C95 500 95 920 274 920H900C1050 920 1050 980 1090 980" stroke="#0B2B5C" stroke-opacity="0.13" stroke-width="48" stroke-linecap="round" stroke-linejoin="round" />
                    <path class="home-milestone-road-path" pathLength="1" d="M108 80H850C1045 80 1045 500 850 500H274C95 500 95 920 274 920H900C1050 920 1050 980 1090 980" stroke="url(#milestone-road-gradient)" stroke-width="32" stroke-linecap="round" stroke-linejoin="round" />
                    <path class="home-milestone-road-flow" d="M108 80H850C1045 80 1045 500 850 500H274C95 500 95 920 274 920H900C1050 920 1050 980 1090 980" stroke="white" stroke-opacity="0.6" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="home-milestone-mobile-track relative space-y-5 pl-6 lg:block lg:h-[var(--road-height)] lg:space-y-0 lg:pl-0" style="--road-height: {{ $roadHeight }}px">
                    @foreach ($milestones as $index => $milestone)
                        @php
                            $color = $roadColors[$index % count($roadColors)];
                            $roadLaneIndex = min(intdiv($index, $yearsPerRoad), count($roadLanes) - 1);
                            $roadLane = $roadLanes[$roadLaneIndex];
                            $itemsInRoad = min($yearsPerRoad, count($milestones) - ($roadLaneIndex * $yearsPerRoad));
                            $positionInRoad = $index % $yearsPerRoad;
                            $roadStop = [
                                'x' => $itemsInRoad === 1
                                    ? ($roadLane['start'] + $roadLane['end']) / 2
                                    : $roadLane['start'] + (($roadLane['end'] - $roadLane['start']) * ($positionInRoad / ($itemsInRoad - 1))),
                                'y' => $roadLane['y'],
                            ];
                            $detail = $milestone['detail'] ?? '';
                            $hasDetail = filled(trim(strip_tags((string) $detail)));
                        @endphp
                        <article x-data="{
                                pinShown: false,
                                cardShown: false,
                                detailOpen: false,
                                detailTrigger: null,
                                previewOpen: false,
                                hydrated: false,
                                reducedMotion: false,
                                observer: null,
                                init() {
                                    this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                                    this.hydrated = true;
                                    this.observer = new IntersectionObserver(([entry]) => {
                                        if (entry.isIntersecting) {
                                            this.pinShown = true;
                                            window.setTimeout(() => this.cardShown = true, this.reducedMotion ? 0 : 180);
                                            this.observer?.disconnect();
                                        }
                                    }, { threshold: 0.3, rootMargin: '0px 0px -10% 0px' });
                                    this.observer.observe(this.$el);
                                },
                                destroy() {
                                    this.observer?.disconnect();
                                },
                                openDetail(event) {
                                    this.detailTrigger = event.currentTarget;
                                    this.detailOpen = true;
                                    this.$nextTick(() => this.$refs.detailClose.focus());
                                },
                                closeDetail() {
                                    this.detailOpen = false;
                                    this.$nextTick(() => this.detailTrigger?.focus());
                                }
                            }"
                            x-init="init()"
                            :class="[
                                hydrated && !pinShown ? 'milestone-pin-waiting' : pinShown ? 'milestone-pin-visible' : '',
                                hydrated && !cardShown ? 'milestone-card-waiting' : cardShown ? 'milestone-card-visible' : ''
                            ]"
                            class="home-milestone-stop relative min-h-14 lg:pointer-events-none lg:absolute lg:inset-0 lg:min-h-0">
                            <div class="home-milestone-station-wrap relative z-10 -ml-14 w-fit lg:pointer-events-auto lg:absolute lg:ml-0 lg:left-[var(--road-stop-x)] lg:top-[var(--road-stop-y)] lg:-translate-x-1/2 lg:-translate-y-1/2"
                                style="--road-stop-x: {{ $roadStop['x'] }}%; --road-stop-y: {{ $roadStop['y'] }}%"
                                @mouseenter="previewOpen = true"
                                @mouseleave="previewOpen = false"
                                @focusin="previewOpen = true"
                                @focusout="previewOpen = false">
                                <button type="button"
                                    class="home-milestone-station home-milestone-card-pin home-milestone-pin inline-flex h-10 min-w-16 items-center justify-center rounded-full border-2 border-white px-2 text-center shadow-[0_8px_14px_-7px_rgba(11,43,92,0.55)] transition-transform duration-200 hover:scale-110 focus:outline-none focus-visible:ring-4 focus-visible:ring-titan-red/30"
                                    style="background-color: {{ $color['hex'] }}"
                                    aria-label="{{ __('Read story') }}: {{ $milestone['year'] }} — {{ $milestone['title'] }}"
                                    @if ($hasDetail)
                                        aria-haspopup="dialog"
                                        @click="openDetail($event)"
                                    @endif>
                                    <span class="home-milestone-pin-ring absolute inset-0 rounded-full border-2 border-white/80"></span>
                                    <span class="relative font-heading text-xs font-black tracking-tight text-white">{{ $milestone['year'] }}</span>
                                </button>

                                <div x-cloak x-show="previewOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-2 scale-95" class="pointer-events-none absolute bottom-[calc(100%+0.8rem)] left-1/2 z-30 hidden w-72 -translate-x-1/2 overflow-hidden rounded-2xl border border-titan-navy/10 bg-white p-4 shadow-[0_22px_50px_-24px_rgba(11,43,92,0.5)] lg:block">
                                    <div class="flex items-start gap-3">
                                        @if ($milestone['image'])
                                            <img src="{{ $milestone['image'] }}" alt="" class="h-14 w-16 shrink-0 rounded-lg object-cover" loading="lazy" decoding="async" />
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-heading text-sm font-black {{ $color['text'] }}">{{ $milestone['year'] }}</p>
                                            <h3 class="mt-0.5 line-clamp-2 font-heading text-base font-black leading-tight text-titan-navy {{ app()->getLocale() === 'km' ? 'font-khmer text-lg' : '' }}">{{ $milestone['title'] }}</h3>
                                        </div>
                                    </div>
                                    <p class="mt-3 line-clamp-2 text-xs leading-relaxed text-titan-navy/60">{{ $milestone['description'] }}</p>
                                    @if ($hasDetail)
                                        <span class="mt-3 inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-[0.14em] {{ $color['text'] }}">{{ __('Read story') }} <x-lucide-arrow-up-right class="h-3.5 w-3.5" /></span>
                                    @endif
                                </div>
                            </div>

                            @if ($hasDetail)
                                <div x-cloak x-show="detailOpen" @keydown.escape.window="closeDetail()" class="pointer-events-auto fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="milestone-detail-{{ $index }}">
                                    <div x-show="detailOpen" x-transition.opacity @click="closeDetail()" class="absolute inset-0 bg-titan-navy/80 backdrop-blur-sm"></div>
                                    <div x-show="detailOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-5 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-5 scale-95" @click.stop class="relative z-10 max-h-[88vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
                                        <button x-ref="detailClose" type="button" @click="closeDetail()" class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-titan-navy/5 text-titan-navy transition-colors hover:bg-titan-red hover:text-white focus:outline-none focus-visible:ring-4 focus-visible:ring-titan-red/30" aria-label="{{ __('Close') }}">
                                            <x-lucide-x class="h-5 w-5" />
                                        </button>
                                        <p class="pr-12 font-heading text-xl font-black {{ $color['text'] }}">{{ $milestone['year'] }}</p>
                                        <h3 id="milestone-detail-{{ $index }}" class="mt-1 pr-10 font-heading text-2xl font-black tracking-tight text-titan-navy sm:text-3xl {{ app()->getLocale() === 'km' ? 'font-khmer' : '' }}">{{ $milestone['title'] }}</h3>
                                        <div class="prose prose-sm mt-6 max-w-none leading-relaxed text-titan-navy/70 [&_a]:font-semibold [&_a]:text-titan-red [&_img]:rounded-xl [&_img]:shadow-sm">
                                            {!! $detail !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
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
