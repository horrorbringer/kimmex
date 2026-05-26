<footer style="background-color: var(--footer-bg); color: #fff;" class="pt-24 pb-12 relative overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16 lg:gap-8 mb-16">
            @php
                $profile = $globalSettings['profile'] ?? [];
                $lang = $siteLocale;
                $isKm = $lang === 'km';
                $brand = $globalSettings['brand'] ?? [];

                // Helper to get translated field or fallback to English
                $getVal = function ($field, $default) use ($profile, $lang) {
                    if (isset($profile[$field]) && is_array($profile[$field])) {
                        return $profile[$field][$lang] ?? $profile[$field]['en'] ?? $default;
                    }
                    return $profile[$field] ?? $default;
                };

                $companyName = $getVal('company_name', 'KIMMEX');
                $address = $getVal('address', __('Phnom Penh, Cambodia'));
                $email = $profile['email'] ?? 'info@kimmex.com.kh';
                $phone = $profile['phone'] ?? '+855 23 999 999';
                $facebook = $profile['facebook'] ?? null;
                $linkedin = $profile['linkedin'] ?? null;
                $youtube = $profile['youtube'] ?? null;
                $instagram = $profile['instagram'] ?? null;
                $telegram = $profile['telegram'] ?? null;

                $googleMapsUrl = $profile['google_maps_url'] ?? '';
                $isEmbed = str_contains($googleMapsUrl, '/maps/embed') || str_contains($googleMapsUrl, 'google.com/maps?pb=');
                $googleMapsLink = (!empty($googleMapsUrl) && !$isEmbed) ? $googleMapsUrl : "https://www.google.com/maps/search/?api=1&query=" . urlencode($address);

                $logo = $profile['logo'] ?? null;
                $logoUrl = $logo ? (\Illuminate\Support\Str::startsWith($logo, 'http') ? $logo : \Illuminate\Support\Facades\Storage::url($logo)) : '/logo.png';
                $tagline = $brand['tagline'] ?? $profile['en']['tagline'] ?? __('Construction & Investment');
            @endphp

            <!-- Column 1: Brand -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <img src="{{ $logoUrl }}" alt="{{ $companyName }}" class="h-12 w-auto object-contain" />
                </div>
                <p class="text-white/50 text-sm leading-relaxed max-w-xs">
                    {{ \Illuminate\Support\Str::limit($brand['company_story'] ?? __('Over 25 years of excellence in building the future of Cambodia.'), 120) }}
                </p>
                <div class="flex gap-3">
                    @if($facebook && $facebook !== '#')
                        <a href="{{ $facebook }}" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded bg-social-facebook flex items-center justify-center hover:brightness-110 transition-all text-white shadow-lg shadow-social-facebook/20">
                            <x-lucide-facebook class="w-4 h-4" />
                        </a>
                    @endif
                    @if($linkedin && $linkedin !== '#')
                        <a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded bg-social-linkedin flex items-center justify-center hover:brightness-110 transition-all text-white shadow-lg shadow-social-linkedin/20">
                            <x-lucide-linkedin class="w-4 h-4" />
                        </a>
                    @endif
                    @if($youtube && $youtube !== '#')
                        <a href="{{ $youtube }}" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded bg-social-youtube flex items-center justify-center hover:brightness-110 transition-all text-white shadow-lg shadow-social-youtube/20">
                            <x-lucide-youtube class="w-4 h-4" />
                        </a>
                    @endif
                    @if($instagram && $instagram !== '#')
                        <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded bg-social-instagram flex items-center justify-center hover:brightness-110 transition-all text-white shadow-lg shadow-social-instagram/20">
                            <x-lucide-instagram class="w-4 h-4" />
                        </a>
                    @endif
                    @if($telegram && $telegram !== '#')
                        <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded bg-social-telegram flex items-center justify-center hover:brightness-110 transition-all text-white shadow-lg shadow-social-telegram/20">
                            <x-lucide-send class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h4 style="color: var(--footer-accent);" class="font-bold text-sm uppercase tracking-widest mb-8 flex items-center gap-2">
                    <x-lucide-hard-hat class="w-3.5 h-3.5" />
                    {{ __('Explore') }}
                </h4>
                <ul class="space-y-4 text-sm text-white/50">
                    <li><a href="/projects"
                            style="--footer-hover: var(--footer-accent);"
                            class="footer-link hover:pl-2 transition-all flex items-center gap-2">{{ __('Projects') }}</a>
                    </li>
                    <li><a href="/services"
                            class="footer-link hover:pl-2 transition-all flex items-center gap-2">{{ __('Services') }}</a>
                    </li>
                    <li><a href="/about"
                            class="footer-link hover:pl-2 transition-all flex items-center gap-2">{{ __('About Us') }}</a>
                    </li>
                    <li><a href="/careers"
                            class="footer-link hover:pl-2 transition-all flex items-center gap-2">{{ __('Careers') }}</a>
                    </li>
                    <li><a href="/news"
                            class="footer-link hover:pl-2 transition-all flex items-center gap-2">{{ __('News & Insights') }}</a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Services -->
            <div>
                <h4 style="color: var(--footer-accent);" class="font-bold text-sm uppercase tracking-widest mb-8 flex items-center gap-2">
                    <x-lucide-hard-hat class="w-3.5 h-3.5" />
                    {{ __('Services') }}
                </h4>
                @php
                    $footerServices = \Illuminate\Support\Facades\Cache::remember('footer_services_'.app()->getLocale(), now()->addHours(12), function() {
                        return \App\Models\Service::where('isActive', true)
                            ->get()
                            ->map(fn($svc) => [
                                'slug' => $svc->slug,
                                'title' => $svc->getTranslation('title', app()->getLocale())
                            ])
                            ->all();
                    });
                @endphp
                <ul class="space-y-4 text-sm text-white/50">
                    @foreach($footerServices as $fs)
                        <li><a href="/services/{{ $fs['slug'] }}"
                                class="footer-link flex items-center gap-2 transition-all group">
                                <span style="background-color: var(--footer-accent);" class="w-1.5 h-1.5 rounded-full group-hover:scale-125 transition-transform shrink-0"></span>
                                {{ $fs['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Column 4: Contact -->
            <div>
                <h4 style="color: var(--footer-accent);" class="font-bold text-sm uppercase tracking-widest mb-8 flex items-center gap-2">
                    <x-lucide-hard-hat class="w-3.5 h-3.5" />
                    {{ __('Contact') }}
                </h4>
                <ul class="space-y-3 text-sm text-white/50">
                    <li class="flex gap-4 rounded border border-white/10 bg-white/5 p-4">
                        <x-lucide-map-pin style="color: var(--footer-accent);" class="shrink-0 w-5 h-5" />
                        <a href="{{ $googleMapsLink }}" target="_blank" rel="noopener noreferrer"
                            class="footer-link transition-colors">
                            {{ $address }}
                        </a>
                    </li>
                    <li class="flex gap-4 items-center rounded border border-white/10 bg-white/5 p-4">
                        <x-lucide-phone style="color: var(--footer-accent);" class="shrink-0 w-5 h-5" />
                        <a href="tel:{{ str_replace(' ', '', $phone) }}"
                            class="footer-link transition-colors">
                            {{ $phone }}
                        </a>
                    </li>
                    <li class="flex gap-4 items-center rounded border border-white/10 bg-white/5 p-4">
                        <x-lucide-mail style="color: var(--footer-accent);" class="shrink-0 w-5 h-5" />
                        <a href="mailto:{{ $email }}" class="footer-link transition-colors">
                            {{ $email }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-white/40">
            <p>&copy; 2026 Kimmex Construction &amp; Investment Co., Ltd. {{ __('All rights reserved') }}.</p>
            <div class="flex gap-6">
                <a href="/privacy-policy" class="footer-link transition-colors">{{ __('Privacy Policy') }}</a>
                <a href="#" class="footer-link transition-colors">{{ __('Terms of Service') }}</a>
            </div>
        </div>
    </div>

    {{-- Scoped footer styles — completely isolated, no global brand variables affected --}}
    <style>
        footer a.footer-link:hover {
            color: var(--footer-accent) !important;
        }
    </style>
</footer>
