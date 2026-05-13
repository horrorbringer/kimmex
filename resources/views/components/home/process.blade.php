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
            ['icon' => 'lucide-clipboard-check', 'step' => '01', 'title' => __('Initial Consultation'), 'desc' => __('We meet to understand your goals, timeline, and conceptual limits.')],
            ['icon' => 'lucide-ruler', 'step' => '02', 'title' => __('Design & Planning'), 'desc' => __('Our architects and engineers draft out the blueprints and 3D models.')],
            ['icon' => 'lucide-hammer', 'step' => '03', 'title' => __('Execution phase'), 'desc' => __('Ground breaks and our professional workforce constructs the vision.')],
            ['icon' => 'lucide-check-circle-2', 'step' => '04', 'title' => __('Final Handover'), 'desc' => __('Quality reviews are conducted before we proudly hand over keys.')],
        ];
    }
@endphp

<section class="py-24 bg-gray-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="text-center max-w-2xl mx-auto mb-16 transition-all duration-1000">
            <span
                class="text-titan-red font-bold uppercase tracking-widest text-xs mb-4 block">{{ __('Our Process') }}</span>
            <h2 class="text-4xl font-black text-titan-navy mb-4 tracking-tighter uppercase">{{ __('How We Work') }}</h2>
            <p class="text-titan-navy/60 text-lg">{{ __('A streamlined approach') }}</p>
        </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <!-- Static Connecting Line -->
                <div class="hidden md:block absolute top-[4rem] left-[10%] right-[10%] h-px bg-gray-200 z-0"></div>

                @foreach($processes as $index => $s)
                    <div x-data="{ shown: false, hover: false }" x-intersect.once="shown = true"
                        @mouseenter="hover = true" @mouseleave="hover = false"
                        style="transition-delay: {{ $index * 150 }}ms"
                        x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                        class="relative z-10 flex flex-col items-center text-center cursor-pointer transition-all duration-700 group">
                        
                        <div class="relative mb-10 transition-all duration-500 ease-in-out"
                            x-bind:class="hover ? '-translate-y-2' : ''">
                            
                            <div x-bind:class="hover ? 'border-titan-red shadow-[0_20px_40px_-10px_rgba(227,30,36,0.2)]' : 'border-gray-100'"
                                class="w-32 h-32 rounded-full border-2 flex flex-col items-center justify-center transition-all duration-500 relative bg-white overflow-hidden z-10">
                                
                                <x-dynamic-component :component="$s['icon']" stroke-width="1.5"
                                    class="mb-2 transition-all duration-500 w-10 h-10 relative z-10 text-titan-red"
                                    x-bind:class="hover ? 'scale-110' : ''" />
                                
                                <span class="text-[10px] font-black transition-all duration-500 relative z-10 tracking-[0.2em] uppercase text-titan-navy/20"
                                    x-bind:class="hover ? '!text-titan-red' : ''">{{ $s['step'] }}</span>
                            </div>
                        </div>

                        <div class="transition-all duration-500">
                            <h3 x-bind:class="hover ? 'text-titan-red' : 'text-titan-navy'"
                                class="text-lg font-black mb-3 transition-all duration-500 tracking-tight uppercase">
                                {{ $s['title'] }}</h3>
                            <p class="text-xs max-w-[180px] mx-auto leading-relaxed text-titan-navy/50 transition-all duration-500 font-medium">
                                {{ $s['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
    </div>
</section>
