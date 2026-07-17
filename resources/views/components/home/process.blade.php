@php
    $processes = \Illuminate\Support\Facades\Cache::remember('process_index_array_'.app()->getLocale(), now()->addHours(12), function() {
        $processDb = \App\Models\MethodologyStep::where('isActive', true)->orderBy('orderIndex')->get();
        return $processDb->map(function($step, $index) {
            $description = $step->getTranslation('description', app()->getLocale()) ?: $step->getTranslation('description', 'en');
            return [
                "step" => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                "icon" => $step->icon ?: 'lucide-check-circle',
                "title" => $step->getTranslation('title', app()->getLocale()) ?: $step->getTranslation('title', 'en'),
                "desc" => trim(strip_tags($description))
            ];
        })->toArray();
    });

    if (empty($processes)) {
        $processes = [
            ['icon' => 'lucide-clipboard-check', 'step' => '01', 'title' => __('Initial Consultation'), 'desc' => __('We meet to understand your goals, timeline, and budget requirements.')],
            ['icon' => 'lucide-ruler', 'step' => '02', 'title' => __('Design & Planning'), 'desc' => __('Our architects and engineers draft blueprints and 3D models.')],
            ['icon' => 'lucide-hammer', 'step' => '03', 'title' => __('Execution'), 'desc' => __('Ground breaks and our professional workforce builds the vision.')],
            ['icon' => 'lucide-check-circle-2', 'step' => '04', 'title' => __('Final Handover'), 'desc' => __('Quality reviews are conducted before we hand over keys.')],
        ];
    }
@endphp

<section class="py-12 md:py-16 bg-white overflow-hidden">
    <div class="max-w-[1280px] mx-auto px-6">

        {{-- Header --}}
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="text-center max-w-2xl mx-auto mb-14 md:mb-20 transition-all duration-1000">
            <div class="flex items-center justify-center gap-3 mb-5">
                <div class="w-8 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Our Process') }}</span>
                <div class="w-8 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
            </div>
            <h2 class="text-3xl md:text-4xl font-heading font-black text-gray-900 tracking-tight mb-4">{{ __('How We Deliver') }}</h2>
            <p class="text-gray-500 text-base md:text-lg">{{ __('A proven methodology that ensures quality, safety, and on-time delivery.') }}</p>
        </div>

        {{-- Process Steps --}}
        <div class="relative">
            {{-- Connecting line (desktop) --}}
            <div class="hidden lg:block absolute top-[52px] left-[calc(12.5%+24px)] right-[calc(12.5%+24px)] h-px bg-gray-200"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6">
                @foreach($processes as $index => $s)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        style="transition-delay: {{ $index * 120 }}ms"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                        class="transition-all duration-700 relative text-center group">

                        {{-- Step circle --}}
                        <div class="relative z-10 mx-auto mb-7">
                            <div class="w-[104px] h-[104px] mx-auto rounded-full border-2 border-gray-100 bg-white flex flex-col items-center justify-center transition-all duration-500 group-hover:border-transparent group-hover:shadow-[0_12px_40px_-8px_rgba(227,30,36,0.2)]"
                                 style="--hover-border: var(--primary-color, #E31E24);">
                                <x-dynamic-component :component="$s['icon']" stroke-width="1.5"
                                    class="w-8 h-8 mb-1 transition-all duration-300 text-gray-400 group-hover:scale-110"
                                    style="color: var(--primary-color, #E31E24);" />
                                <span class="text-[10px] font-bold tracking-[0.2em] text-gray-300 group-hover:text-gray-500 transition-colors">{{ $s['step'] }}</span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <h3 class="text-base font-bold text-gray-900 mb-2 transition-colors group-hover:text-titan-red {{ app()->getLocale() === 'km' ? 'font-khmer text-lg' : 'tracking-tight' }}">
                            {{ $s['title'] }}
                        </h3>
                        <p class="text-sm text-gray-400 leading-relaxed max-w-[200px] mx-auto">
                            {{ $s['desc'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
