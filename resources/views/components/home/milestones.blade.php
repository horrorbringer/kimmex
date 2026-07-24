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
                    'description' => \Illuminate\Support\Str::limit(trim(strip_tags($milestone->getTranslation('description', $locale, false) ?: $milestone->getTranslation('description', 'en'))), 64),
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
    $roadPinOffsets = [-230, 166, -10, -61, 98, 63, -24, 120, 210];
@endphp

@if (! empty($milestones))
    <section class="overflow-hidden border-y border-titan-navy/10 bg-white py-16 md:py-24">
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
                    init() {
                        const observer = new IntersectionObserver(([entry]) => {
                            if (entry.isIntersecting) {
                                this.active = true;
                                observer.disconnect();
                            }
                        }, { threshold: 0.18 });
                        observer.observe(this.$el);
                    }
                }"
                x-init="init()"
                :class="active ? 'home-milestone-route-active' : ''"
                class="home-milestone-route relative mx-auto max-w-[1140px]">
                <svg class="pointer-events-none absolute left-1/2 top-0 hidden h-[1512px] w-[620px] -translate-x-1/2 lg:block" viewBox="0 0 620 1512" fill="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="milestone-road-gradient" x1="0" y1="0" x2="620" y2="1512" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#8A35B5" />
                            <stop offset="0.3" stop-color="#2E34BC" />
                            <stop offset="0.52" stop-color="#1686D9" />
                            <stop offset="0.7" stop-color="#16B35B" />
                            <stop offset="0.84" stop-color="#F7A600" />
                            <stop offset="1" stop-color="#D51B59" />
                        </linearGradient>
                    </defs>
                    <path d="M80 84C215 35 410 50 470 160C535 278 410 340 300 420C200 495 205 610 330 675C455 740 468 842 375 920C290 990 242 1080 340 1170C412 1235 470 1320 520 1428" stroke="#0B2B5C" stroke-opacity="0.13" stroke-width="48" stroke-linecap="round" />
                    <path class="home-milestone-road-path" d="M80 84C215 35 410 50 470 160C535 278 410 340 300 420C200 495 205 610 330 675C455 740 468 842 375 920C290 990 242 1080 340 1170C412 1235 470 1320 520 1428" stroke="url(#milestone-road-gradient)" stroke-width="32" stroke-linecap="round" />
                    <path class="home-milestone-road-flow" d="M80 84C215 35 410 50 470 160C535 278 410 340 300 420C200 495 205 610 330 675C455 740 468 842 375 920C290 990 242 1080 340 1170C412 1235 470 1320 520 1428" stroke="white" stroke-opacity="0.6" stroke-width="4" stroke-linecap="round" />
                </svg>

                <div class="relative space-y-7 border-l border-titan-navy/10 pl-6 lg:space-y-0 lg:border-l-0 lg:pl-0">
                    @foreach ($milestones as $index => $milestone)
                        @php
                            $color = $roadColors[$index % count($roadColors)];
                            $side = $index % 2 === 0 ? 'lg:col-start-1 lg:mr-10' : 'lg:col-start-3 lg:ml-10';
                            $pinOffset = $roadPinOffsets[$index] ?? 0;
                        @endphp
                        <article x-data="{
                                pinShown: false,
                                cardShown: false,
                                hydrated: false,
                                reducedMotion: false,
                                canTilt: false,
                                init() {
                                    this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                                    this.canTilt = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
                                    this.hydrated = true;
                                    const observer = new IntersectionObserver(([entry]) => {
                                        if (entry.isIntersecting) {
                                            this.pinShown = true;
                                            window.setTimeout(() => this.cardShown = true, this.reducedMotion ? 0 : 180);
                                            observer.disconnect();
                                        }
                                    }, { threshold: 0.3, rootMargin: '0px 0px -10% 0px' });
                                    observer.observe(this.$el);
                                },
                                tilt(event) {
                                    if (this.reducedMotion || !this.canTilt) return;
                                    const bounds = event.currentTarget.getBoundingClientRect();
                                    const rotateY = ((event.clientX - bounds.left) / bounds.width - 0.5) * 8;
                                    const rotateX = ((event.clientY - bounds.top) / bounds.height - 0.5) * -8;
                                    event.currentTarget.style.setProperty('--milestone-rotate-x', `${rotateX}deg`);
                                    event.currentTarget.style.setProperty('--milestone-rotate-y', `${rotateY}deg`);
                                },
                                resetTilt(event) {
                                    event.currentTarget.style.setProperty('--milestone-rotate-x', '0deg');
                                    event.currentTarget.style.setProperty('--milestone-rotate-y', '0deg');
                                }
                            }"
                            x-init="init()"
                            @mousemove="tilt($event)"
                            @mouseleave="resetTilt($event)"
                            :class="[
                                hydrated && !pinShown ? 'milestone-pin-waiting' : pinShown ? 'milestone-pin-visible' : '',
                                hydrated && !cardShown ? 'milestone-card-waiting' : cardShown ? 'milestone-card-visible' : ''
                            ]"
                            class="home-milestone-stop relative lg:grid lg:min-h-[168px] lg:grid-cols-[1fr_180px_1fr] lg:items-center">
                            <span class="home-milestone-mobile-pin absolute -left-[2.05rem] top-5 z-10 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white lg:hidden" style="background-color: {{ $color['hex'] }}">
                                <span class="home-milestone-mobile-pin-ring absolute inset-0 rounded-full border-2" style="border-color: {{ $color['hex'] }}"></span>
                            </span>
                            <span class="home-milestone-pin-wrap absolute left-1/2 top-1/2 z-11 hidden h-16 w-16 lg:block" style="--road-pin-x: {{ $pinOffset }}px">
                                <span class="home-milestone-pin-ring absolute inset-0 rounded-full border-2 border-white/80"></span>
                                <svg viewBox="0 0 48 60" class="home-milestone-pin h-full w-full drop-shadow-[0_8px_8px_rgba(11,43,92,0.25)]" aria-hidden="true">
                                    <path d="M24 2C11.85 2 2 11.85 2 24c0 16.5 18.02 31.96 21.1 34.44a1.45 1.45 0 0 0 1.8 0C27.98 55.96 46 40.5 46 24 46 11.85 36.15 2 24 2Z" fill="{{ $color['hex'] }}" />
                                    <circle cx="24" cy="24" r="8" fill="white" />
                                </svg>
                            </span>
                            <div class="home-milestone-card relative z-1 overflow-hidden rounded-2xl border bg-white p-4 pl-5 shadow-[0_14px_30px_-25px_rgba(11,43,92,0.45)] {{ $color['border'] }} lg:min-h-[112px] lg:flex lg:items-center lg:gap-4 lg:bg-white/95 {{ $side }}">
                                <span class="absolute inset-y-0 left-0 w-1" style="background-color: {{ $color['hex'] }}"></span>
                                @if ($milestone['image'])
                                    <div class="mb-4 aspect-[16/7] overflow-hidden rounded-xl bg-titan-navy/5 lg:mb-0 lg:h-20 lg:w-28 lg:shrink-0 lg:aspect-auto">
                                        <img src="{{ $milestone['image'] }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                                    </div>
                                @endif
                                <div>
                                    <p class="font-heading text-xl font-black tracking-tight {{ $color['text'] }}">{{ $milestone['year'] }}</p>
                                    <h3 class="mt-1 font-heading text-lg font-black tracking-tight text-titan-navy {{ app()->getLocale() === 'km' ? 'font-khmer text-xl' : '' }}">{{ $milestone['title'] }}</h3>
                                    <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-titan-navy/60">{{ $milestone['description'] }}</p>
                                </div>
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
