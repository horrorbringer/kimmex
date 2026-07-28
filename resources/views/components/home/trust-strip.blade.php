<section class="relative z-30 border-y border-gray-100 bg-white" aria-label="{{ __('Company highlights') }}">
    <div class="max-w-[1280px] mx-auto px-5 sm:px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['value' => 25, 'suffix' => '+', 'label' => __('Years of Experience')],
                ['value' => 150, 'suffix' => '+', 'label' => __('Projects Delivered')],
                ['value' => 'QA/QC', 'suffix' => '', 'label' => __('Quality Assurance')],
                ['value' => 500, 'suffix' => '+', 'label' => __('Technical Workforce')],
            ] as $highlight)
                <div
                    @if(is_int($highlight['value']))
                        x-data="{
                            value: 0,
                            target: {{ $highlight['value'] }},
                            started: false,
                            count() {
                                if (this.started) return;
                                this.started = true;

                                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                                    this.value = this.target;
                                    return;
                                }

                                const startedAt = performance.now();
                                const duration = 900;
                                const tick = (now) => {
                                    const progress = Math.min((now - startedAt) / duration, 1);
                                    this.value = Math.round(this.target * (1 - Math.pow(1 - progress, 3)));

                                    if (progress < 1) requestAnimationFrame(tick);
                                };

                                requestAnimationFrame(tick);
                            },
                        }"
                        x-intersect.once="count()"
                    @endif
                    class="group flex min-h-24 sm:min-h-28 flex-col justify-center border-l border-gray-100 px-4 odd:border-l-0 sm:px-6 lg:px-8 lg:odd:border-l lg:first:border-l-0">
                    <span class="font-heading text-2xl sm:text-3xl font-black tracking-tight text-titan-navy transition-colors duration-300 ease-out group-hover:text-titan-red">
                        @if(is_int($highlight['value']))
                            <span x-text="value + '{{ $highlight['suffix'] }}'">{{ $highlight['value'] }}{{ $highlight['suffix'] }}</span>
                        @else
                            {{ $highlight['value'] }}
                        @endif
                    </span>
                    <span class="mt-1 text-[10px] sm:text-xs font-bold uppercase tracking-[0.12em] text-titan-navy/45">
                        {{ $highlight['label'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>
