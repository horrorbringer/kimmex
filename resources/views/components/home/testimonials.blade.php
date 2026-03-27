@php
    $testimonialsDb = \App\Models\Testimonial::where('isFeatured', true)->orderBy('orderIndex')->take(3)->get();
    if ($testimonialsDb->count() > 0) {
        $testimonials = $testimonialsDb->map(function (\App\Models\Testimonial $t) {
            return [
                'quote' => strip_tags($t->getTranslation('content', app()->getLocale() === 'km' ? 'kh' : app()->getLocale()) ?: $t->getTranslation('content', 'en')),
                'rating' => $t->rating ?? 5,
                'author' => $t->getTranslation('clientName', app()->getLocale() === 'km' ? 'kh' : app()->getLocale()) ?: $t->getTranslation('clientName', 'en'),
                'role' => $t->getTranslation('clientRole', app()->getLocale() === 'km' ? 'kh' : app()->getLocale()) ?: $t->getTranslation('clientRole', 'en')
            ];
        })->toArray();
    } else {
        $testimonials = [
            ['quote' => __('They delivered our commercial building ahead of schedule and with impeccable quality.'), 'rating' => 5, 'author' => __('Sok V.'), 'role' => __('CEO, Alpha Corp')],
            ['quote' => __('The attention to detail and safety standards were outstanding during the water plant project.'), 'rating' => 5, 'author' => __('Dr. Cham'), 'role' => __('Director of Infrastructure')],
            ['quote' => __('Highly professional team. They handled all the MEP complexities without a single delay.'), 'rating' => 5, 'author' => __('Mr. Rithy'), 'role' => __('Property Developer')],
        ];
    }
@endphp

<section class="py-24 bg-white">
    <div class="max-w-[1400px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="text-center max-w-2xl mx-auto mb-16 transition-all duration-1000">
            <span
                class="text-accent-orange font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('Testimonials') }}</span>
            <h2 class="text-4xl font-heading font-black text-titan-navy mb-4">{{ __('What Our Clients Say') }}</h2>
            <p class="text-titan-navy/50 text-lg">{{ __('We build relationships, not just structures.') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($testimonials as $index => $t)
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000">
                    <div
                        class="bg-gray-50 p-8 rounded-2xl relative h-full flex flex-col group hover:-translate-y-2 hover:shadow-xl transition-all duration-500">
                        <x-lucide-quote
                            class="text-accent-orange/20 absolute top-6 right-6 w-12 h-12 group-hover:text-accent-orange/40 transition-colors" />
                        <div class="flex gap-1 mb-4">
                            @for($i = 0; $i < $t['rating']; $i++)
                                <x-lucide-star class="text-accent-orange fill-accent-orange w-4 h-4" />
                            @endfor
                        </div>
                        <p class="text-titan-navy/70 mb-6 relative z-10 leading-relaxed flex-grow">
                            &ldquo;{{ $t['quote'] }}&rdquo;</p>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-titan-navy rounded-full flex items-center justify-center text-white font-bold shrink-0">
                                {{ substr($t['author'], 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-titan-navy">{{ $t['author'] }}</div>
                                <div class="text-sm text-titan-navy/50">{{ $t['role'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>