@props(['milestonesData' => null])

@php
    $data = $milestonesData ?? app(\App\Services\HomePageService::class)->getMilestonesData();
    extract($data);
@endphp

@if (! empty($milestones))
    <section class="overflow-hidden border-y border-titan-navy/10 bg-[#f8fbff] py-16 md:py-24">
        <div class="mx-auto max-w-[1440px] px-6">
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
                    timelineFrame: null,
                    targetTimelineScrollLeft: 0,
                    init() {
                        this.observer = new IntersectionObserver(([entry]) => {
                            if (entry.isIntersecting) {
                                this.active = true;
                                this.observer.disconnect();
                            }
                        }, { threshold: 0.12 });
                        this.observer.observe(this.$el);
                    },
                    handleTimelineWheel(event) {
                        const timeline = this.$refs.desktopTimeline;

                        if (! timeline || ! window.matchMedia('(min-width: 1024px)').matches || Math.abs(event.deltaY) <= Math.abs(event.deltaX)) {
                            return;
                        }

                        const maximumScroll = Math.max(0, timeline.scrollWidth - timeline.clientWidth);
                        const scrollDelta = event.deltaMode === 1 ? event.deltaY * 16 : event.deltaY;
                        const currentTarget = Math.min(maximumScroll, Math.max(0, this.targetTimelineScrollLeft || timeline.scrollLeft));
                        const nextTarget = Math.min(maximumScroll, Math.max(0, currentTarget + scrollDelta));

                        if (maximumScroll === 0 || (scrollDelta < 0 && nextTarget === 0 && timeline.scrollLeft <= 0.5) || (scrollDelta > 0 && nextTarget === maximumScroll && timeline.scrollLeft >= maximumScroll - 0.5)) {
                            return;
                        }

                        event.preventDefault();
                        this.targetTimelineScrollLeft = nextTarget;

                        if (this.timelineFrame === null) {
                            this.animateTimelineScroll();
                        }
                    },
                    animateTimelineScroll() {
                        const timeline = this.$refs.desktopTimeline;

                        if (! timeline) {
                            this.timelineFrame = null;

                            return;
                        }

                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                            timeline.scrollLeft = this.targetTimelineScrollLeft;
                            this.timelineFrame = null;

                            return;
                        }

                        const distance = this.targetTimelineScrollLeft - timeline.scrollLeft;

                        if (Math.abs(distance) < 0.5) {
                            timeline.scrollLeft = this.targetTimelineScrollLeft;
                            this.timelineFrame = null;

                            return;
                        }

                        timeline.scrollLeft += distance * 0.32;
                        this.timelineFrame = window.requestAnimationFrame(() => this.animateTimelineScroll());
                    },
                    destroy() {
                        this.observer?.disconnect();
                        window.cancelAnimationFrame(this.timelineFrame);
                    }
                }"
                x-init="init()"
                @wheel="handleTimelineWheel($event)"
                :class="active ? 'home-milestone-route-active' : ''"
                class="home-milestone-route relative mx-auto">
                <div class="home-milestone-mobile-track relative space-y-5 border-l border-titan-navy/10 pl-6 lg:hidden">
                    @foreach ($milestones as $index => $milestone)
                        @php
                            $color = $roadColors[$index % count($roadColors)];
                            $detail = $milestone['detail'] ?? '';
                            $hasDetail = filled(trim(strip_tags((string) $detail)));
                        @endphp
                        <article x-data="{ detailOpen: false, detailTrigger: null, openDetail(event) { this.detailTrigger = event.currentTarget; this.detailOpen = true; this.$nextTick(() => this.$refs.detailClose.focus()); }, closeDetail() { this.detailOpen = false; this.$nextTick(() => this.detailTrigger?.focus()); } }" class="home-milestone-mobile-item relative" style="--milestone-delay: {{ $index * 110 }}ms">
                            <span class="absolute -left-[2.05rem] top-5 z-10 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white" style="background-color: {{ $color }}"><span class="absolute inset-0 rounded-full border-2" style="border-color: {{ $color }}"></span></span>
                            <button type="button" aria-label="{{ $milestone['title'] }}" @if ($hasDetail) @click="openDetail($event)" @endif class="home-milestone-story-card block w-full overflow-hidden rounded-2xl border border-titan-navy/10 bg-white p-3 text-left shadow-[0_14px_30px_-25px_rgba(11,43,92,0.45)] {{ $hasDetail ? 'cursor-pointer' : 'cursor-default' }}">
                                <div class="flex gap-4">
                                    @if ($milestone['image'])<img src="{{ $milestone['image'] }}" @if (filled($milestone['imageSrcset'])) srcset="{{ $milestone['imageSrcset'] }}" @endif sizes="(min-width: 1024px) 64px, 80px" width="80" height="64" alt="" class="h-16 w-20 shrink-0 rounded-xl object-cover" loading="lazy" decoding="async" />@endif
                                    <div><p class="font-heading text-lg font-black text-titan-navy">{{ $milestone['year'] }}</p><h3 class="mt-1 font-heading !text-sm font-black text-titan-navy {{ app()->getLocale() === 'km' ? 'font-khmer text-base' : '' }}">{{ $milestone['title'] }}</h3></div>
                                </div>
                            </button>
                            @if ($hasDetail)
                                @include('components.home.milestone-dialog', ['index' => $index, 'milestone' => $milestone, 'detail' => $detail])
                            @endif
                        </article>
                    @endforeach
                </div>

                <div x-ref="desktopTimeline" aria-label="{{ __('Company milestones timeline') }}" class="home-milestone-blueprint home-milestone-desktop-scroll hidden overflow-x-auto pb-6 lg:block">
                        <div class="relative" style="width: {{ $roadWidth }}px; height: {{ $roadHeight }}px">
                        <svg class="pointer-events-none absolute inset-0 h-full w-full" viewBox="0 0 {{ $roadWidth }} {{ $roadHeight }}" fill="none" aria-hidden="true">
                            <defs><linearGradient id="milestone-road-gradient" x1="0" y1="0" x2="{{ $roadWidth }}" y2="600" gradientUnits="userSpaceOnUse"><stop stop-color="#174EA6" /><stop offset="0.4" stop-color="#2E8CE0" /><stop offset="0.7" stop-color="#18A957" /><stop offset="1" stop-color="#D89D13" /></linearGradient></defs>
                            <path d="{{ $roadPath }}" stroke="#0B2B5C" stroke-opacity="0.12" stroke-width="42" stroke-linecap="round" />
                            <path class="home-milestone-road-path" pathLength="1" d="{{ $roadPath }}" stroke="url(#milestone-road-gradient)" stroke-width="28" stroke-linecap="round" />
                            <path class="home-milestone-road-flow" d="{{ $roadPath }}" stroke="white" stroke-opacity="0.6" stroke-width="3" stroke-linecap="round" />
                        </svg>

                    @foreach ($milestones as $index => $milestone)
                        @php
                            $color = $roadColors[$index % count($roadColors)];
                            $stop = $roadStops[$index];
                            $detail = $milestone['detail'] ?? '';
                            $hasDetail = filled(trim(strip_tags((string) $detail)));
                        @endphp
                        <article x-data="{ detailOpen: false, detailTrigger: null, openDetail(event) { this.detailTrigger = event.currentTarget; this.detailOpen = true; this.$nextTick(() => this.$refs.detailClose.focus()); }, closeDetail() { this.detailOpen = false; this.$nextTick(() => this.detailTrigger?.focus()); } }" class="home-milestone-stop absolute left-[var(--stop-x)] top-[var(--stop-y)] w-[15rem] -translate-x-1/2" style="--stop-x: {{ $stop['x'] }}px; --stop-y: {{ $stop['y'] }}px; --milestone-delay: {{ $index * 110 }}ms">
                            <span class="home-milestone-station absolute left-1/2 top-0 z-20 flex h-11 w-11 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-[5px] border-white shadow-[0_5px_15px_rgba(11,43,92,0.28)]" style="background-color: {{ $color }}"><span class="h-3 w-3 rounded-full bg-white"></span></span>
                            <span class="absolute left-1/2 top-[var(--connector-top)] h-[var(--connector-height)] w-0.5 -translate-x-1/2" style="--connector-top: {{ min(0, $stop['cardOffset']) }}px; --connector-height: {{ abs($stop['cardOffset']) }}px; background-color: {{ $color }}"></span>
                            <button type="button" aria-label="{{ $milestone['title'] }}" @if ($hasDetail) @click="openDetail($event)" @endif class="home-milestone-story-card absolute left-1/2 top-[var(--card-y)] block w-[12.5rem] -translate-x-1/2 overflow-hidden rounded-2xl border border-titan-navy/10 bg-white/95 p-2.5 text-left shadow-[0_18px_32px_-24px_rgba(11,43,92,0.42)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_38px_-22px_rgba(11,43,92,0.42)] {{ $hasDetail ? 'cursor-pointer' : 'cursor-default' }}" style="--card-y: {{ $stop['cardOffset'] }}px">
                                @if ($loop->last)
                                    <span class="absolute right-3 top-3 rounded-full bg-titan-red px-2 py-1 text-[9px] font-bold uppercase tracking-[0.12em] text-white shadow-sm">{{ __('Latest') }}</span>
                                @endif
                                <div class="flex items-center gap-3">
                                    @if ($milestone['image'])<img src="{{ $milestone['image'] }}" @if (filled($milestone['imageSrcset'])) srcset="{{ $milestone['imageSrcset'] }}" @endif sizes="(min-width: 1024px) 64px, 80px" width="64" height="64" alt="" class="h-16 w-16 shrink-0 rounded-xl object-cover" loading="lazy" decoding="async" />@endif
                                    <div class="min-w-0"><p class="font-heading text-base font-black leading-none text-titan-navy">{{ $milestone['year'] }}</p><span class="mt-2 block h-0.5 w-5 bg-titan-red"></span><p class="mt-2 line-clamp-2 font-heading text-xs font-black leading-snug text-titan-navy {{ app()->getLocale() === 'km' ? 'font-khmer text-sm' : '' }}">{{ $milestone['title'] }}</p></div>
                                </div>
                            </button>
                            @if ($hasDetail)
                                @include('components.home.milestone-dialog', ['index' => $index, 'milestone' => $milestone, 'detail' => $detail])
                            @endif
                        </article>
                    @endforeach
                        </div>
                </div>
            </div>

            <div class="mt-10 text-center lg:mt-12"><a href="{{ url('/about#milestones') }}" class="group inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.16em] text-titan-red">{{ __('Explore our complete history') }}<x-lucide-arrow-right class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" /></a></div>
        </div>
    </section>
@endif
