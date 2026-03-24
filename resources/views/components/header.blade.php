@php $isHeroPage = request()->routeIs(['home', 'about', 'services.index', 'services.show', 'projects.index', 'news.index', 'contact', 'careers']); @endphp
<header x-data="{
    isHeroPage: {{ $isHeroPage ? 'true' : 'false' }},
    isScrolled: false,
    get navDark() { return this.isHeroPage ? this.isScrolled : true; },
    hoveredNav: null,
    isSearchOpen: false,
    isMobileMenuOpen: false,
    expandedMobileItem: null,
    searchQuery: '',
    init() {
        const self = this;
        window.addEventListener('scroll', () => {
            self.isScrolled = window.scrollY > 50;
        });
        window.addEventListener('resize', () => { if (window.innerWidth >= 1024) self.isMobileMenuOpen = false; });
    }
}" class="fixed top-0 left-0 w-full z-[100]">

    <!-- TOP BAR -->
    <div :class="isScrolled ? 'h-0 opacity-0' : 'h-10 opacity-100'" class="bg-titan-navy text-white text-[11px] tracking-wide font-medium transition-all duration-500 overflow-hidden relative">
        <div class="max-w-[1600px] mx-auto px-2 sm:px-6 h-full flex justify-between items-center">
            <div class="flex gap-2 sm:gap-6 items-center">
                <a href="tel:+85523999888" class="flex items-center gap-1.5 hover:text-accent-orange cursor-pointer transition whitespace-nowrap">
                    <x-lucide-phone class="text-accent-orange shrink-0 w-3 h-3" />
                    <span class="text-[10px] sm:hidden">+855 2...</span>
                    <span class="hidden sm:inline">+855 23 999 888</span>
                </a>
                <a href="mailto:info@kimmex.com" class="hidden md:flex items-center gap-2 hover:text-accent-orange cursor-pointer transition">
                    <x-lucide-mail class="text-accent-orange w-3 h-3" />
                    info@kimmex.com
                </a>
            </div>

            <div class="flex gap-4 items-center">
                <div class="hidden sm:flex items-center gap-2 text-white/60">
                    <x-lucide-map-pin class="w-3 h-3" />
                    <span>{{ __('Phnom Penh, Cambodia') }}</span>
                </div>
                <div class="w-[1px] h-3 bg-white/20 hidden sm:block"></div>
                <div class="hidden sm:flex gap-2">
                    <a href="#" class="w-6 h-6 rounded bg-white/10 flex items-center justify-center hover:bg-accent-orange transition-colors">
                        <x-lucide-facebook class="w-3 h-3" />
                    </a>
                    <a href="#" class="w-6 h-6 rounded bg-white/10 flex items-center justify-center hover:bg-accent-orange transition-colors">
                        <x-lucide-linkedin class="w-3 h-3" />
                    </a>
                    <a href="#" class="w-6 h-6 rounded bg-white/10 flex items-center justify-center hover:bg-accent-orange transition-colors">
                        <x-lucide-youtube class="w-3 h-3" />
                    </a>
                    <a href="#" class="w-6 h-6 rounded bg-white/10 flex items-center justify-center hover:bg-accent-orange transition-colors">
                        <x-lucide-instagram class="w-3 h-3" />
                    </a>
                </div>

                <div class="w-[1px] h-3 bg-white/20 hidden sm:block"></div>
                <a href="/admin" class="hidden sm:flex items-center gap-2 px-2 py-1 bg-white/10 hover:bg-accent-orange rounded transition-colors group">
                    <x-lucide-shield class="text-accent-orange group-hover:text-white w-2.5 h-2.5" />
                    <span class="text-[9px] font-bold">{{ __('ADMIN') }}</span>
                </a>

                <div class="w-[1px] h-3 bg-white/20 hidden sm:block"></div>
                <div class="flex items-center gap-1 bg-white/10 rounded px-1 py-0.5">
                    <a href="{{ route('lang.switch', 'en') }}" class="px-1.5 py-0.5 rounded text-[10px] font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-accent-orange text-white' : 'text-white/60 hover:text-white' }}">EN</a>
                    <a href="{{ route('lang.switch', 'km') }}" class="px-1.5 py-0.5 rounded text-[10px] font-bold transition-all {{ app()->getLocale() === 'km' ? 'bg-accent-orange text-white' : 'text-white/60 hover:text-white' }}">KH</a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <nav :class="navDark ? 'bg-white/95 backdrop-blur-md shadow-xl' : 'bg-transparent border-b border-white/10'" class="w-full transition-all duration-500">
        <div class="max-w-[1600px] mx-auto px-6">
            <div class="flex justify-between items-center h-20">

                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 group cursor-pointer">
                    <div class="relative">
                        <img src="/logo.png" alt="Kimmex Logo" class="h-12 w-auto transition-all duration-300" />
                    </div>
                    <div class="leading-none flex flex-col justify-center">
                        <span :class="navDark ? 'text-titan-navy' : 'text-white'" class="block font-heading font-black text-2xl md:text-3xl tracking-tight transition-all duration-300 leading-none group-hover:text-accent-orange">
                            KIMMEX
                        </span>
                        <span :class="navDark ? 'text-titan-navy/50' : 'text-white/60'" class="block text-[9px] font-bold tracking-[0.15em] uppercase transition-colors duration-300 mt-0.5">
                            {{ __('Construction & Investment') }}
                        </span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center">

                    <!-- About Us -->
                    <div class="relative group/nav">
                        <a href="/about" class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'" class="text-[13px] font-bold uppercase tracking-wide transition-all duration-200 group-hover/nav:text-accent-orange">{{ __('About Us') }}</span>
                            <x-lucide-chevron-down stroke-width="2.5" :class="navDark ? 'text-titan-navy/50' : 'text-white/50'" class="w-3 h-3 transition-transform duration-300 group-hover/nav:-rotate-180 group-hover/nav:text-accent-orange" />
                            <span class="absolute bottom-0 left-5 right-5 h-[3px] bg-accent-orange transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                        <div class="absolute top-full left-0 pt-0 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible transition-all duration-300 transform translate-y-2 group-hover/nav:translate-y-0 z-50">
                            <div class="bg-titan-navy/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.5)] rounded-2xl border border-white/10 min-w-[280px] p-2">
                                <a href="/about#profile" class="flex items-center px-4 py-3.5 rounded-xl hover:bg-white/10 transition-all duration-200 group/item">
                                    <div>
                                        <div class="font-medium text-white/90 group-hover/item:text-accent-orange text-sm transition-colors">{{ __('Company Profile') }}</div>
                                        <div class="text-[10px] text-white/50 mt-0.5 group-hover/item:text-white/70 transition-colors">{{ __('Learn about our history') }}</div>
                                    </div>
                                </a>
                                <a href="/about#leadership" class="flex items-center px-4 py-3.5 rounded-xl hover:bg-white/10 transition-all duration-200 group/item">
                                    <div>
                                        <div class="font-medium text-white/90 group-hover/item:text-accent-orange text-sm transition-colors">{{ __('Leadership') }}</div>
                                        <div class="text-[10px] text-white/50 mt-0.5 group-hover/item:text-white/70 transition-colors">{{ __('Meet our team') }}</div>
                                    </div>
                                </a>
                                <a href="/about#safety" class="flex items-center px-4 py-3.5 rounded-xl hover:bg-white/10 transition-all duration-200 group/item">
                                    <div>
                                        <div class="font-medium text-white/90 group-hover/item:text-accent-orange text-sm transition-colors">{{ __('Quality & Safety') }}</div>
                                        <div class="text-[10px] text-white/50 mt-0.5 group-hover/item:text-white/70 transition-colors">{{ __('Our standards') }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="relative group/nav">
                        <a href="/services" class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'" class="text-[13px] font-bold uppercase tracking-wide transition-all duration-200 group-hover/nav:text-accent-orange">{{ __('Services') }}</span>
                            <x-lucide-chevron-down stroke-width="2.5" :class="navDark ? 'text-titan-navy/50' : 'text-white/50'" class="w-3 h-3 transition-transform duration-300 group-hover/nav:-rotate-180 group-hover/nav:text-accent-orange" />
                            <span class="absolute bottom-0 left-5 right-5 h-[3px] bg-accent-orange transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                        <div class="absolute top-full left-0 pt-0 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible transition-all duration-300 transform translate-y-2 group-hover/nav:translate-y-0 z-50">
                            <div class="bg-titan-navy/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.5)] rounded-2xl border border-white/10 min-w-[280px] p-2">
                                <a href="/services/design-build" class="flex items-center px-4 py-3.5 rounded-xl hover:bg-white/10 transition-all duration-200 group/item">
                                    <div>
                                        <div class="font-medium text-white/90 group-hover/item:text-accent-orange text-sm transition-colors">{{ __('Design & Build') }}</div>
                                        <div class="text-[10px] text-white/50 mt-0.5 group-hover/item:text-white/70 transition-colors">{{ __('Full lifecycle solutions') }}</div>
                                    </div>
                                </a>
                                <a href="/services/construction" class="flex items-center px-4 py-3.5 rounded-xl hover:bg-white/10 transition-all duration-200 group/item">
                                    <div>
                                        <div class="font-medium text-white/90 group-hover/item:text-accent-orange text-sm transition-colors">{{ __('Construction') }}</div>
                                        <div class="text-[10px] text-white/50 mt-0.5 group-hover/item:text-white/70 transition-colors">{{ __('Revitalize existing structures') }}</div>
                                    </div>
                                </a>
                                <a href="/services/project-management" class="flex items-center px-4 py-3.5 rounded-xl hover:bg-white/10 transition-all duration-200 group/item">
                                    <div>
                                        <div class="font-medium text-white/90 group-hover/item:text-accent-orange text-sm transition-colors">{{ __('Project Management') }}</div>
                                        <div class="text-[10px] text-white/50 mt-0.5 group-hover/item:text-white/70 transition-colors">{{ __('Oversight & control') }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Projects -->
                    <div class="relative group/nav">
                        <a href="/projects" class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'" class="text-[13px] font-bold uppercase tracking-wide transition-all duration-200 group-hover/nav:text-accent-orange">{{ __('Projects') }}</span>
                            <x-lucide-chevron-down stroke-width="2.5" :class="navDark ? 'text-titan-navy/50' : 'text-white/50'" class="w-3 h-3 transition-transform duration-300 group-hover/nav:-rotate-180 group-hover/nav:text-accent-orange" />
                            <span class="absolute bottom-0 left-5 right-5 h-[3px] bg-accent-orange transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                        <div class="absolute top-full left-0 pt-0 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible transition-all duration-300 transform translate-y-2 group-hover/nav:translate-y-0 z-50">
                            <div class="bg-titan-navy/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.5)] rounded-2xl border border-white/10 min-w-[280px] p-2">
                                <!-- Completed Projects with 3rd Level Flyout -->
                                <div class="relative group/nested">
                                    <a href="/projects?status=completed" class="flex items-center justify-between px-4 py-3.5 rounded-xl hover:bg-white/10 transition-all duration-200 group/item">
                                        <div>
                                            <div class="font-medium text-white/90 group-hover/item:text-accent-orange text-sm transition-colors">{{ __('Completed Projects') }}</div>
                                            <div class="text-[10px] text-white/50 mt-0.5 group-hover/item:text-white/70 transition-colors">{{ __('View our portfolio') }}</div>
                                        </div>
                                        <x-lucide-arrow-right class="w-3.5 h-3.5 text-white/30 group-hover/item:text-accent-orange transition-colors" />
                                    </a>
                                    <div class="absolute left-full top-0 ml-2 opacity-0 invisible group-hover/nested:opacity-100 group-hover/nested:visible transition-all duration-300 transform translate-x-2 group-hover/nested:translate-x-0 z-[60]">
                                        <div class="bg-titan-navy/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.5)] rounded-2xl border border-white/10 min-w-[240px] p-2">
                                            <a href="/projects/completed?type=Government+Office+Building" class="group/sub flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/70 hover:text-white hover:bg-white/10 transition-all">
                                                <span>{{ __('Government') }}</span>
                                                <x-lucide-arrow-right class="w-3.5 h-3.5 opacity-0 -translate-x-2 group-hover/sub:opacity-100 group-hover/sub:translate-x-0 transition-all text-accent-orange" />
                                            </a>
                                            <a href="/projects/completed?type=Water+Treatment+Plant" class="group/sub flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/70 hover:text-white hover:bg-white/10 transition-all">
                                                <span>{{ __('Water Treatment') }}</span>
                                                <x-lucide-arrow-right class="w-3.5 h-3.5 opacity-0 -translate-x-2 group-hover/sub:opacity-100 group-hover/sub:translate-x-0 transition-all text-accent-orange" />
                                            </a>
                                            <a href="/projects/completed?type=Slope+Construction" class="group/sub flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/70 hover:text-white hover:bg-white/10 transition-all">
                                                <span>{{ __('Slope') }}</span>
                                                <x-lucide-arrow-right class="w-3.5 h-3.5 opacity-0 -translate-x-2 group-hover/sub:opacity-100 group-hover/sub:translate-x-0 transition-all text-accent-orange" />
                                            </a>
                                            <a href="/projects/completed?type=Systems" class="group/sub flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/70 hover:text-white hover:bg-white/10 transition-all">
                                                <span>{{ __('Systems') }}</span>
                                                <x-lucide-arrow-right class="w-3.5 h-3.5 opacity-0 -translate-x-2 group-hover/sub:opacity-100 group-hover/sub:translate-x-0 transition-all text-accent-orange" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Projects in Progress with 3rd Level Flyout -->
                                <div class="relative group/nested mt-1">
                                    <a href="/projects?status=in-progress" class="flex items-center justify-between px-4 py-3.5 rounded-xl hover:bg-white/10 transition-all duration-200 group/item">
                                        <div>
                                            <div class="font-medium text-white/90 group-hover/item:text-accent-orange text-sm transition-colors">{{ __('Project in Progress') }}</div>
                                            <div class="text-[10px] text-white/50 mt-0.5 group-hover/item:text-white/70 transition-colors">{{ __('Current developments') }}</div>
                                        </div>
                                        <x-lucide-arrow-right class="w-3.5 h-3.5 text-white/30 group-hover/item:text-accent-orange transition-colors" />
                                    </a>
                                    <div class="absolute left-full top-0 ml-2 opacity-0 invisible group-hover/nested:opacity-100 group-hover/nested:visible transition-all duration-300 transform translate-x-2 group-hover/nested:translate-x-0 z-[60]">
                                        <div class="bg-titan-navy/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.5)] rounded-2xl border border-white/10 min-w-[240px] p-2">
                                            <a href="/projects/implementation?type=Water+Treatment+Plant" class="group/sub flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/70 hover:text-white hover:bg-white/10 transition-all">
                                                <span>{{ __('Water Treatment') }}</span>
                                                <x-lucide-arrow-right class="w-3.5 h-3.5 opacity-0 -translate-x-2 group-hover/sub:opacity-100 group-hover/sub:translate-x-0 transition-all text-accent-orange" />
                                            </a>
                                            <a href="/projects/implementation?type=Systems" class="group/sub flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/70 hover:text-white hover:bg-white/10 transition-all">
                                                <span>{{ __('Systems') }}</span>
                                                <x-lucide-arrow-right class="w-3.5 h-3.5 opacity-0 -translate-x-2 group-hover/sub:opacity-100 group-hover/sub:translate-x-0 transition-all text-accent-orange" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- News -->
                    <div class="relative group/nav">
                        <a href="/news" class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'" class="text-[13px] font-bold uppercase tracking-wide transition-all duration-200 group-hover/nav:text-accent-orange">{{ __('News') }}</span>
                            <x-lucide-chevron-down stroke-width="2.5" :class="navDark ? 'text-titan-navy/50' : 'text-white/50'" class="w-3 h-3 transition-transform duration-300 group-hover/nav:-rotate-180 group-hover/nav:text-accent-orange" />
                            <span class="absolute bottom-0 left-5 right-5 h-[3px] bg-accent-orange transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                        <div class="absolute top-full left-0 pt-0 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible transition-all duration-300 transform translate-y-2 group-hover/nav:translate-y-0 z-50">
                            <div class="bg-titan-navy/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.5)] rounded-2xl border border-white/10 min-w-[280px] p-2">
                                <a href="/news" class="flex items-center px-4 py-3.5 rounded-xl hover:bg-white/10 transition-all duration-200 group/item">
                                    <div>
                                        <div class="font-medium text-white/90 group-hover/item:text-accent-orange text-sm transition-colors">{{ __('News & Updates') }}</div>
                                        <div class="text-[10px] text-white/50 mt-0.5 group-hover/item:text-white/70 transition-colors">{{ __('Latest announcements') }}</div>
                                    </div>
                                </a>
                                <a href="/documents" class="flex items-center px-4 py-3.5 rounded-xl hover:bg-white/10 transition-all duration-200 group/item">
                                    <div>
                                        <div class="font-medium text-white/90 group-hover/item:text-accent-orange text-sm transition-colors">{{ __('Doc Collection') }}</div>
                                        <div class="text-[10px] text-white/50 mt-0.5 group-hover/item:text-white/70 transition-colors">{{ __('Resources & documents') }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Careers (no dropdown) -->
                    <div class="relative group/nav">
                        <a href="/careers" class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'" class="text-[13px] font-bold uppercase tracking-wide transition-all duration-200 group-hover/nav:text-accent-orange">{{ __('Careers') }}</span>
                            <span class="absolute bottom-0 left-5 right-5 h-[3px] bg-accent-orange transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                    </div>

                    <!-- Contact (no dropdown) -->
                    <div class="relative group/nav">
                        <a href="/contact" class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'" class="text-[13px] font-bold uppercase tracking-wide transition-all duration-200 group-hover/nav:text-accent-orange">{{ __('Contact') }}</span>
                            <span class="absolute bottom-0 left-5 right-5 h-[3px] bg-accent-orange transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                    </div>

                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    <!-- Search Button -->
                    <button @click="isSearchOpen = true" :class="navDark ? 'bg-gray-100 text-titan-navy' : 'bg-white/10 text-white hover:bg-white/20'" class="w-10 h-10 rounded-lg flex items-center justify-center hover:bg-accent-orange hover:text-white transition-all">
                        <x-lucide-search class="w-4 h-4" />
                    </button>

                    <!-- Mobile Menu Button -->
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen" :class="navDark ? 'bg-titan-navy text-white' : 'bg-white/10 text-white'" class="lg:hidden w-10 h-10 rounded-lg flex items-center justify-center transition-colors">
                        <span x-show="!isMobileMenuOpen"><x-lucide-menu class="w-5 h-5" /></span>
                        <span x-show="isMobileMenuOpen" style="display:none"><x-lucide-x class="w-5 h-5" /></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Border -->
        <div :class="navDark ? 'bg-gray-100' : 'bg-transparent'" class="h-[1px]"></div>
    </nav>

    <!-- MOBILE MENU -->
    <div x-show="isMobileMenuOpen" x-collapse style="display: none;" class="lg:hidden bg-white border-b border-gray-100 shadow-xl overflow-hidden w-full">
        <div class="max-h-[70vh] overflow-y-auto w-full">
            <div class="p-4 space-y-1">
                <!-- About Us -->
                <div>
                    <div class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 cursor-pointer" @click="expandedMobileItem = expandedMobileItem === 0 ? null : 0">
                        <a href="/about" class="font-bold text-titan-navy">{{ __('About Us') }}</a>
                        <x-lucide-chevron-down class="w-4 h-4 text-titan-navy/50 transition-transform duration-300" x-bind:class="expandedMobileItem === 0 ? 'rotate-180' : ''" />
                    </div>
                    <div x-show="expandedMobileItem === 0" x-collapse style="display:none" class="ml-4 mt-1 space-y-1">
                        <a href="/about#profile" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-accent-orange/10 text-titan-navy/70 hover:text-accent-orange transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent-orange"></div>
                            <span class="text-sm font-medium">{{ __('Company Profile') }}</span>
                        </a>
                        <a href="/about#leadership" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-accent-orange/10 text-titan-navy/70 hover:text-accent-orange transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent-orange"></div>
                            <span class="text-sm font-medium">{{ __('Leadership') }}</span>
                        </a>
                        <a href="/about#safety" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-accent-orange/10 text-titan-navy/70 hover:text-accent-orange transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent-orange"></div>
                            <span class="text-sm font-medium">{{ __('Quality & Safety') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Services -->
                <div>
                    <div class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 cursor-pointer" @click="expandedMobileItem = expandedMobileItem === 1 ? null : 1">
                        <a href="/services" class="font-bold text-titan-navy">{{ __('Services') }}</a>
                        <x-lucide-chevron-down class="w-4 h-4 text-titan-navy/50 transition-transform duration-300" x-bind:class="expandedMobileItem === 1 ? 'rotate-180' : ''" />
                    </div>
                    <div x-show="expandedMobileItem === 1" x-collapse style="display:none" class="ml-4 mt-1 space-y-1">
                        <a href="/services/design-build" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-accent-orange/10 text-titan-navy/70 hover:text-accent-orange transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent-orange"></div>
                            <span class="text-sm font-medium">{{ __('Design & Build') }}</span>
                        </a>
                        <a href="/services/construction" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-accent-orange/10 text-titan-navy/70 hover:text-accent-orange transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent-orange"></div>
                            <span class="text-sm font-medium">{{ __('Construction') }}</span>
                        </a>
                        <a href="/services/project-management" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-accent-orange/10 text-titan-navy/70 hover:text-accent-orange transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent-orange"></div>
                            <span class="text-sm font-medium">{{ __('Project Management') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Projects -->
                <div>
                    <div class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 cursor-pointer" @click="expandedMobileItem = expandedMobileItem === 2 ? null : 2">
                        <a href="/projects" class="font-bold text-titan-navy">{{ __('Projects') }}</a>
                        <x-lucide-chevron-down class="w-4 h-4 text-titan-navy/50 transition-transform duration-300" x-bind:class="expandedMobileItem === 2 ? 'rotate-180' : ''" />
                    </div>
                    <div x-show="expandedMobileItem === 2" x-collapse style="display:none" class="ml-4 mt-1 space-y-1">
                        <a href="/projects?status=completed" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-accent-orange/10 text-titan-navy/70 hover:text-accent-orange transition-all mt-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent-orange"></div>
                            <span class="text-sm font-medium">{{ __('Completed Projects') }}</span>
                        </a>
                        <div class="ml-8 border-l border-gray-100 pl-2 space-y-1 my-1">
                            <a href="/projects/completed?type=Government+Office+Building" class="block px-3 py-1.5 text-xs font-medium text-titan-navy/60 hover:text-accent-orange transition-colors">{{ __('Government') }}</a>
                            <a href="/projects/completed?type=Water+Treatment+Plant" class="block px-3 py-1.5 text-xs font-medium text-titan-navy/60 hover:text-accent-orange transition-colors">{{ __('Water Treatment') }}</a>
                            <a href="/projects/completed?type=Slope+Construction" class="block px-3 py-1.5 text-xs font-medium text-titan-navy/60 hover:text-accent-orange transition-colors">{{ __('Slope') }}</a>
                            <a href="/projects/completed?type=Systems" class="block px-3 py-1.5 text-xs font-medium text-titan-navy/60 hover:text-accent-orange transition-colors">{{ __('Systems') }}</a>
                        </div>
                        <a href="/projects?status=in-progress" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-accent-orange/10 text-titan-navy/70 hover:text-accent-orange transition-all mt-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent-orange"></div>
                            <span class="text-sm font-medium">{{ __('Project in Progress') }}</span>
                        </a>
                        <div class="ml-8 border-l border-gray-100 pl-2 space-y-1 my-1">
                            <a href="/projects/implementation?type=Water+Treatment+Plant" class="block px-3 py-1.5 text-xs font-medium text-titan-navy/60 hover:text-accent-orange transition-colors">{{ __('Water Treatment') }}</a>
                            <a href="/projects/implementation?type=Systems" class="block px-3 py-1.5 text-xs font-medium text-titan-navy/60 hover:text-accent-orange transition-colors">{{ __('Systems') }}</a>
                        </div>
                    </div>
                </div>

                <!-- News -->
                <div>
                    <div class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 cursor-pointer" @click="expandedMobileItem = expandedMobileItem === 3 ? null : 3">
                        <a href="/news" class="font-bold text-titan-navy">{{ __('News') }}</a>
                        <x-lucide-chevron-down class="w-4 h-4 text-titan-navy/50 transition-transform duration-300" x-bind:class="expandedMobileItem === 3 ? 'rotate-180' : ''" />
                    </div>
                    <div x-show="expandedMobileItem === 3" x-collapse style="display:none" class="ml-4 mt-1 space-y-1">
                        <a href="/news" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-accent-orange/10 text-titan-navy/70 hover:text-accent-orange transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent-orange"></div>
                            <span class="text-sm font-medium">{{ __('News & Updates') }}</span>
                        </a>
                        <a href="/documents" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-accent-orange/10 text-titan-navy/70 hover:text-accent-orange transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent-orange"></div>
                            <span class="text-sm font-medium">{{ __('Doc Collection') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Careers -->
                <a href="/careers" class="block px-4 py-3 rounded-lg hover:bg-gray-50 font-bold text-titan-navy">{{ __('Careers') }}</a>

                <!-- Contact -->
                <a href="/contact" class="block px-4 py-3 rounded-lg hover:bg-gray-50 font-bold text-titan-navy">{{ __('Contact') }}</a>
            </div>

            <!-- Mobile Contact Info -->
            <div class="p-4 bg-gray-50 border-t border-gray-100">
                <div class="flex flex-col gap-2 text-sm">
                    <a href="tel:+85523999888" class="flex items-center gap-2 text-titan-navy/70">
                        <x-lucide-phone class="text-accent-orange w-3.5 h-3.5" />
                        +855 23 999 888
                    </a>
                    <a href="mailto:info@kimmex.com" class="flex items-center gap-2 text-titan-navy/70">
                        <x-lucide-mail class="text-accent-orange w-3.5 h-3.5" />
                        info@kimmex.com
                    </a>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('lang.switch', 'en') }}" class="flex-1 py-2 rounded text-xs font-bold transition-all border text-center {{ app()->getLocale() === 'en' ? 'bg-accent-orange text-white border-accent-orange' : 'bg-white text-titan-navy border-gray-200' }}">
                        English
                    </a>
                    <a href="{{ route('lang.switch', 'km') }}" class="flex-1 py-2 rounded text-xs font-bold transition-all border text-center {{ app()->getLocale() === 'km' ? 'bg-accent-orange text-white border-accent-orange' : 'bg-white text-titan-navy border-gray-200' }}">
                        ខ្មែរ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- SEARCH MODAL -->
    <div x-show="isSearchOpen" style="display:none" class="fixed inset-0 z-[200] flex items-start justify-center p-4 pt-[10vh]">
        <div x-show="isSearchOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="isSearchOpen = false" class="absolute inset-0 bg-titan-navy/80 backdrop-blur-sm"></div>
        <div x-show="isSearchOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 -translate-y-4 scale-95" class="relative w-full max-w-2xl bg-white shadow-2xl overflow-hidden rounded-xl">
            <!-- Search Input -->
            <div class="relative">
                <x-lucide-search class="absolute left-5 top-1/2 -translate-y-1/2 text-titan-navy/30 w-5 h-5" />
                <input type="text" x-model="searchQuery" @keydown.enter="if(searchQuery.trim()) window.location.href='/projects?search=' + encodeURIComponent(searchQuery.trim())" @keydown.escape="isSearchOpen = false" x-ref="searchInput" x-effect="if(isSearchOpen) $nextTick(() => $refs.searchInput?.focus())" placeholder="{{ __('Search projects, services...') }}" class="w-full bg-transparent pl-14 pr-24 py-5 text-lg font-medium text-titan-navy outline-none placeholder:text-titan-navy/30 border-b border-gray-100" />
                <button @click="isSearchOpen = false" class="absolute right-4 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs font-bold uppercase tracking-widest text-titan-navy/50 hover:text-accent-orange transition-colors bg-gray-100 rounded-md">ESC</button>
            </div>
            <!-- Quick Links -->
            <div class="p-5">
                <p class="text-xs font-bold text-titan-navy/40 uppercase tracking-widest mb-4">{{ __('Quick Links') }}</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="/projects" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-accent-orange hover:text-white text-titan-navy font-medium transition-all group">
                        <span class="text-lg">🏗️</span>
                        <span class="text-sm">{{ __('Projects') }}</span>
                        <x-lucide-arrow-right class="w-3.5 h-3.5 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                    </a>
                    <a href="/services" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-accent-orange hover:text-white text-titan-navy font-medium transition-all group">
                        <span class="text-lg">⚙️</span>
                        <span class="text-sm">{{ __('Services') }}</span>
                        <x-lucide-arrow-right class="w-3.5 h-3.5 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                    </a>
                    <a href="/about" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-accent-orange hover:text-white text-titan-navy font-medium transition-all group">
                        <span class="text-lg">🏢</span>
                        <span class="text-sm">{{ __('About Us') }}</span>
                        <x-lucide-arrow-right class="w-3.5 h-3.5 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                    </a>
                    <a href="/contact" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-accent-orange hover:text-white text-titan-navy font-medium transition-all group">
                        <span class="text-lg">📞</span>
                        <span class="text-sm">{{ __('Contact') }}</span>
                        <x-lucide-arrow-right class="w-3.5 h-3.5 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                    </a>
                </div>
            </div>
            <!-- Categories -->
            <div class="px-5 pb-5">
                <p class="text-xs font-bold text-titan-navy/40 uppercase tracking-widest mb-3">{{ __('Categories') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Commercial', 'Infrastructure', 'Industrial', 'Construction', 'Government'] as $tag)
                        <a href="/projects?search={{ $tag }}" class="px-4 py-2 bg-titan-navy/5 text-titan-navy text-xs font-bold uppercase rounded-full cursor-pointer hover:bg-accent-orange hover:text-white transition-all">
                            {{ __($tag) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</header>
