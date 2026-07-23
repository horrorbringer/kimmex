<section class="relative z-30 border-y border-gray-100 bg-white" aria-label="{{ __('Company highlights') }}">
    <div class="max-w-[1280px] mx-auto px-5 sm:px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['value' => '25+', 'label' => __('Years of Experience')],
                ['value' => '150+', 'label' => __('Projects Delivered')],
                ['value' => 'ISO 9001', 'label' => __('Quality Standard')],
                ['value' => '500+', 'label' => __('Technical Workforce')],
            ] as $highlight)
                <div class="group flex min-h-24 sm:min-h-28 flex-col justify-center border-l border-gray-100 px-4 odd:border-l-0 sm:px-6 lg:px-8 lg:odd:border-l lg:first:border-l-0">
                    <span class="font-heading text-2xl sm:text-3xl font-black tracking-tight text-titan-navy transition-colors duration-300 ease-out group-hover:text-titan-red">
                        {{ $highlight['value'] }}
                    </span>
                    <span class="mt-1 text-[10px] sm:text-xs font-bold uppercase tracking-[0.12em] text-titan-navy/45">
                        {{ $highlight['label'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>
