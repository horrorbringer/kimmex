@php
    $profile = $globalSettings['profile'] ?? [];
    $lang = $siteLocale;
    $brand = $globalSettings['brand'] ?? [];

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
    $tiktok = $profile['tiktok'] ?? null;

    $googleMapsUrl = $profile['google_maps_url'] ?? '';
    $isEmbed = str_contains($googleMapsUrl, '/maps/embed') || str_contains($googleMapsUrl, 'google.com/maps?pb=');
    $googleMapsLink = (!empty($googleMapsUrl) && !$isEmbed) ? $googleMapsUrl : "https://www.google.com/maps/search/?api=1&query=" . urlencode($address);

    $logo = (! empty($profile['logo_footer'])) ? $profile['logo_footer'] : ($profile['logo'] ?? null);
    $logoUrl = \App\Support\PublicStorage::urlIfExists($logo, '/logo.png');

    $footerServices = \Illuminate\Support\Facades\Cache::remember('nav_services_'.$lang, now()->addHours(12), function() use ($lang) {
        return \App\Models\Service::where('isActive', true)
            ->orderBy('orderIndex')
            ->orderBy('id')
            ->get()
            ->map(fn($svc) => ['slug' => $svc->slug, 'title' => $svc->getTranslation('title', $lang)])
            ->values()
            ->take(6)
            ->all();
    });
@endphp


