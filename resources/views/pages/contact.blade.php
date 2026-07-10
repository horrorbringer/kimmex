<x-layouts.app title="Contact Us" description="Get in touch with Kimmex for your construction and engineering needs.">

    <div class="bg-white min-h-screen text-titan-navy">

        <!-- HERO -->
        <section class="relative h-[320px] md:h-[380px] flex items-end overflow-hidden bg-titan-navy">
            <div class="absolute inset-0">
                <img src="/images/webp/projects/Thumbnail-3.webp" alt="Contact Kimmex"
                    class="w-full h-full object-cover opacity-50" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/90 via-titan-navy/40 to-transparent"></div>
            </div>
            <div class="relative z-10 w-full max-w-[1200px] mx-auto px-6 pb-10 md:pb-12">
                <p class="text-[9px] font-black uppercase tracking-[0.35em] text-titan-red mb-2">{{ __('Kimmex') }}</p>
                <h1 class="font-black text-white uppercase leading-none drop-shadow-lg"
                    style="font-size: clamp(1.6rem, 4vw, 2.6rem) !important; color: white !important; font-weight: 900 !important;">
                    {{ __('Get In Touch') }}
                </h1>
                <p class="text-white/50 text-sm mt-2">{{ __('We respond within 24 hours on business days.') }}</p>
            </div>
        </section>

        @php
            $profile = \App\Models\SystemSetting::get('organization_profile', []);
            $lang = app()->getLocale();
            $email = $profile['email'] ?? 'info@kimmex.com.kh';
            $phone = $profile['phone'] ?? '+855 23 999 999';
            $address = $profile[$lang]['address'] ?? ($profile['en']['address'] ?? __('Phnom Penh, Cambodia'));
            $googleMapsUrl = $profile['google_maps_url'] ?? '';
            $originalMapsUrl = $googleMapsUrl;

            // Enhanced detection for embeddable URLs
            $isEmbed =
                str_contains($googleMapsUrl, '/maps/embed') ||
                str_contains($googleMapsUrl, 'google.com/maps?pb=') ||
                str_contains($googleMapsUrl, 'output=embed');

            if (!$isEmbed && !empty($googleMapsUrl)) {
                // If it's not an embed link, we use the official Kimmex Embed fallback
                $googleMapsUrl =
                    'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.667785689154!2d104.89350269999998!3d11.575656499999992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31095176fe4b5e51%3A0x844dbeef5ee9d25b!2sKim%20mex%20Construction%20%26%20Investment%20Co.%2Cltd!5e0!3m2!1skm!2skh!4v1775701743611!5m2!1skm!2skh';
            } elseif (empty($googleMapsUrl)) {
                // Default fallback if nothing provided
                $googleMapsUrl =
                    'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.667785689154!2d104.89350269999998!3d11.575656499999992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31095176fe4b5e51%3A0x844dbeef5ee9d25b!2sKim%20mex%20Construction%20%26%20Investment%20Co.%2Cltd!5e0!3m2!1skm!2skh!4v1775701743611!5m2!1skm!2skh';
            }

            // Clickable link fallback: prioritze user's link if provided and it's NOT an embed link
            $googleMapsLink =
                !empty($originalMapsUrl) && !$isEmbed
                ? $originalMapsUrl
                : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address);

            $facebook = $profile['facebook'] ?? '#';
            $linkedin = $profile['linkedin'] ?? '#';
            $youtube = $profile['youtube'] ?? '#';
            $instagram = $profile['instagram'] ?? '#';
            $telegram = $profile['telegram'] ?? '#';
            $workingHours =
                $profile[$lang]['working_hours'] ?? ($profile['en']['working_hours'] ?? 'Mon - Fri: 8:00 AM - 5:00 PM');
        @endphp

        <!-- CONTACT INFO STRIP -->
        <div class="bg-gray-50 border-b border-gray-100">
            <div class="max-w-[1200px] mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                    <a href="{{ $googleMapsLink }}" target="_blank"
                        class="flex items-center gap-3 py-4 md:py-5 md:pr-8 group">
                        <div class="w-8 h-8 rounded bg-white border border-gray-200 flex items-center justify-center shrink-0 group-hover:border-titan-red/30 transition-colors">
                            <x-lucide-map-pin class="w-3.5 h-3.5 text-titan-red" stroke-width="1.5" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-[9px] font-black uppercase tracking-[0.2em] text-titan-navy/30 mb-0.5">{{ __('Address') }}</div>
                            <div class="text-[11px] font-semibold text-titan-navy/70 group-hover:text-titan-red transition-colors truncate">{{ $address }}</div>
                        </div>
                    </a>
                    <a href="tel:{{ str_replace(' ', '', $phone) }}"
                        class="flex items-center gap-3 py-4 md:py-5 md:px-8 group">
                        <div class="w-8 h-8 rounded bg-white border border-gray-200 flex items-center justify-center shrink-0 group-hover:border-titan-red/30 transition-colors">
                            <x-lucide-phone class="w-3.5 h-3.5 text-titan-red" stroke-width="1.5" />
                        </div>
                        <div>
                            <div class="text-[9px] font-black uppercase tracking-[0.2em] text-titan-navy/30 mb-0.5">{{ __('Phone') }}</div>
                            <div class="text-[11px] font-semibold text-titan-navy/70 group-hover:text-titan-red transition-colors">{{ $phone }}</div>
                        </div>
                    </a>
                    <a href="mailto:{{ $email }}"
                        class="flex items-center gap-3 py-4 md:py-5 md:pl-8 group">
                        <div class="w-8 h-8 rounded bg-white border border-gray-200 flex items-center justify-center shrink-0 group-hover:border-titan-red/30 transition-colors">
                            <x-lucide-mail class="w-3.5 h-3.5 text-titan-red" stroke-width="1.5" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-[9px] font-black uppercase tracking-[0.2em] text-titan-navy/30 mb-0.5">{{ __('Email') }}</div>
                            <div class="text-[11px] font-semibold text-titan-navy/70 group-hover:text-titan-red transition-colors truncate">{{ $email }}</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- FORM + SIDEBAR -->
        <section class="py-10 md:py-14 max-w-[1200px] mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-10">

                <!-- FORM (2/3) -->
                <div class="lg:col-span-2">
                    <div class="bg-white border border-gray-100 rounded-lg p-5 md:p-8">
                        <div class="flex items-center gap-2.5 mb-6">
                            <div class="w-5 h-[2px] bg-titan-red rounded-full"></div>
                            <div>
                                <h2 class="contact-section-title text-[11px] font-black text-titan-navy uppercase tracking-[0.2em]">{{ __('Send a Message') }}</h2>
                                <p class="text-[10px] text-titan-navy/35 mt-0.5">{{ __('We will get back to you within 24 hours.') }}</p>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="flex items-center gap-2 bg-green-50 border border-green-100 text-green-700 rounded p-3 mb-5 text-[11px] font-semibold">
                                <x-lucide-check-circle class="w-4 h-4 text-green-500 shrink-0" />
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">{{ __('First Name') }} <span class="text-titan-red">*</span></label>
                                    <div class="relative">
                                        <x-lucide-user class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/20 pointer-events-none" />
                                        <input type="text" name="first_name" required
                                            class="w-full h-10 pl-9 pr-3 rounded border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all @error('first_name') border-titan-red bg-red-50 @enderror"
                                            placeholder="{{ __('First name') }}" />
                                    </div>
                                    @error('first_name')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">{{ __('Last Name') }} <span class="text-titan-red">*</span></label>
                                    <div class="relative">
                                        <x-lucide-user class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/20 pointer-events-none" />
                                        <input type="text" name="last_name" required
                                            class="w-full h-10 pl-9 pr-3 rounded border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all @error('last_name') border-titan-red bg-red-50 @enderror"
                                            placeholder="{{ __('Last name') }}" />
                                    </div>
                                    @error('last_name')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">{{ __('Email Address') }} <span class="text-titan-red">*</span></label>
                                <div class="relative">
                                    <x-lucide-at-sign class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/20 pointer-events-none" />
                                    <input type="email" name="email" required
                                        class="w-full h-10 pl-9 pr-3 rounded border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all @error('email') border-titan-red bg-red-50 @enderror"
                                        placeholder="you@example.com" />
                                </div>
                                @error('email')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div x-data="{ phoneVal: '', phoneError: '' }">
                                    <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">{{ __('Phone') }}</label>
                                    <div class="relative">
                                        <x-lucide-phone class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/20 pointer-events-none" />
                                        <input type="tel" name="phone" inputmode="tel"
                                            x-model="phoneVal"
                                            @blur="phoneError = phoneVal && !/^\+?[\d\s\-(). ]{7,25}$/.test(phoneVal.trim()) ? '{{ __('Enter a valid number') }}' : ''"
                                            @input="if(phoneError) phoneError = ''"
                                            :class="phoneError ? 'border-titan-red bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-titan-red/40 focus:bg-white'"
                                            class="w-full h-10 pl-9 pr-3 rounded border text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:ring-1 focus:ring-titan-red/10 transition-all"
                                            placeholder="+855 12 345 678" />
                                    </div>
                                    <p x-show="phoneError" x-text="phoneError" class="text-[9px] text-titan-red font-bold mt-1" style="display:none"></p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">{{ __('Subject') }}</label>
                                    <div class="relative">
                                        <x-lucide-file-text class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/20 pointer-events-none" />
                                        <input type="text" name="subject"
                                            class="w-full h-10 pl-9 pr-3 rounded border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all"
                                            placeholder="{{ __('Project discussion') }}" />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">{{ __('Message') }} <span class="text-titan-red">*</span></label>
                                <textarea name="message" required rows="5"
                                    class="w-full px-3 py-2.5 rounded border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all resize-none @error('message') border-titan-red bg-red-50 @enderror"
                                    placeholder="{{ __('Tell us about your project…') }}"></textarea>
                                @error('message')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div x-data="{ fileName: '' }">
                                <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">
                                    {{ __('Attachment') }} <span class="text-titan-navy/20 font-medium normal-case tracking-normal text-[10px]">{{ __('(optional)') }}</span>
                                </label>
                                <div class="relative h-10 rounded border transition-all duration-200 cursor-pointer overflow-hidden"
                                    :class="fileName ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300'">
                                    <input type="file" name="attachment" accept=".pdf,.doc,.docx,.jpg,.png"
                                        class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full"
                                        @change="fileName = $event.target.files[0]?.name || ''" />
                                    <div class="flex items-center justify-between h-full px-3">
                                        <span class="text-[11px] font-semibold truncate"
                                            :class="fileName ? 'text-green-700' : 'text-titan-navy/30'"
                                            x-text="fileName || '{{ __('PDF, DOCX, JPG, PNG — max 5 MB') }}'"></span>
                                        <template x-if="!fileName"><x-lucide-paperclip class="w-3.5 h-3.5 text-titan-navy/20 shrink-0" /></template>
                                        <template x-if="fileName"><x-lucide-file-check class="w-3.5 h-3.5 text-green-500 shrink-0" /></template>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-3 pt-2 border-t border-gray-100">
                                <p class="text-[9px] text-titan-navy/25">* {{ __('required fields') }}</p>
                                <button type="submit"
                                    class="inline-flex items-center gap-2 h-9 px-6 rounded bg-titan-red hover:bg-titan-navy text-white font-black text-[9px] uppercase tracking-[0.2em] transition-all group">
                                    {{ __('Send Message') }}
                                    <x-lucide-send class="w-3.5 h-3.5 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- SIDEBAR (1/3) -->
                <div class="space-y-3 lg:sticky lg:top-28 self-start">

                    <!-- Hours -->
                    <div class="bg-white border border-gray-100 rounded-lg p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <x-lucide-clock class="w-3.5 h-3.5 text-titan-red shrink-0" />
                            <p class="contact-section-title text-[9px] font-black uppercase tracking-[0.25em] text-titan-navy/40">{{ __('Working Hours') }}</p>
                        </div>
                        <p class="text-[11px] font-semibold text-titan-navy/60 leading-relaxed">{{ $workingHours }}</p>
                    </div>

                    <!-- Map preview -->
                    <div class="rounded-lg overflow-hidden border border-gray-100 h-[180px] md:h-[200px] relative group">
                        <iframe src="{{ $googleMapsUrl }}" width="100%" height="100%" style="border:0;"
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        <a href="{{ $googleMapsLink }}" target="_blank"
                            class="absolute bottom-2 right-2 inline-flex items-center gap-1.5 h-7 px-3 rounded bg-white/90 backdrop-blur-sm border border-gray-200 text-[9px] font-black uppercase tracking-[0.15em] text-titan-navy hover:text-titan-red transition-colors shadow-sm">
                            <x-lucide-external-link class="w-3 h-3" />{{ __('Open Map') }}
                        </a>
                    </div>

                    <!-- Social -->
                    <div class="bg-white border border-gray-100 rounded-lg p-5">
                        <p class="contact-section-title text-[9px] font-black uppercase tracking-[0.25em] text-titan-navy/40 mb-3">{{ __('Follow Us') }}</p>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ $facebook }}" target="_blank" rel="noopener"
                                class="w-8 h-8 rounded bg-social-facebook flex items-center justify-center text-white hover:brightness-110 transition-all shadow-sm">
                                <x-social-icon network="facebook" class="w-3.5 h-3.5" />
                            </a>
                            <a href="{{ $linkedin }}" target="_blank" rel="noopener"
                                class="w-8 h-8 rounded bg-social-linkedin flex items-center justify-center text-white hover:brightness-110 transition-all shadow-sm">
                                <x-social-icon network="linkedin" class="w-3.5 h-3.5" />
                            </a>
                            @if($youtube && $youtube !== '#')
                            <a href="{{ $youtube }}" target="_blank" rel="noopener"
                                class="w-8 h-8 rounded bg-social-youtube flex items-center justify-center text-white hover:brightness-110 transition-all shadow-sm">
                                <x-social-icon network="youtube" class="w-3.5 h-3.5" />
                            </a>
                            @endif
                            @if($instagram && $instagram !== '#')
                            <a href="{{ $instagram }}" target="_blank" rel="noopener"
                                class="w-8 h-8 rounded bg-social-instagram flex items-center justify-center text-white hover:brightness-110 transition-all shadow-sm">
                                <x-social-icon network="instagram" class="w-3.5 h-3.5" />
                            </a>
                            @endif
                            @if($telegram && $telegram !== '#')
                            <a href="{{ $telegram }}" target="_blank" rel="noopener"
                                class="w-8 h-8 rounded bg-social-telegram flex items-center justify-center text-white hover:brightness-110 transition-all shadow-sm">
                                <x-social-icon network="telegram" class="w-3.5 h-3.5" />
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Direct CTA -->
                    <a href="tel:{{ str_replace(' ', '', $phone) }}"
                        class="flex items-center justify-center gap-2 h-10 w-full rounded bg-titan-navy text-white text-[9px] font-black uppercase tracking-[0.2em] hover:bg-titan-red transition-colors">
                        <x-lucide-phone class="w-3.5 h-3.5" />{{ __('Call Us Now') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- FULL MAP -->
        <div class="max-w-[1200px] mx-auto px-6 pb-10 md:pb-14">
            <div class="w-full h-[220px] md:h-[280px] rounded-lg overflow-hidden border border-gray-100">
                <iframe src="{{ $googleMapsUrl }}" width="100%" height="100%" style="border:0;"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

        <!-- CTA BANNER -->
        <section class="bg-titan-navy py-10 md:py-12">
            <div class="max-w-[1200px] mx-auto px-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.3em] text-titan-red mb-1">{{ __('Ready?') }}</p>
                    <h3 class="contact-section-title text-base font-black text-white uppercase tracking-tight">{{ __('Start Your Project Today') }}</h3>
                    <p class="text-white/35 text-[11px] mt-1">{{ __('Our expert team is here to help every step of the way.') }}</p>
                </div>
                <a href="tel:{{ str_replace(' ', '', $phone) }}"
                    class="shrink-0 inline-flex items-center gap-2 h-10 px-6 rounded bg-titan-red text-white text-[9px] font-black uppercase tracking-[0.2em] hover:bg-white hover:text-titan-navy transition-all">
                    <x-lucide-phone class="w-3.5 h-3.5" />{{ __('Call Us Now') }}
                </a>
            </div>
        </section>
    </div>

</x-layouts.app>
