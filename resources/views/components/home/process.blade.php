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

<section class="bg-white py-16 md:py-24">
    <div class="max-w-[1280px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="grid gap-6 border-b border-titan-navy/10 pb-10 transition-all duration-700 ease-out motion-reduce:transition-none lg:grid-cols-[1.15fr_0.85fr] lg:items-end lg:gap-16">
            <div>
                <div class="mb-5 flex items-center gap-3">
                    <span class="h-px w-10 bg-titan-red"></span>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-titan-red">{{ __('Our Process') }}</span>
                </div>
                <h2 class="font-heading text-3xl font-black tracking-tight text-titan-navy md:text-5xl">{{ __('How We Deliver') }}</h2>
            </div>
            <p class="max-w-lg text-base leading-relaxed text-titan-navy/60 lg:pb-1 md:text-lg">{{ __('A proven methodology that ensures quality, safety, and on-time delivery.') }}</p>
        </div>

        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:mt-14 lg:grid-cols-4">
            @foreach($processes as $index => $s)
                <article x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                    class="group relative border-t border-titan-navy/10 py-8 transition-[opacity,transform] duration-700 ease-out motion-reduce:transition-none sm:px-7 sm:odd:border-l sm:odd:border-t-0 lg:border-l lg:border-t-0 lg:first:border-l-0 lg:py-2">
                    <span class="absolute -top-2 left-0 h-1 w-10 bg-titan-red sm:odd:left-7 lg:left-7 lg:first:left-0"></span>

                    <div class="mb-8 flex items-center justify-between">
                        <span class="font-heading text-5xl font-black leading-none text-titan-navy/10">{{ $s['step'] }}</span>
                        <div class="flex h-11 w-11 items-center justify-center rounded-full border border-titan-navy/10 text-titan-red transition-[background-color,color,border-color] duration-300 ease-out group-hover:border-titan-red group-hover:bg-titan-red group-hover:text-white">
                            <x-dynamic-component :component="$s['icon']" class="h-5 w-5" stroke-width="1.8" />
                        </div>
                    </div>

                    <h3 class="mb-3 font-heading text-xl font-black tracking-tight text-titan-navy transition-colors duration-300 group-hover:text-titan-red {{ app()->getLocale() === 'km' ? 'font-khmer text-2xl' : '' }}">
                        {{ $s['title'] }}
                    </h3>
                    <p class="max-w-[230px] text-sm leading-relaxed text-titan-navy/55">
                        {{ $s['desc'] }}
                    </p>
                </article>
            @endforeach
        </div>
    </div>
</section>
