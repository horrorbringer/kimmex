@php
$stats = [
    ['icon' => 'lucide-users', 'value' => '100', 'label' => 'Team Members', 'bg' => 'bg-orange-50', 'color' => 'text-accent-orange'],
    ['icon' => 'lucide-award', 'value' => '50', 'label' => 'Awards Won', 'bg' => 'bg-red-50', 'color' => 'text-titan-red'],
    ['icon' => 'lucide-hard-hat', 'value' => '25', 'label' => 'Years Experience', 'bg' => 'bg-gray-50', 'color' => 'text-titan-navy'],
    ['icon' => 'lucide-shield-check', 'value' => '0', 'label' => 'Safety Incidents', 'bg' => 'bg-green-50', 'color' => 'text-green-600'],
];
@endphp

<section class="py-24 bg-gray-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            
            <!-- Context -->
            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000">
                <h2 class="text-accent-orange font-bold uppercase tracking-[0.3em] text-sm mb-4 block">
                    {{ __('About Kimmex') }}
                </h2>
                <h1 class="text-4xl md:text-6xl font-heading font-black text-titan-navy mb-8 leading-tight">
                    {{ __('Experience & Excellence') }}
                </h1>
                <p class="text-titan-navy/60 text-lg mb-8">
                    {{ __('Over 25 years of excellence in building the future of Cambodia. We specialize in mega-projects and infrastructure.') }}
                </p>

                <div class="grid grid-cols-2 gap-4">
                    @foreach($stats as $item)
                        <div class="group bg-white p-6 rounded-2xl border border-gray-100 relative overflow-hidden transition-all duration-500 hover:shadow-[0_20px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1">
                            <div class="absolute top-0 right-0 w-24 h-24 {{ $item['bg'] }} opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-700"></div>
                            <div class="flex flex-col gap-4">
                                <div class="w-12 h-12 {{ $item['bg'] }} {{ $item['color'] }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <x-dynamic-component :component="$item['icon']" class="w-6 h-6" />
                                </div>
                                <div>
                                    <div class="text-3xl font-heading font-black text-titan-navy mb-1 flex items-baseline gap-1">
                                        {{ $item['value'] }}
                                        <span class="text-accent-orange text-sm font-bold">+</span>
                                    </div>
                                    <h3 class="text-xs font-bold text-titan-navy/40 uppercase tracking-widest group-hover:text-titan-navy/60 transition-colors">
                                        {{ $item['label'] }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <a href="/about" class="inline-flex items-center gap-3 mt-10 text-titan-red font-heading font-black uppercase tracking-[0.3em] text-xs hover:gap-6 transition-all">
                    {{ __('Learn More About Us') }} <x-lucide-arrow-right class="w-4 h-4" />
                </a>
            </div>

            <!-- Imagery Grid -->
            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000 delay-200 relative">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative h-64 w-full rounded-2xl overflow-hidden shadow-lg">
                        <img src="/images/projects/Thumbnail-4.jpg" alt="Construction Site" class="object-cover w-full h-full" />
                    </div>
                    <div class="relative h-64 w-full rounded-2xl overflow-hidden shadow-lg mt-8 md:mt-0">
                        <img src="/images/projects/Thumbnail-5.jpg" alt="Team Meeting" class="object-cover w-full h-full" />
                    </div>
                    <div class="relative h-64 w-full rounded-2xl overflow-hidden shadow-lg -mt-8 md:mt-0">
                        <img src="/images/projects/Thumbnail-6.jpg" alt="Architecture" class="object-cover w-full h-full" />
                    </div>
                    <div class="relative h-64 w-full rounded-2xl overflow-hidden shadow-lg">
                        <img src="/images/projects/Thumbnail-7.jpg" alt="Building" class="object-cover w-full h-full" />
                    </div>
                </div>
                <!-- Center Badge -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-accent-orange text-white p-6 rounded-2xl shadow-xl text-center z-10 w-32 h-32 flex flex-col items-center justify-center">
                    <div class="text-4xl font-black">25+</div>
                    <div class="text-xs uppercase tracking-widest mt-1">{{ __('Years of Excellence') }}</div>
                </div>
            </div>

        </div>
    </div>
</section>
