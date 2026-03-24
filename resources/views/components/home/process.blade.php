@php
$processes = [
    ['icon' => 'lucide-clipboard-check', 'step' => '01', 'title' => __('Initial Consultation'), 'desc' => __('We meet to understand your goals, timeline, and conceptual limits.')],
    ['icon' => 'lucide-ruler', 'step' => '02', 'title' => __('Design & Planning'), 'desc' => __('Our architects and engineers draft out the blueprints and 3D models.')],
    ['icon' => 'lucide-hammer', 'step' => '03', 'title' => __('Execution phase'), 'desc' => __('Ground breaks and our professional workforce constructs the vision.')],
    ['icon' => 'lucide-check-circle-2', 'step' => '04', 'title' => __('Final Handover'), 'desc' => __('Quality reviews are conducted before we proudly hand over keys.')],
];
@endphp

<section class="py-24 bg-titan-navy">
    <div class="max-w-[1400px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="text-center max-w-2xl mx-auto mb-16 transition-all duration-1000">
            <span class="text-accent-orange font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('Our Process') }}</span>
            <h2 class="text-4xl font-black text-white mb-4">{{ __('How We Work') }}</h2>
            <p class="text-white/50 text-lg">{{ __('A streamlined approach') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            <div class="hidden md:block absolute top-[4.5rem] left-[10%] right-[10%] h-px bg-gradient-to-r from-transparent via-white/20 to-transparent z-0"></div>

            @foreach($processes as $index => $s)
                <div x-data="{ shown: false }" x-intersect.once="shown = true" style="transition-delay: {{ $index * 100 }}ms" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" 
                     class="relative z-10 flex flex-col items-center text-center group transition-all duration-1000">
                    <div class="w-36 h-36 bg-white/5 backdrop-blur-sm rounded-full border border-white/10 flex flex-col items-center justify-center mb-8 group-hover:bg-accent-orange group-hover:border-accent-orange group-hover:shadow-[0_0_40px_rgba(255,107,0,0.5)] group-hover:-translate-y-2 transition-all duration-500 relative">
                        <x-dynamic-component :component="$s['icon']" class="text-accent-orange mb-2 group-hover:text-white group-hover:scale-110 transition-all duration-300 w-8 h-8" />
                        <span class="text-xl font-black text-white/40 group-hover:text-white transition-colors">{{ $s['step'] }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 group-hover:text-accent-orange transition-colors">{{ $s['title'] }}</h3>
                    <p class="text-sm text-white/50 max-w-[200px] leading-relaxed group-hover:text-white/80 transition-colors">{{ $s['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
