<x-layouts.app title="Contact Us" description="Get in touch with Kimmex for your construction and engineering needs.">

    @push('head')
    <script type="application/ld+json">
    {!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => __('Contact'), 'item' => url('/contact')],
    ]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'Kimmex Construction & Investment Co., Ltd',
        'image' => url('/logo.png'),
        'url' => url('/'),
        'telephone' => $phone ?? '+855 23 884 604',
        'email' => $email ?? 'info@kimmex.com.kh',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $address ?? 'Phnom Penh',
            'addressLocality' => 'Phnom Penh',
            'addressCountry' => 'KH',
        ],
        'openingHours' => ['Mo-Fr 08:00-17:30', 'Sa 08:00-12:00'],
        'priceRange' => '$$$',
        'areaServed' => ['Cambodia', 'Phnom Penh', 'Siem Reap', 'Battambang'],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endpush

    @php
        $profile = \App\Models\SystemSetting::get('organization_profile', []);
        $lang = app()->getLocale();
        $email = $profile['email'] ?? 'info@kimmex.com.kh';
        $phone = $profile['phone'] ?? '+855 23 999 999';
        $address = $profile[$lang]['address'] ?? ($profile['en']['address'] ?? __('Phnom Penh, Cambodia'));
        $googleMapsUrl = $profile['google_maps_url'] ?? '';
        $originalMapsUrl = $googleMapsUrl;

        $isEmbed = str_contains($googleMapsUrl, '/maps/embed') || str_contains($googleMapsUrl, 'google.com/maps?pb=') || str_contains($googleMapsUrl, 'output=embed');

        if (!$isEmbed && !empty($googleMapsUrl)) {
            $googleMapsUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.667785689154!2d104.89350269999998!3d11.575656499999992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31095176fe4b5e51%3A0x844dbeef5ee9d25b!2sKim%20mex%20Construction%20%26%20Investment%20Co.%2Cltd!5e0!3m2!1skm!2skh!4v1775701743611!5m2!1skm!2skh';
        } elseif (empty($googleMapsUrl)) {
            $googleMapsUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.667785689154!2d104.89350269999998!3d11.575656499999992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31095176fe4b5e51%3A0x844dbeef5ee9d25b!2sKim%20mex%20Construction%20%26%20Investment%20Co.%2Cltd!5e0!3m2!1skm!2skh!4v1775701743611!5m2!1skm!2skh';
        }

        $googleMapsLink = !empty($originalMapsUrl) && !$isEmbed ? $originalMapsUrl : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address);

        $facebook = $profile['facebook'] ?? '#';
        $linkedin = $profile['linkedin'] ?? '#';
        $youtube = $profile['youtube'] ?? '#';
        $instagram = $profile['instagram'] ?? '#';
        $telegram = $profile['telegram'] ?? '#';
        $tiktok = $profile['tiktok'] ?? '#';
        $workingHours = $profile[$lang]['working_hours'] ?? ($profile['en']['working_hours'] ?? 'Mon - Fri: 8:00 AM - 5:00 PM');
    @endphp


    <div class="bg-gray-50 min-h-screen">

        <!-- ═══ HERO ═══ -->
        <section class="relative h-[400px] md:h-[450px] flex items-end overflow-hidden" style="background: #0B2B5C;">
            <div class="absolute inset-0">
                <img src="/images/webp/projects/Thumbnail-3.webp" alt="{{ __('Contact Us') }}" class="w-full h-full object-cover opacity-35" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#071A33]/95 via-[#0B2B5C]/50 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-[#071A33]/60 via-transparent to-transparent"></div>
            </div>
            <div class="relative z-10 w-full max-w-[1280px] mx-auto px-6 pb-14 md:pb-18">
                <nav class="flex items-center gap-2 text-xs mb-6" style="color: rgba(255,255,255,0.5);">
                    <a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <span style="color: rgba(255,255,255,0.9);">{{ __('Contact') }}</span>
                </nav>
                <h1 class="font-heading font-[900] uppercase leading-[1] tracking-tight mb-4"
                    style="font-size: clamp(2rem, 5vw, 3.2rem); color: #FFFFFF;">
                    {{ __('Get In') }} <span style="color: var(--primary-color, #E31E24);">{{ __('Touch') }}</span>
                </h1>
                <p class="max-w-lg leading-relaxed" style="color: rgba(255,255,255,0.6); font-size: 1rem;">
                    {{ __('Have a project in mind? We\'d love to hear from you. Our team responds within 24 hours.') }}
                </p>
            </div>
        </section>


        <!-- ═══ CONTACT CARDS ═══ -->
        <section class="relative z-20 -mt-10">
            <div class="max-w-[1280px] mx-auto px-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="{{ $googleMapsLink }}" target="_blank"
                        class="group bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg hover:border-gray-200 transition-all duration-300">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-4"
                             style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                            <x-lucide-map-pin class="w-5 h-5" style="color: var(--primary-color, #E31E24);" />
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ __('Visit Us') }}</p>
                        <p class="text-sm font-semibold text-gray-700 group-hover:text-titan-red transition-colors leading-relaxed">{{ $address }}</p>
                    </a>
                    <a href="tel:{{ str_replace(' ', '', $phone) }}"
                        class="group bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg hover:border-gray-200 transition-all duration-300">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-4"
                             style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                            <x-lucide-phone class="w-5 h-5" style="color: var(--primary-color, #E31E24);" />
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ __('Call Us') }}</p>
                        <p class="text-sm font-semibold text-gray-700 group-hover:text-titan-red transition-colors">{{ $phone }}</p>
                    </a>
                    <a href="mailto:{{ $email }}"
                        class="group bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg hover:border-gray-200 transition-all duration-300">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-4"
                             style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                            <x-lucide-mail class="w-5 h-5" style="color: var(--primary-color, #E31E24);" />
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ __('Email Us') }}</p>
                        <p class="text-sm font-semibold text-gray-700 group-hover:text-titan-red transition-colors">{{ $email }}</p>
                    </a>
                </div>
            </div>
        </section>


        <!-- ═══ FORM + MAP ═══ -->
        <section class="py-16 md:py-20">
            <div class="max-w-[1280px] mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-10">

                    <!-- FORM (3/5) -->
                    <div class="lg:col-span-3">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="px-6 md:px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                     style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                                    <x-lucide-message-square class="w-4 h-4" style="color: var(--primary-color, #E31E24);" />
                                </div>
                                <div>
                                    <h2 class="text-sm font-bold text-gray-900">{{ __('Send Us a Message') }}</h2>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ __('We\'ll get back to you within 24 hours.') }}</p>
                                </div>
                            </div>

                            <div class="px-6 md:px-8 py-6 md:py-8">
                                @if(session('success'))
                                    <div class="flex items-center gap-2 bg-green-50 border border-green-100 text-green-700 rounded-lg p-3 mb-6 text-sm font-medium">
                                        <x-lucide-check-circle class="w-4 h-4 text-green-500 shrink-0" />
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <form action="{{ route('contact.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-5" x-data="{ submitting: false }" x-on:submit="submitting = true">
                                    @csrf

                                    <!-- Name -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('First Name') }} <span class="text-red-500">*</span></label>
                                            <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                                class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition @error('first_name') border-red-300 bg-red-50 @enderror"
                                                placeholder="{{ __('First name') }}" />
                                            @error('first_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Last Name') }} <span class="text-red-500">*</span></label>
                                            <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                                class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition @error('last_name') border-red-300 bg-red-50 @enderror"
                                                placeholder="{{ __('Last name') }}" />
                                            @error('last_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Email Address') }} <span class="text-red-500">*</span></label>
                                        <input type="email" name="email" value="{{ old('email') }}" required
                                            class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition @error('email') border-red-300 bg-red-50 @enderror"
                                            placeholder="you@company.com" />
                                        @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                    </div>

                                    <!-- Phone + Subject -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Phone') }} <span class="text-gray-300 font-normal">({{ __('optional') }})</span></label>
                                            <input type="tel" name="phone" value="{{ old('phone') }}" inputmode="tel"
                                                class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition"
                                                placeholder="+855 12 345 678" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Subject') }} <span class="text-gray-300 font-normal">({{ __('optional') }})</span></label>
                                            <input type="text" name="subject" value="{{ old('subject') }}"
                                                class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition"
                                                placeholder="{{ __('Project inquiry') }}" />
                                        </div>
                                    </div>

                                    <!-- Message -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Message') }} <span class="text-red-500">*</span></label>
                                        <textarea name="message" required rows="5"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition resize-none @error('message') border-red-300 bg-red-50 @enderror"
                                            placeholder="{{ __('Tell us about your project or how we can help...') }}">{{ old('message') }}</textarea>
                                        @error('message')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                    </div>

                                    <!-- Attachment -->
                                    <div x-data="{ fileName: '' }">
                                        <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Attachment') }} <span class="text-gray-300 font-normal">({{ __('optional') }})</span></label>
                                        <div class="relative h-11 rounded-xl border-2 border-dashed transition-all cursor-pointer overflow-hidden"
                                            :class="fileName ? 'border-green-300 bg-green-50' : 'border-gray-200 hover:border-gray-300 bg-gray-50'">
                                            <input type="file" name="attachment" accept=".pdf,.doc,.docx,.jpg,.png"
                                                class="absolute inset-0 opacity-0 cursor-pointer z-10"
                                                @change="fileName = $event.target.files[0]?.name || ''" />
                                            <div class="flex items-center justify-between h-full px-4">
                                                <span class="text-sm truncate"
                                                    :class="fileName ? 'text-green-700 font-medium' : 'text-gray-400'"
                                                    x-text="fileName || '{{ __('PDF, DOCX, JPG, PNG — max 5 MB') }}'"></span>
                                                <template x-if="!fileName"><x-lucide-paperclip class="w-4 h-4 text-gray-300 shrink-0" /></template>
                                                <template x-if="fileName"><x-lucide-file-check class="w-4 h-4 text-green-500 shrink-0" /></template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit -->
                                    <div class="flex items-center justify-between pt-3">
                                        <p class="text-xs text-gray-400">* {{ __('required fields') }}</p>
                                        <button type="submit"
                                            x-bind:disabled="submitting"
                                            class="inline-flex items-center gap-2 h-11 px-7 rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 group disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-md"
                                            style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                                            <span x-show="!submitting">{{ __('Send Message') }}</span>
                                            <span x-show="submitting" class="inline-flex items-center gap-2">
                                                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                {{ __('Sending...') }}
                                            </span>
                                            <x-lucide-send x-show="!submitting" class="w-4 h-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- SIDEBAR (2/5) -->
                    <div class="lg:col-span-2 space-y-5">

                        <!-- Map -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="h-[220px] md:h-[240px] relative">
                                <iframe src="{{ $googleMapsUrl }}" width="100%" height="100%" style="border:0;"
                                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                            <div class="p-5">
                                <a href="{{ $googleMapsLink }}" target="_blank"
                                    class="flex items-center gap-3 group">
                                    <div class="w-9 h-9 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-gray-100 transition-colors">
                                        <x-lucide-navigation class="w-4 h-4 text-gray-400 group-hover:text-titan-red transition-colors" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-900 group-hover:text-titan-red transition-colors">{{ __('Get Directions') }}</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ __('Open in Google Maps') }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Office Hours -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
                                    <x-lucide-clock class="w-4 h-4 text-gray-400" />
                                </div>
                                <h3 class="text-sm font-bold text-gray-900">{{ __('Office Hours') }}</h3>
                            </div>
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">{{ __('Monday - Friday') }}</span>
                                    <span class="text-sm font-semibold text-gray-700">8:00 - 17:30</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">{{ __('Saturday') }}</span>
                                    <span class="text-sm font-semibold text-gray-700">{{ __('Closed') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">{{ __('Sunday') }}</span>
                                    <span class="text-sm font-semibold text-gray-400">{{ __('Closed') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Social -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-4">{{ __('Follow Us') }}</h3>
                            <div class="flex gap-2.5">
                                @if($facebook && $facebook !== '#')
                                    <a href="{{ $facebook }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-social-facebook flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-social-facebook/30 transition-all" aria-label="Facebook">
                                        <x-social-icon network="facebook" class="w-4 h-4" />
                                    </a>
                                @endif
                                @if($telegram && $telegram !== '#')
                                    <a href="{{ $telegram }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-social-telegram flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-social-telegram/30 transition-all" aria-label="Telegram">
                                        <x-social-icon network="telegram" class="w-4 h-4" />
                                    </a>
                                @endif
                                @if($linkedin && $linkedin !== '#')
                                    <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-social-linkedin flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-social-linkedin/30 transition-all" aria-label="LinkedIn">
                                        <x-social-icon network="linkedin" class="w-4 h-4" />
                                    </a>
                                @endif
                                @if($youtube && $youtube !== '#')
                                    <a href="{{ $youtube }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-social-youtube flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-social-youtube/30 transition-all" aria-label="YouTube">
                                        <x-social-icon network="youtube" class="w-4 h-4" />
                                    </a>
                                @endif
                                @if($instagram && $instagram !== '#')
                                    <a href="{{ $instagram }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-social-instagram flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-social-instagram/30 transition-all" aria-label="Instagram">
                                        <x-social-icon network="instagram" class="w-4 h-4" />
                                    </a>
                                @endif
                                @if($tiktok && $tiktok !== '#')
                                    <a href="{{ $tiktok }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl bg-black flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-black/30 transition-all" aria-label="TikTok">
                                        <x-social-icon network="tiktok" class="w-4 h-4" />
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Direct Call -->
                        <a href="tel:{{ str_replace(' ', '', $phone) }}"
                            class="group flex items-center justify-center gap-3 w-full h-12 rounded-2xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5"
                            style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                            <x-lucide-phone class="w-4 h-4" />
                            {{ __('Call Us Now') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

</x-layouts.app>
