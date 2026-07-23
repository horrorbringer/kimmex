<section class="relative py-14 md:py-20 overflow-hidden" style="background: linear-gradient(135deg, #071A33 0%, #0B2B5C 100%);">
    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>

    <div class="relative z-10 max-w-[1280px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-col lg:flex-row items-center justify-between gap-10 transition-all duration-1000">

            {{-- Left: Text --}}
            <div class="text-center lg:text-left max-w-xl">
                <span class="mb-3 block text-[10px] font-bold uppercase tracking-[0.2em] text-white/50">{{ __('Let’s build with confidence') }}</span>
                <h2 class="text-3xl md:text-4xl font-heading font-black leading-tight tracking-tight mb-4" style="color: #FFFFFF;">
                    {{ __('Ready to Start Your Project?') }}
                </h2>
                <p class="text-base md:text-lg leading-relaxed" style="color: rgba(255,255,255,0.6);">
                    {{ __('Contact us today for a free consultation and quote on your next construction project.') }}
                </p>
            </div>

            {{-- Right: Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 shrink-0">
                <a href="/contact"
                    class="group flex items-center justify-center gap-2.5 px-8 py-4 rounded-xl font-bold uppercase tracking-wider text-sm transition-[box-shadow,filter] duration-300 ease-out shadow-lg hover:shadow-xl hover:brightness-110"
                    style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                    {{ __('Get Free Quote') }}
                    <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </a>
                <a href="tel:+85523999888"
                    class="group flex items-center justify-center gap-2.5 px-8 py-4 rounded-xl font-bold uppercase tracking-wider text-sm transition-[background-color,border-color] duration-300 ease-out hover:bg-white/10"
                    style="border: 2px solid rgba(255,255,255,0.2); color: #FFFFFF;">
                    <x-lucide-phone class="w-4 h-4" />
                    {{ __('Call Now') }}
                </a>
            </div>
        </div>
    </div>
</section>
