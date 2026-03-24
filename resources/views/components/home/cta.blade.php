<section class="py-24 bg-accent-orange">
    <div class="max-w-[1400px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="flex flex-col lg:flex-row items-center justify-between gap-8 transition-all duration-1000">
            <div class="text-center lg:text-left">
                <h2 class="text-4xl md:text-5xl font-heading font-black text-white mb-4">{{ __('Ready to Start Your Project?') }}</h2>
                <p class="text-white/80 text-lg max-w-xl">{{ __('Contact us today for a free consultation and quote on your next big project.') }}</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="/contact" class="bg-white text-titan-navy px-8 py-4 font-bold uppercase tracking-widest text-sm hover:bg-titan-navy hover:text-white transition-all rounded-lg flex items-center gap-2">
                    {{ __('Get Free Quote') }} <x-lucide-arrow-right class="w-4 h-4" />
                </a>
                <a href="tel:+85523999888" class="border-2 border-white text-white px-8 py-4 font-bold uppercase tracking-widest text-sm hover:bg-white hover:text-titan-navy transition-all rounded-lg flex items-center gap-2">
                    <x-lucide-phone class="w-4 h-4" /> {{ __('Call Now') }}
                </a>
            </div>
        </div>
    </div>
</section>
