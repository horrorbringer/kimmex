@php $isHeroPage = request()->routeIs(['home', 'about', 'services.index', 'services.show', 'projects.index', 'projects.show', 'news.index', 'news.show', 'contact', 'careers', 'careers.show']); @endphp
<header x-data="{
    isHeroPage: {{ $isHeroPage ? 'true' : 'false' }},
    isScrolled: false,
    get navDark() { return true; },

    isSearchOpen: false,
    isMobileMenuOpen: false,
    expandedMobileItem: null,
    searchQuery: '',
    scrollHandler: null,
    resizeHandler: null,
    init() {
        const self = this;
        let ticking = false;
        const syncScroll = () => {
            self.isScrolled = window.scrollY > 50;
            ticking = false;
        };

        this.scrollHandler = () => {
            if (!ticking) {
                window.requestAnimationFrame(syncScroll);
                ticking = true;
            }
        };

        this.resizeHandler = () => {
            if (window.innerWidth >= 1024) self.isMobileMenuOpen = false;
        };

        window.addEventListener('scroll', this.scrollHandler, { passive: true });
        window.addEventListener('resize', this.resizeHandler, { passive: true });
        syncScroll();
    },
    destroy() {
        window.removeEventListener('scroll', this.scrollHandler);
        window.removeEventListener('resize', this.resizeHandler);
    }
}" class="fixed top-0 left-0 w-full z-[100]">

    @php
        $profile = $globalSettings['profile'] ?? [];
        $email = $profile['email'] ?? 'info@kimmex.com.kh';
        $phone = $profile['phone'] ?? '+855 23 999 999';
        $facebook = $profile['facebook'] ?? null;
        $linkedin = $profile['linkedin'] ?? null;
        $youtube = $profile['youtube'] ?? null;
        $instagram = $profile['instagram'] ?? null;
        $telegram = $profile['telegram'] ?? null;
        $tiktok = $profile['tiktok'] ?? null;

        $lang = $siteLocale;
        $companyName = $profile[$lang]['company_name'] ?? $profile['en']['company_name'] ?? 'KIMMEX';
        $tagline = $globalSettings['brand']['tagline'] ?? $profile['en']['tagline'] ?? __('Construction & Investment');
        $logo = (! empty($profile['logo_header'])) ? $profile['logo_header'] : ($profile['logo'] ?? null);
        $logoUrl = \App\Support\PublicStorage::urlIfExists($logo, '/logo.webp');

        $navProjectFilters = \Illuminate\Support\Facades\Cache::remember('nav_project_filters_v1_' . $lang, now()->addHours(12), function () use ($lang) {
            $categoriesForStatus = function (string $status) use ($lang): array {
                return \App\Models\ProjectCategory::where('isActive', true)
                    ->whereHas('projects', fn ($query) => $query
                        ->where('isActive', true)
                        ->where('status', $status))
                    ->get()
                    ->sortBy(fn ($category) => $category->localizedName($lang))
                    ->map(fn ($category) => [
                        'slug' => $category->slug,
                        'name' => $category->localizedName($lang),
                    ])
                    ->values()
                    ->all();
            };

            return [
                'completed' => $categoriesForStatus(\App\Enums\ProjectStatus::COMPLETED->value),
                'ongoing' => $categoriesForStatus(\App\Enums\ProjectStatus::ONGOING->value),
            ];
        });

        $navServices = \Illuminate\Support\Facades\Cache::remember('nav_services_' . $lang, now()->addHours(12), function () use ($lang) {
            return \App\Models\Service::where('isActive', true)
                ->orderBy('orderIndex')
                ->orderBy('id')
                ->get()
                ->map(fn($svc) => [
                    'slug' => $svc->slug,
                    'title' => $svc->getTranslation('title', $lang)
                ])
                ->values()
                ->all();
        });
    @endphp
    <!-- TOP BAR -->
    <div :class="isScrolled ? 'hidden' : 'h-8 opacity-100 border-gray-100 bg-white'"
        class="text-titan-navy text-[11px] tracking-wide font-medium transition-all duration-500 overflow-hidden relative border-b">
        <div class="max-w-[1600px] mx-auto px-3 sm:px-6 h-full flex justify-between items-center">
            <div class="flex gap-2 sm:gap-6 items-center">
                <a href="tel:{{ str_replace(' ', '', $phone) }}"
                    class="flex items-center gap-1.5 hover:text-titan-red cursor-pointer transition whitespace-nowrap text-titan-navy/60 hover:text-titan-red">
                    <x-lucide-phone class="text-titan-red shrink-0 w-3 h-3" />
                    <span class="text-[11px] sm:hidden font-semibold tracking-normal">{{ $phone }}</span>
                    <span class="hidden sm:inline">{{ $phone }}</span>
                </a>
                <a href="mailto:{{ $email }}"
                    class="hidden md:flex items-center gap-2 hover:text-titan-red cursor-pointer transition text-titan-navy/60 hover:text-titan-red">
                    <x-lucide-mail class="text-titan-red w-3 h-3" />
                    {{ $email }}
                </a>
            </div>

            <div class="flex gap-4 items-center">
                <div class="hidden sm:flex items-center gap-2 text-titan-navy/40">
                    <x-lucide-map-pin class="w-3 h-3" />
                    <span>{{ __('Phnom Penh, Cambodia') }}</span>
                </div>
                <div class="w-[1px] h-3 bg-gray-200 hidden sm:block"></div>
                <div class="hidden sm:flex gap-2">
                    @if($facebook && $facebook !== '#')
                        <a href="{{ $facebook }}" target="_blank"
                            class="w-6 h-6 rounded bg-social-facebook flex items-center justify-center hover:brightness-110 transition-all duration-300">
                            <x-social-icon network="facebook" class="w-3 h-3 text-white" />
                        </a>
                    @endif
                    @if($linkedin && $linkedin !== '#')
                        <a href="{{ $linkedin }}" target="_blank"
                            class="w-6 h-6 rounded bg-social-linkedin flex items-center justify-center hover:brightness-110 transition-all duration-300">
                            <x-social-icon network="linkedin" class="w-3 h-3 text-white" />
                        </a>
                    @endif
                    @if($youtube && $youtube !== '#')
                        <a href="{{ $youtube }}" target="_blank"
                            class="w-6 h-6 rounded bg-social-youtube flex items-center justify-center hover:brightness-110 transition-all duration-300">
                            <x-social-icon network="youtube" class="w-3 h-3 text-white" />
                        </a>
                    @endif
                    @if($instagram && $instagram !== '#')
                        <a href="{{ $instagram }}" target="_blank"
                            class="w-6 h-6 rounded bg-social-instagram flex items-center justify-center hover:brightness-110 transition-all duration-300">
                            <x-social-icon network="instagram" class="w-3 h-3 text-white" />
                        </a>
                    @endif
                    @if($tiktok && $tiktok !== '#')
                        <a href="{{ $tiktok }}" target="_blank" rel="noopener noreferrer"
                            class="w-6 h-6 rounded bg-black flex items-center justify-center hover:brightness-125 transition-all duration-300"
                            aria-label="TikTok">
                            <x-social-icon network="tiktok" class="w-3 h-3 text-white" />
                        </a>
                    @endif
                    @if($telegram && $telegram !== '#')
                        <a href="{{ $telegram }}" target="_blank"
                            class="w-6 h-6 rounded bg-social-telegram flex items-center justify-center hover:brightness-110 transition-all duration-300">
                            <x-social-icon network="telegram" class="w-3 h-3 text-white" />
                        </a>
                    @endif
                </div>

                @if(auth()->check() && auth()->user()->isAdmin())
                    <div class="w-[1px] h-3 bg-gray-200 hidden sm:block"></div>
                    <a href="/admin"
                        class="hidden sm:flex items-center gap-2 px-2 py-1 bg-white/10 hover:bg-titan-red rounded transition-colors group">
                        <x-lucide-shield class="text-titan-red group-hover:text-white w-2.5 h-2.5" />
                        <span class="text-[9px] font-bold">{{ __('ADMIN') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <nav class="bg-white shadow-sm border-b border-gray-100 w-full transition-all duration-500">
        <div class="max-w-[1600px] mx-auto px-6">
            <div class="flex justify-between items-center h-20">

                <!-- Logo -->
                <a href="/" wire:navigate.hover class="flex min-w-0 items-center group cursor-pointer">
                    <img src="{{ $logoUrl }}" alt="{{ $companyName }}"
                        class="h-14 w-auto max-w-full object-contain transition-all duration-300"
                        style="max-width: min(62vw, 32rem);" loading="eager" decoding="async" />
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center">

                    <!-- About Us -->
                    <div class="relative group/nav">
                        <a href="/about" wire:navigate.hover class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'"
                                class="{{ app()->getLocale() === 'km' ? 'font-khmer text-[14px] tracking-normal' : 'text-[13px] font-semibold uppercase tracking-wide' }} transition-all duration-200 group-hover/nav:text-titan-red">{{ __('About Us') }}</span>
                            <x-lucide-chevron-down stroke-width="2.5" :class="navDark ? 'text-titan-navy/50' : 'text-white/50'"
                                class="w-3 h-3 transition-transform duration-300 group-hover/nav:-rotate-180 group-hover/nav:text-titan-red" />
                            <span
                                class="absolute bottom-0 left-5 right-5 h-[3px] bg-titan-red transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                        <div
                            class="absolute top-full left-0 pt-0 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible transition-all duration-300 transform translate-y-2 group-hover/nav:translate-y-0 z-50">
                            <div
                                class="bg-white/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.15)] rounded border border-gray-100 min-w-[280px] p-2">
                                <a href="/about#profile"
                                    class="flex items-center px-4 py-3.5 rounded hover:bg-gray-50 transition-all duration-200 group/item">
                                    <div>
                                        <div
                                            class="font-medium text-titan-navy group-hover/item:text-titan-red text-sm transition-colors">
                                            {{ __('Company Profile') }}
                                        </div>
                                        <div
                                            class="text-[10px] text-titan-navy/40 mt-0.5 group-hover/item:text-titan-navy/60 transition-colors">
                                            {{ __('Learn about our history') }}
                                        </div>
                                    </div>
                                </a>
                                <a href="/about#leadership"
                                    class="flex items-center px-4 py-3.5 rounded hover:bg-gray-50 transition-all duration-200 group/item">
                                    <div>
                                        <div
                                            class="font-medium text-titan-navy group-hover/item:text-titan-red text-sm transition-colors">
                                            {{ __('Leadership') }}
                                        </div>
                                        <div
                                            class="text-[10px] text-titan-navy/40 mt-0.5 group-hover/item:text-titan-navy/60 transition-colors">
                                            {{ __('Meet our team') }}
                                        </div>
                                    </div>
                                </a>
                                <a href="/about#safety"
                                    class="flex items-center px-4 py-3.5 rounded hover:bg-gray-50 transition-all duration-200 group/item">
                                    <div>
                                        <div
                                            class="font-medium text-titan-navy group-hover/item:text-titan-red text-sm transition-colors">
                                            {{ __('Quality & Safety') }}
                                        </div>
                                        <div
                                            class="text-[10px] text-titan-navy/40 mt-0.5 group-hover/item:text-titan-navy/60 transition-colors">
                                            {{ __('Our standards') }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="relative group/nav">
                        <a href="/services" wire:navigate.hover class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'"
                                class="{{ app()->getLocale() === 'km' ? 'font-khmer text-[14px] tracking-normal' : 'text-[13px] font-semibold uppercase tracking-wide' }} transition-all duration-200 group-hover/nav:text-titan-red">{{ __('Services') }}</span>
                            <x-lucide-chevron-down stroke-width="2.5" :class="navDark ? 'text-titan-navy/50' : 'text-white/50'"
                                class="w-3 h-3 transition-transform duration-300 group-hover/nav:-rotate-180 group-hover/nav:text-titan-red" />
                            <span
                                class="absolute bottom-0 left-5 right-5 h-[3px] bg-titan-red transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                        <div
                            class="absolute top-full left-0 pt-0 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible transition-all duration-300 transform translate-y-2 group-hover/nav:translate-y-0 z-50">
                            <div
                                class="bg-white/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.15)] rounded border border-gray-100 min-w-[280px] p-2">
                                @foreach($navServices as $navService)
                                    <a href="/services/{{ $navService['slug'] }}"
                                        class="group/sub flex items-center justify-between px-4 py-3 rounded text-sm font-medium text-titan-navy/70 hover:text-titan-navy hover:bg-gray-50 transition-all">
                                        <span>{{ $navService['title'] }}</span>
                                        <x-lucide-arrow-right
                                            class="w-3.5 h-3.5 opacity-0 -translate-x-2 group-hover/sub:opacity-100 group-hover/sub:translate-x-0 transition-all text-titan-red" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Projects -->
                    <div class="relative group/nav">
                        <a href="/projects" wire:navigate.hover class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'"
                                class="{{ app()->getLocale() === 'km' ? 'font-khmer text-[14px] tracking-normal' : 'text-[13px] font-semibold uppercase tracking-wide' }} transition-all duration-200 group-hover/nav:text-titan-red">{{ __('Projects') }}</span>
                            <x-lucide-chevron-down stroke-width="2.5" :class="navDark ? 'text-titan-navy/50' : 'text-white/50'"
                                class="w-3 h-3 transition-transform duration-300 group-hover/nav:-rotate-180 group-hover/nav:text-titan-red" />
                            <span
                                class="absolute bottom-0 left-5 right-5 h-[3px] bg-titan-red transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                        <div
                            class="absolute top-full left-0 pt-0 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible transition-all duration-300 transform translate-y-2 group-hover/nav:translate-y-0 z-50">
                            <div
                                class="bg-white/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.15)] rounded border border-gray-100 min-w-[280px] p-2">
                                <!-- Completed Projects with 3rd Level Flyout -->
                                @if($navProjectFilters['completed'] !== [])
                                <div class="relative group/nested">
                                    <a href="/projects?status=completed"
                                        class="flex items-center justify-between px-4 py-3.5 rounded hover:bg-gray-50 transition-all duration-200 group/item">
                                        <div>
                                            <div
                                                class="font-medium text-titan-navy group-hover/item:text-titan-red text-sm transition-colors">
                                                {{ __('Completed Projects') }}
                                            </div>
                                            <div
                                                class="text-[10px] text-titan-navy/40 mt-0.5 group-hover/item:text-titan-navy/60 transition-colors">
                                                {{ __('View our portfolio') }}
                                            </div>
                                        </div>
                                        <x-lucide-arrow-right
                                            class="w-3.5 h-3.5 text-titan-navy/30 group-hover/item:text-titan-red transition-colors" />
                                    </a>
                                    <div
                                        class="absolute left-full top-0 ml-2 opacity-0 invisible group-hover/nested:opacity-100 group-hover/nested:visible transition-all duration-300 transform translate-x-2 group-hover/nested:translate-x-0 z-[60]">
                                        <div
                                            class="bg-white/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.15)] rounded border border-gray-100 min-w-[240px] p-2">
                                            @foreach($navProjectFilters['completed'] as $navCat)
                                                <a href="/projects?status=completed&category={{ urlencode($navCat['slug']) }}"
                                                    class="group/sub flex items-center justify-between px-4 py-3 rounded text-sm font-medium text-titan-navy/70 hover:text-titan-navy hover:bg-gray-50 transition-all">
                                                    <span>{{ $navCat['name'] }}</span>
                                                    <x-lucide-arrow-right
                                                        class="w-3.5 h-3.5 opacity-0 -translate-x-2 group-hover/sub:opacity-100 group-hover/sub:translate-x-0 transition-all text-titan-red" />
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Projects in Progress with 3rd Level Flyout -->
                                @if($navProjectFilters['ongoing'] !== [])
                                <div class="relative group/nested mt-1">
                                    <a href="/projects?status=ongoing"
                                        class="flex items-center justify-between px-4 py-3.5 rounded hover:bg-gray-50 transition-all duration-200 group/item">
                                        <div>
                                            <div
                                                class="font-medium text-titan-navy group-hover/item:text-titan-red text-sm transition-colors">
                                                {{ __('Project in Progress') }}
                                            </div>
                                            <div
                                                class="text-[10px] text-titan-navy/40 mt-0.5 group-hover/item:text-titan-navy/60 transition-colors">
                                                {{ __('Current developments') }}
                                            </div>
                                        </div>
                                        <x-lucide-arrow-right
                                            class="w-3.5 h-3.5 text-titan-navy/30 group-hover/item:text-titan-red transition-colors" />
                                    </a>
                                    <div
                                        class="absolute left-full top-0 ml-2 opacity-0 invisible group-hover/nested:opacity-100 group-hover/nested:visible transition-all duration-300 transform translate-x-2 group-hover/nested:translate-x-0 z-[60]">
                                        <div
                                            class="bg-white/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.15)] rounded border border-gray-100 min-w-[240px] p-2">
                                            @foreach($navProjectFilters['ongoing'] as $navCat)
                                                <a href="/projects?status=ongoing&category={{ urlencode($navCat['slug']) }}"
                                                    class="group/sub flex items-center justify-between px-4 py-3 rounded text-sm font-medium text-titan-navy/70 hover:text-titan-navy hover:bg-gray-50 transition-all">
                                                    <span>{{ $navCat['name'] }}</span>
                                                    <x-lucide-arrow-right
                                                        class="w-3.5 h-3.5 opacity-0 -translate-x-2 group-hover/sub:opacity-100 group-hover/sub:translate-x-0 transition-all text-titan-red" />
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- News -->
                    <div class="relative group/nav">
                        <a href="/news" wire:navigate.hover class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'"
                                class="{{ app()->getLocale() === 'km' ? 'font-khmer text-[14px] tracking-normal' : 'text-[13px] font-semibold uppercase tracking-wide' }} transition-all duration-200 group-hover/nav:text-titan-red">{{ __('News') }}</span>
                            <x-lucide-chevron-down stroke-width="2.5" :class="navDark ? 'text-titan-navy/50' : 'text-white/50'"
                                class="w-3 h-3 transition-transform duration-300 group-hover/nav:-rotate-180 group-hover/nav:text-titan-red" />
                            <span
                                class="absolute bottom-0 left-5 right-5 h-[3px] bg-titan-red transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                        <div
                            class="absolute top-full left-0 pt-0 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible transition-all duration-300 transform translate-y-2 group-hover/nav:translate-y-0 z-50">
                            <div
                                class="bg-white/95 backdrop-blur-xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.15)] rounded border border-gray-100 min-w-[280px] p-2">
                                <a href="/news"
                                    class="flex items-center px-4 py-3.5 rounded hover:bg-gray-50 transition-all duration-200 group/item">
                                    <div>
                                        <div
                                            class="font-medium text-titan-navy group-hover/item:text-titan-red text-sm transition-colors">
                                            {{ __('News & Updates') }}
                                        </div>
                                        <div
                                            class="text-[10px] text-titan-navy/40 mt-0.5 group-hover/item:text-titan-navy/60 transition-colors">
                                            {{ __('Latest announcements') }}
                                        </div>
                                    </div>
                                </a>
                                @if($hasPublicDocuments)
                                    <a href="/documents"
                                        class="flex items-center px-4 py-3.5 rounded hover:bg-gray-50 transition-all duration-200 group/item">
                                        <div>
                                            <div
                                                class="font-medium text-titan-navy group-hover/item:text-titan-red text-sm transition-colors">
                                                {{ __('Doc Collection') }}
                                            </div>
                                            <div
                                                class="text-[10px] text-titan-navy/40 mt-0.5 group-hover/item:text-titan-navy/60 transition-colors">
                                                {{ __('Resources & documents') }}
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Careers (no dropdown) -->
                    <div class="relative group/nav">
                        <a href="/careers" wire:navigate.hover class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'"
                                class="{{ app()->getLocale() === 'km' ? 'font-khmer text-[14px] tracking-normal' : 'text-[13px] font-semibold uppercase tracking-wide' }} transition-all duration-200 group-hover/nav:text-titan-red">{{ __('Careers') }}</span>
                            <span
                                class="absolute bottom-0 left-5 right-5 h-[3px] bg-titan-red transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                    </div>

                    <!-- Contact (no dropdown) -->
                    <div class="relative group/nav">
                        <a href="/contact" wire:navigate.hover class="flex items-center gap-1 px-5 py-8 cursor-pointer relative">
                            <span :class="navDark ? 'text-titan-navy' : 'text-white'"
                                class="{{ app()->getLocale() === 'km' ? 'font-khmer text-[14px] tracking-normal' : 'text-[13px] font-semibold uppercase tracking-wide' }} transition-all duration-200 group-hover/nav:text-titan-red">{{ __('Contact') }}</span>
                            <span
                                class="absolute bottom-0 left-5 right-5 h-[3px] bg-titan-red transition-all duration-300 opacity-0 group-hover/nav:opacity-100"></span>
                        </a>
                    </div>

                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Language Switcher -->
                    <div :class="navDark ? 'bg-gray-100' : 'bg-white/10'"
                        class="hidden sm:flex items-center gap-0.5 rounded p-0.5 h-8 border border-white/5">
                        <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
                            class="h-full flex items-center px-2.5 rounded text-[9px] font-black tracking-widest transition-all"
                            :class="{{ app()->getLocale() === 'en' ? "'bg-titan-red text-white shadow-md shadow-titan-red/20'" : "navDark ? 'text-titan-navy/40 hover:text-titan-navy hover:bg-gray-200' : 'text-white/40 hover:text-white hover:bg-white/10'" }}">
                            EN
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['lang' => 'km']) }}"
                            class="h-full flex items-center px-2.5 rounded text-[9px] font-black tracking-widest transition-all"
                            :class="{{ app()->getLocale() === 'km' ? "'bg-titan-red text-white shadow-md shadow-titan-red/20'" : "navDark ? 'text-titan-navy/40 hover:text-titan-navy hover:bg-gray-200' : 'text-white/40 hover:text-white hover:bg-white/10'" }}">
                            KH
                        </a>
                    </div>

                    <!-- Search Button -->
                    <button @click="isSearchOpen = true"
                        :class="navDark ? 'bg-gray-100 text-titan-navy' : 'bg-white/10 text-white hover:bg-white/20'"
                        class="hidden lg:flex w-8 h-8 rounded items-center justify-center hover:bg-titan-red hover:text-white transition-all">
                        <x-lucide-search class="w-3.5 h-3.5" />
                    </button>

                    <!-- Mobile Menu Button -->
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                        :class="navDark ? 'bg-titan-navy text-white' : 'bg-white/10 text-white'"
                        class="lg:hidden w-8 h-8 rounded flex items-center justify-center transition-colors">
                        <span x-show="!isMobileMenuOpen"><x-lucide-menu class="w-4 h-4" /></span>
                        <span x-show="isMobileMenuOpen" style="display:none"><x-lucide-x class="w-4 h-4" /></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Border -->
        <div :class="navDark ? 'bg-gray-100' : 'bg-transparent'" class="h-[1px]"></div>
    </nav>

    <!-- MOBILE MENU -->
    <div x-show="isMobileMenuOpen" x-collapse style="display: none;"
        class="lg:hidden bg-white border-b border-gray-100 shadow-xl overflow-hidden w-full">
        <div class="max-h-[70vh] overflow-y-auto w-full">
            <div class="p-4 space-y-1">
                <!-- About Us -->
                <div>
                    <div class="flex items-center justify-between px-4 py-3 rounded hover:bg-gray-50 cursor-pointer"
                        @click="expandedMobileItem = expandedMobileItem === 0 ? null : 0">
                        <a href="/about" wire:navigate.hover
                            class="{{ app()->getLocale() === 'km' ? 'font-khmer text-lg' : 'font-semibold' }} text-titan-navy">{{ __('About Us') }}</a>
                        <x-lucide-chevron-down class="w-4 h-4 text-titan-navy/50 transition-transform duration-300"
                            x-bind:class="expandedMobileItem === 0 ? 'rotate-180' : ''" />
                    </div>
                    <div x-show="expandedMobileItem === 0" x-collapse style="display:none" class="ml-4 mt-1 space-y-1">
                        <a href="/about#profile"
                            class="flex items-center gap-3 px-4 py-2.5 rounded hover:bg-titan-red/10 text-titan-navy/70 hover:text-titan-red transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-titan-red"></div>
                            <span class="text-sm font-medium">{{ __('Company Profile') }}</span>
                        </a>
                        <a href="/about#leadership"
                            class="flex items-center gap-3 px-4 py-2.5 rounded hover:bg-titan-red/10 text-titan-navy/70 hover:text-titan-red transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-titan-red"></div>
                            <span class="text-sm font-medium">{{ __('Leadership') }}</span>
                        </a>
                        <a href="/about#safety"
                            class="flex items-center gap-3 px-4 py-2.5 rounded hover:bg-titan-red/10 text-titan-navy/70 hover:text-titan-red transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-titan-red"></div>
                            <span class="text-sm font-medium">{{ __('Quality & Safety') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Services -->
                <div>
                    <div class="flex items-center justify-between px-4 py-3 rounded hover:bg-gray-50 cursor-pointer"
                        @click="expandedMobileItem = expandedMobileItem === 1 ? null : 1">
                        <a href="/services" wire:navigate.hover
                            class="{{ app()->getLocale() === 'km' ? 'font-khmer text-lg' : 'font-semibold' }} text-titan-navy">{{ __('Services') }}</a>
                        <x-lucide-chevron-down class="w-4 h-4 text-titan-navy/50 transition-transform duration-300"
                            x-bind:class="expandedMobileItem === 1 ? 'rotate-180' : ''" />
                    </div>
                    <div x-show="expandedMobileItem === 1" x-collapse style="display:none" class="ml-4 mt-1 space-y-1">
                        @foreach($navServices as $navService)
                            <a href="/services/{{ $navService['slug'] }}"
                                class="flex items-center gap-3 px-4 py-2.5 rounded hover:bg-titan-red/10 text-titan-navy/70 hover:text-titan-red transition-all">
                                <div class="w-1.5 h-1.5 rounded-full bg-titan-red"></div>
                                <span class="text-sm font-medium">{{ $navService['title'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Projects -->
                <div>
                    <div class="flex items-center justify-between px-4 py-3 rounded hover:bg-gray-50 cursor-pointer"
                        @click="expandedMobileItem = expandedMobileItem === 2 ? null : 2">
                        <a href="/projects" wire:navigate.hover
                            class="{{ app()->getLocale() === 'km' ? 'font-khmer text-lg' : 'font-semibold' }} text-titan-navy">{{ __('Projects') }}</a>
                        <x-lucide-chevron-down class="w-4 h-4 text-titan-navy/50 transition-transform duration-300"
                            x-bind:class="expandedMobileItem === 2 ? 'rotate-180' : ''" />
                    </div>
                    <div x-show="expandedMobileItem === 2" x-collapse style="display:none" class="ml-4 mt-1 space-y-1">
                        @if($navProjectFilters['completed'] !== [])
                        <a href="/projects?status=completed"
                            class="flex items-center gap-3 px-4 py-2.5 rounded hover:bg-titan-red/10 text-titan-navy/70 hover:text-titan-red transition-all mt-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-titan-red"></div>
                            <span class="text-sm font-medium">{{ __('Completed Projects') }}</span>
                        </a>
                        <div class="ml-8 border-l border-gray-100 pl-2 space-y-1 my-1">
                            @foreach($navProjectFilters['completed'] as $navCat)
                                <a href="/projects?status=completed&category={{ urlencode($navCat['slug']) }}"
                                    class="block px-3 py-1.5 text-xs font-medium text-titan-navy/60 hover:text-titan-red transition-colors">
                                    {{ $navCat['name'] }}
                                </a>
                            @endforeach
                        </div>
                        @endif
                        @if($navProjectFilters['ongoing'] !== [])
                        <a href="/projects?status=ongoing"
                            class="flex items-center gap-3 px-4 py-2.5 rounded hover:bg-titan-red/10 text-titan-navy/70 hover:text-titan-red transition-all mt-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-titan-red"></div>
                            <span class="text-sm font-medium">{{ __('Project in Progress') }}</span>
                        </a>
                        <div class="ml-8 border-l border-gray-100 pl-2 space-y-1 my-1">
                            @foreach($navProjectFilters['ongoing'] as $navCat)
                                <a href="/projects?status=ongoing&category={{ urlencode($navCat['slug']) }}"
                                    class="block px-3 py-1.5 text-xs font-medium text-titan-navy/60 hover:text-titan-red transition-colors">
                                    {{ $navCat['name'] }}
                                </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- News -->
                <div>
                    <div class="flex items-center justify-between px-4 py-3 rounded hover:bg-gray-50 cursor-pointer"
                        @click="expandedMobileItem = expandedMobileItem === 3 ? null : 3">
                        <a href="/news" wire:navigate.hover
                            class="{{ app()->getLocale() === 'km' ? 'font-khmer text-lg' : 'font-semibold' }} text-titan-navy">{{ __('News') }}</a>
                        <x-lucide-chevron-down class="w-4 h-4 text-titan-navy/50 transition-transform duration-300"
                            x-bind:class="expandedMobileItem === 3 ? 'rotate-180' : ''" />
                    </div>
                    <div x-show="expandedMobileItem === 3" x-collapse style="display:none" class="ml-4 mt-1 space-y-1">
                        <a href="/news"
                            class="flex items-center gap-3 px-4 py-2.5 rounded hover:bg-titan-red/10 text-titan-navy/70 hover:text-titan-red transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-titan-red"></div>
                            <span class="text-sm font-medium">{{ __('News & Updates') }}</span>
                        </a>
                        @if($hasPublicDocuments)
                            <a href="/documents"
                                class="flex items-center gap-3 px-4 py-2.5 rounded hover:bg-titan-red/10 text-titan-navy/70 hover:text-titan-red transition-all">
                                <div class="w-1.5 h-1.5 rounded-full bg-titan-red"></div>
                                <span class="text-sm font-medium">{{ __('Doc Collection') }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Careers -->
                <a href="/careers" wire:navigate.hover
                    class="block px-4 py-3 rounded hover:bg-gray-50 {{ app()->getLocale() === 'km' ? 'font-khmer text-lg' : 'font-semibold' }} text-titan-navy">{{ __('Careers') }}</a>

                <!-- Contact -->
                <a href="/contact" wire:navigate.hover
                    class="block px-4 py-3 rounded hover:bg-gray-50 {{ app()->getLocale() === 'km' ? 'font-khmer text-lg' : 'font-semibold' }} text-titan-navy">{{ __('Contact') }}</a>
            </div>

            <!-- Mobile Contact Info -->
            <div class="p-4 bg-gray-50 border-t border-gray-100">
                <div class="flex flex-col gap-2 text-sm">
                    <a href="tel:{{ str_replace(' ', '', $phone) }}" class="flex items-center gap-2 text-titan-navy/70">
                        <x-lucide-phone class="text-titan-red w-3.5 h-3.5" />
                        {{ $phone }}
                    </a>
                    <a href="mailto:{{ $email }}" class="flex items-center gap-2 text-titan-navy/70">
                        <x-lucide-mail class="text-titan-red w-3.5 h-3.5" />
                        {{ $email }}
                    </a>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
                        class="flex-1 py-2 rounded text-xs font-bold transition-all border text-center {{ app()->getLocale() === 'en' ? 'bg-titan-red text-white border-titan-red' : 'bg-white text-titan-navy border-gray-200' }}">
                        {{ __('English') }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'km']) }}"
                        class="flex-1 py-2 rounded text-xs font-bold transition-all border text-center {{ app()->getLocale() === 'km' ? 'bg-titan-red text-white border-titan-red' : 'bg-white text-titan-navy border-gray-200' }}">
                        ខ្មែរ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- SEARCH MODAL -->
    <div x-show="isSearchOpen" style="display:none"
        class="fixed inset-0 z-[200] flex items-start justify-center p-4 pt-[10vh]">
        <div x-show="isSearchOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="isSearchOpen = false"
            class="absolute inset-0 bg-titan-navy/80 backdrop-blur-sm"></div>
        <div x-show="isSearchOpen" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
            class="relative w-full max-w-2xl bg-white shadow-2xl overflow-hidden rounded">
            <!-- Search Input -->
            <div class="relative">
                <x-lucide-search class="absolute left-5 top-1/2 -translate-y-1/2 text-titan-navy/30 w-5 h-5" />
                <input type="text" x-model="searchQuery"
                    @keydown.enter="if(searchQuery.trim()) window.location.href='/projects?search=' + encodeURIComponent(searchQuery.trim())"
                    @keydown.escape="isSearchOpen = false" x-ref="searchInput"
                    x-effect="if(isSearchOpen) $nextTick(() => $refs.searchInput?.focus())"
                    placeholder="{{ __('Search projects, services...') }}"
                    class="w-full bg-transparent pl-14 pr-24 py-5 text-lg font-medium text-titan-navy outline-none placeholder:text-titan-navy/30 border-b border-gray-100" />
                <button @click="isSearchOpen = false"
                    class="absolute right-4 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs font-bold uppercase tracking-widest text-titan-navy/50 hover:text-titan-red transition-colors bg-gray-100 rounded">ESC</button>
            </div>
            <!-- Quick Links -->
            <div class="p-5">
                <p
                    class="{{ app()->getLocale() === 'km' ? 'font-khmer text-xs text-titan-navy/60' : 'text-xs font-bold text-titan-navy/40 uppercase tracking-widest' }} mb-4">
                    {{ __('Quick Links') }}
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="/projects"
                        class="flex items-center gap-3 px-4 py-3 rounded bg-gray-50 hover:bg-titan-red hover:text-white text-titan-navy font-medium transition-all group">
                        <span class="text-lg">🏗️</span>
                        <span class="text-sm">{{ __('Projects') }}</span>
                        <x-lucide-arrow-right
                            class="w-3.5 h-3.5 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                    </a>
                    <a href="/services"
                        class="flex items-center gap-3 px-4 py-3 rounded bg-gray-50 hover:bg-titan-red hover:text-white text-titan-navy font-medium transition-all group">
                        <span class="text-lg">⚙️</span>
                        <span class="text-sm">{{ __('Services') }}</span>
                        <x-lucide-arrow-right
                            class="w-3.5 h-3.5 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                    </a>
                    <a href="/about"
                        class="flex items-center gap-3 px-4 py-3 rounded bg-gray-50 hover:bg-titan-red hover:text-white text-titan-navy font-medium transition-all group">
                        <span class="text-lg">🏢</span>
                        <span class="text-sm">{{ __('About Us') }}</span>
                        <x-lucide-arrow-right
                            class="w-3.5 h-3.5 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                    </a>
                    <a href="/contact"
                        class="flex items-center gap-3 px-4 py-3 rounded bg-gray-50 hover:bg-titan-red hover:text-white text-titan-navy font-medium transition-all group">
                        <span class="text-lg">📞</span>
                        <span class="text-sm">{{ __('Contact') }}</span>
                        <x-lucide-arrow-right
                            class="w-3.5 h-3.5 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                    </a>
                </div>
            </div>
            <!-- Categories -->
            <div class="px-5 pb-5">
                <p
                    class="{{ app()->getLocale() === 'km' ? 'font-khmer text-xs text-titan-navy/60' : 'text-xs font-bold text-titan-navy/40 uppercase tracking-widest' }} mb-3">
                    {{ __('Categories') }}
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Commercial', 'Infrastructure', 'Industrial', 'Construction', 'Government'] as $tag)
                        <a href="/projects?search={{ $tag }}"
                            class="px-4 py-2 bg-titan-navy/5 text-titan-navy text-xs font-bold uppercase rounded cursor-pointer hover:bg-titan-red hover:text-white transition-all">
                            {{ __($tag) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</header>
