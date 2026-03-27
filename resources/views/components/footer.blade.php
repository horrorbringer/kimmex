<footer class="bg-titan-navy text-white pt-24 pb-12 relative overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16 lg:gap-8 mb-16">
            <!-- Column 1: Brand -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <img src="/logo.png" alt="Kimmex Logo" class="h-10 w-auto" />
                    <div class="flex flex-col flex-1">
                        <span class="font-bold text-xl leading-none tracking-tight text-white">KIMMEX</span>
                        <span
                            class="text-[10px] uppercase tracking-[0.2em] text-accent-orange">{{ __('Construction') }}</span>
                    </div>
                </div>
                <p class="text-white/50 text-sm leading-relaxed max-w-xs">
                    {{ __('Over 25 years of excellence in building the future of Cambodia.') }}
                </p>
                <div class="flex gap-3 pt-2">
                    <a href="#" target="_blank" rel="noopener noreferrer"
                        class="w-9 h-9 rounded bg-white/10 flex items-center justify-center hover:bg-accent-orange transition-all text-white">
                        <x-lucide-facebook class="w-4 h-4" />
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer"
                        class="w-9 h-9 rounded bg-white/10 flex items-center justify-center hover:bg-accent-orange transition-all text-white">
                        <x-lucide-linkedin class="w-4 h-4" />
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer"
                        class="w-9 h-9 rounded bg-white/10 flex items-center justify-center hover:bg-accent-orange transition-all text-white">
                        <x-lucide-youtube class="w-4 h-4" />
                    </a>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h4 class="font-bold text-sm uppercase tracking-widest mb-8 text-accent-orange flex items-center gap-2">
                    <x-lucide-hard-hat class="w-3.5 h-3.5" />
                    {{ __('Explore') }}
                </h4>
                <ul class="space-y-4 text-sm text-white/50">
                    <li><a href="/projects"
                            class="hover:text-accent-orange hover:pl-2 transition-all flex items-center gap-2">{{ __('Projects') }}</a>
                    </li>
                    <li><a href="/services"
                            class="hover:text-accent-orange hover:pl-2 transition-all flex items-center gap-2">{{ __('Services') }}</a>
                    </li>
                    <li><a href="/about"
                            class="hover:text-accent-orange hover:pl-2 transition-all flex items-center gap-2">{{ __('About Us') }}</a>
                    </li>
                    <li><a href="/careers"
                            class="hover:text-accent-orange hover:pl-2 transition-all flex items-center gap-2">{{ __('Careers') }}</a>
                    </li>
                    <li><a href="/news"
                            class="hover:text-accent-orange hover:pl-2 transition-all flex items-center gap-2">{{ __('News & Insights') }}</a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Services -->
            <div>
                <h4 class="font-bold text-sm uppercase tracking-widest mb-8 text-accent-orange flex items-center gap-2">
                    <x-lucide-hard-hat class="w-3.5 h-3.5" />
                    {{ __('Services') }}
                </h4>
                <ul class="space-y-4 text-sm text-white/50">
                    <li><a href="/services/design-and-build"
                            class="flex items-center gap-2 hover:text-accent-orange transition-all group"><span
                                class="w-1.5 h-1.5 bg-accent-orange rounded-full group-hover:scale-125 transition-transform"></span>{{ __('Design & Build') }}</a>
                    </li>
                    <li><a href="/services/construction"
                            class="flex items-center gap-2 hover:text-accent-orange transition-all group"><span
                                class="w-1.5 h-1.5 bg-accent-orange rounded-full group-hover:scale-125 transition-transform"></span>{{ __('Construction') }}</a>
                    </li>
                    <li><a href="/services/systems"
                            class="flex items-center gap-2 hover:text-accent-orange transition-all group"><span
                                class="w-1.5 h-1.5 bg-accent-orange rounded-full group-hover:scale-125 transition-transform"></span>{{ __('MEP Systems') }}</a>
                    </li>
                </ul>
            </div>

            <!-- Column 4: Contact -->
            <div>
                <h4 class="font-bold text-sm uppercase tracking-widest mb-8 text-accent-orange flex items-center gap-2">
                    <x-lucide-hard-hat class="w-3.5 h-3.5" />
                    {{ __('Contact') }}
                </h4>
                <ul class="space-y-6 text-sm text-white/50">
                    <li class="flex gap-4">
                        <x-lucide-map-pin class="text-accent-orange shrink-0 w-5 h-5" />
                        <a href="#" target="_blank" rel="noopener noreferrer"
                            class="hover:text-accent-orange transition-colors">
                            {{ __('Phnom Penh, Cambodia') }}
                        </a>
                    </li>
                    <li class="flex gap-4 items-center">
                        <x-lucide-phone class="text-accent-orange shrink-0 w-5 h-5" />
                        <a href="tel:+85523999888" class="hover:text-accent-orange transition-colors">
                            +855 23 999 888
                        </a>
                    </li>
                    <li class="flex gap-4 items-center">
                        <x-lucide-mail class="text-accent-orange shrink-0 w-5 h-5" />
                        <a href="mailto:info@kimmex.com" class="hover:text-accent-orange transition-colors">
                            info@kimmex.com
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div
            class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-white/40">
            <p>&copy; 2026 Kimmex Construction & Investment Co., Ltd. {{ __('All rights reserved') }}.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-accent-orange transition-colors">{{ __('Privacy Policy') }}</a>
                <a href="#" class="hover:text-accent-orange transition-colors">{{ __('Terms of Service') }}</a>
            </div>
        </div>
    </div>
</footer>