<footer id="site-footer">
    <div class="ft-inner">

        <!-- ═══ ROW 1: Main content ═══ -->
        <div class="ft-grid">

            <!-- Brand Column -->
            <div class="ft-brand">
                <img src="{{ $logoUrl }}" alt="{{ $companyName }}" class="ft-logo" loading="lazy" decoding="async" />
                <p class="ft-desc">
                    {{ \Illuminate\Support\Str::limit($brand['company_story'] ?? __('Over 25 years of excellence in building Cambodia\'s future. We deliver high-quality infrastructure and construction solutions.'), 160) }}
                </p>
                <div class="ft-socials">
                    @if($facebook && $facebook !== '#')
                        <a href="{{ $facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><x-social-icon network="facebook" class="w-4 h-4" /></a>
                    @endif
                    @if($telegram && $telegram !== '#')
                        <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" aria-label="Telegram"><x-social-icon network="telegram" class="w-4 h-4" /></a>
                    @endif
                    @if($linkedin && $linkedin !== '#')
                        <a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><x-social-icon network="linkedin" class="w-4 h-4" /></a>
                    @endif
                    @if($youtube && $youtube !== '#')
                        <a href="{{ $youtube }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><x-social-icon network="youtube" class="w-4 h-4" /></a>
                    @endif
                    @if($instagram && $instagram !== '#')
                        <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><x-social-icon network="instagram" class="w-4 h-4" /></a>
                    @endif
                    @if($tiktok && $tiktok !== '#')
                        <a href="{{ $tiktok }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><x-social-icon network="tiktok" class="w-4 h-4" /></a>
                    @endif
                </div>
            </div>

            <!-- Links Column -->
            <div class="ft-col">
                <h4 class="ft-title">{{ __('Company') }}</h4>
                <ul class="ft-links">
                    <li><a href="/about">{{ __('About Us') }}</a></li>
                    <li><a href="/projects">{{ __('Projects') }}</a></li>
                    <li><a href="/news">{{ __('News') }}</a></li>
                    <li><a href="/careers">{{ __('Careers') }}</a></li>
                    <li><a href="/contact">{{ __('Contact') }}</a></li>
                </ul>
            </div>

            <!-- Services Column -->
            <div class="ft-col">
                <h4 class="ft-title">{{ __('Services') }}</h4>
                <ul class="ft-links">
                    @foreach($footerServices as $fs)
                        <li><a href="/services/{{ $fs['slug'] }}">{{ $fs['title'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="ft-col">
                <h4 class="ft-title">{{ __('Contact') }}</h4>
                <div class="ft-contact">
                    <a href="{{ $googleMapsLink }}" target="_blank" rel="noopener noreferrer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>{{ $address }}</span>
                    </a>
                    <a href="tel:{{ str_replace(' ', '', $phone) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span>{{ $phone }}</span>
                    </a>
                    <a href="mailto:{{ $email }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <span>{{ $email }}</span>
                    </a>
                </div>
                <div class="ft-hours">
                    <span class="ft-hours-label">{{ __('Office Hours') }}</span>
                    <span>{{ __('Mon-Fri') }}: 8:00 - 17:30</span>
                </div>
            </div>
        </div>


        <!-- ═══ ROW 2: Newsletter ═══ -->
        <div class="ft-newsletter">
            <div class="ft-newsletter-text">
                <h4>{{ __('Subscribe to Our Newsletter') }}</h4>
                <p>{{ __('Get the latest project updates and company news.') }}</p>
            </div>
            <div class="ft-newsletter-form">
                <livewire:subscribe-form />
            </div>
        </div>

        <!-- ═══ ROW 3: Bottom bar ═══ -->
        <div class="ft-bottom">
            <p>&copy; {{ date('Y') }} {{ $companyName }}. {{ __('All rights reserved') }}.</p>
            <div class="ft-bottom-links">
                <a href="/privacy-policy">{{ __('Privacy Policy') }}</a>
                <a href="/sitemap.xml">{{ __('Sitemap') }}</a>
            </div>
        </div>
    </div>


    <style>
        /* ══════════════════════════════════════════════════════════════
           FOOTER — ID-scoped styles (highest CSS specificity)
           Guarantees correct colors regardless of theme overrides.
           
           Palette:
           • BG:      #071A33 (deep navy — trust, stability)
           • Text:    #ffffff at 90/60/40% (clear hierarchy)
           • Accent:  var(--footer-accent) / #E31E24 (hover only)
           • Surface: rgba(255,255,255,0.04) (subtle depth)
        ══════════════════════════════════════════════════════════════ */

        #site-footer {
            background: var(--footer-bg, #071A33);
            position: relative;
            overflow: hidden;
        }
        #site-footer::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--footer-accent, #E31E24), transparent 50%);
        }

        /* Reset ALL inherited colors inside footer */
        #site-footer,
        #site-footer *:not(svg):not(path):not(circle):not(rect):not(img) {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            border-color: rgba(255,255,255,0.08);
        }

        #site-footer .ft-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 1.25rem;
        }

        /* ── Grid ── */
        #site-footer .ft-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 640px) {
            #site-footer .ft-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (min-width: 1024px) {
            #site-footer .ft-grid {
                grid-template-columns: 2fr 1fr 1fr 1.4fr;
                gap: 2rem;
            }
        }

        /* ── Brand column ── */
        #site-footer .ft-logo {
            height: 42px;
            width: auto;
            margin-bottom: 1rem;
        }
        #site-footer .ft-desc {
            font-size: 0.8125rem;
            line-height: 1.75;
            color: rgba(255,255,255,0.5);
            margin-bottom: 1.5rem;
        }

        /* ── Social icons ── */
        #site-footer .ft-socials {
            display: flex;
            gap: 0.5rem;
        }
        #site-footer .ft-socials a {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.7);
            transition: all 0.3s ease;
        }
        #site-footer .ft-socials a:hover {
            background: var(--footer-accent, #E31E24);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(227,30,36,0.3);
        }

        /* ── Column titles ── */
        #site-footer .ft-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: rgba(255,255,255,0.9);
            margin-bottom: 0.85rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        /* ── Link lists ── */
        #site-footer .ft-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        #site-footer .ft-links li {
            margin-bottom: 0.5rem;
        }
        #site-footer .ft-links a {
            font-size: 0.8125rem;
            color: rgba(255,255,255,0.5);
            transition: color 0.2s, padding-left 0.2s;
            display: inline-block;
        }
        #site-footer .ft-links a:hover {
            color: var(--footer-accent, #E31E24);
            padding-left: 4px;
        }

        /* ── Contact items ── */
        #site-footer .ft-contact {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }
        #site-footer .ft-contact a {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            font-size: 0.8125rem;
            color: rgba(255,255,255,0.55);
            transition: color 0.2s;
        }
        #site-footer .ft-contact a:hover {
            color: var(--footer-accent, #E31E24);
        }
        #site-footer .ft-contact svg {
            flex-shrink: 0;
            margin-top: 2px;
            color: var(--footer-accent, #E31E24);
            opacity: 0.8;
        }

        /* ── Working hours ── */
        #site-footer .ft-hours {
            margin-top: 0.85rem;
            padding: 0.65rem 0.8rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.45);
        }
        #site-footer .ft-hours-label {
            font-weight: 700;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.7);
            margin-bottom: 0.15rem;
        }
    </style>


    <style>
        /* ── Newsletter row ── */
        #site-footer .ft-newsletter {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1.75rem;
            padding: 1.25rem 0;
            border-top: 1px solid rgba(255,255,255,0.07);
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        @media (min-width: 768px) {
            #site-footer .ft-newsletter {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        #site-footer .ft-newsletter h4 {
            font-size: 0.875rem;
            font-weight: 700;
            color: rgba(255,255,255,0.85);
            margin-bottom: 0.2rem;
        }
        #site-footer .ft-newsletter p {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
        }
        #site-footer .ft-newsletter-form {
            width: 100%;
            max-width: 360px;
        }

        /* ── Bottom bar ── */
        #site-footer .ft-bottom {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            align-items: center;
            padding-top: 1.15rem;
            margin-top: 0;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.3);
        }
        @media (min-width: 640px) {
            #site-footer .ft-bottom {
                flex-direction: row;
                justify-content: space-between;
            }
        }
        #site-footer .ft-bottom-links {
            display: flex;
            gap: 1.5rem;
        }
        #site-footer .ft-bottom-links a {
            color: rgba(255,255,255,0.35);
            font-size: 0.75rem;
            transition: color 0.2s;
        }
        #site-footer .ft-bottom-links a:hover {
            color: var(--footer-accent, #E31E24);
        }
    </style>
</footer>
