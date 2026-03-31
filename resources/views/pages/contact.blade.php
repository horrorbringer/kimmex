<x-layouts.app title="Contact Us" description="Get in touch with Kimmex for your construction and engineering needs.">

    <div class="bg-white min-h-screen text-titan-navy">

        <!-- HERO -->
        <section class="relative z-10 flex items-center justify-center overflow-hidden bg-titan-navy"
            style="min-height: 480px;">
            <div class="absolute inset-0">
                <img src="/images/projects/Thumbnail-3.jpg" alt="Contact"
                    class="w-full h-full object-cover opacity-50" />
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/50 via-titan-navy/30 to-titan-navy/80">
                </div>
            </div>

            <div class="relative z-20 text-center max-w-4xl px-6 pt-[100px]" x-data="{ shown: false }"
                x-init="setTimeout(() => shown = true, 100)">
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-6'"
                    class="transition-all duration-700 delay-100 inline-flex items-center gap-2 px-5 py-2.5 bg-white/5 backdrop-blur-sm rounded-full text-white/80 text-[11px] font-bold uppercase tracking-widest mb-8 border border-white/10">
                    <x-lucide-mail class="w-3.5 h-3.5 text-titan-red" />
                    {{ __('Contact') }}
                </div>

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-700 delay-300 font-black text-white mb-6 leading-none tracking-tighter uppercase"
                    style="font-size: clamp(2.5rem, 7vw, 5.5rem);">
                    {{ __('Get In Touch') }}
                </h1>

                <p :class="shown ? 'opacity-100' : 'opacity-0'"
                    class="transition-all duration-700 delay-500 text-base text-white/40 max-w-xl mx-auto leading-relaxed">
                    {{ __('Have a project in mind? Our team is ready to bring your vision to life.') }}
                </p>
            </div>
        </section>

        @php
            $profile = \App\Models\SystemSetting::get('organization_profile', []);
            $lang = app()->getLocale();
            $email = $profile['email'] ?? 'info@kimmex.com.kh';
            $phone = $profile['phone'] ?? '+855 23 999 999';
            $address = $profile[$lang]['address'] ?? $profile['en']['address'] ?? __('Phnom Penh, Cambodia');
            $googleMapsUrl = $profile['google_maps_url'] ?? '';
            $originalMapsUrl = $googleMapsUrl;

            // Improved fallback for missing or invalid embed URLs
            $isEmbed = str_contains($googleMapsUrl, '/maps/embed') ||
                str_contains($googleMapsUrl, 'google.com/maps?pb=') ||
                str_contains($googleMapsUrl, 'google.com/maps/embed');

            if (!$isEmbed && !empty($googleMapsUrl)) {
                $googleMapsUrl = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.767!2d104.9197!3d11.5563!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTHCsDMzJzIyLjciTiAxMDTCsDU1JzEwLjkiRQ!5e0!3m2!1sen!2skh!4v1234567890";
            } elseif (empty($googleMapsUrl)) {
                $googleMapsUrl = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.767!2d104.9197!3d11.5563!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTHCsDMzJzIyLjciTiAxMDTCsDU1JzEwLjkiRQ!5e0!3m2!1sen!2skh!4v1234567890";
            }

            // Clickable link fallback: prioritze user's link if provided and it's NOT an embed link
            $googleMapsLink = (!empty($originalMapsUrl) && !$isEmbed)
                ? $originalMapsUrl
                : "https://www.google.com/maps/search/?api=1&query=" . urlencode($address);

            $facebook = $profile['facebook'] ?? '#';
            $linkedin = $profile['linkedin'] ?? '#';
            $youtube = $profile['youtube'] ?? '#';
            $instagram = $profile['instagram'] ?? '#';
            $telegram = $profile['telegram'] ?? '#';
            $workingHours = $profile[$lang]['working_hours'] ?? $profile['en']['working_hours'] ?? 'Mon - Fri: 8:00 AM - 5:00 PM';
        @endphp

        <!-- CONTACT INFO BAR -->
        <section class="max-w-[1200px] mx-auto px-6 relative z-40 -mt-12">
            <div
                class="bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-gray-100 grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <a href="{{ $googleMapsLink }}" target="_blank"
                    class="flex items-center gap-4 p-7 group hover:bg-gray-50/50 transition-colors rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none">
                    <div
                        class="w-11 h-11 bg-titan-red/10 text-titan-red rounded-xl flex items-center justify-center shrink-0 group-hover:bg-titan-red group-hover:text-white transition-all duration-300">
                        <x-lucide-map-pin class="w-5 h-5" />
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-titan-navy/30 mb-0.5">
                            {{ __('Address') }}
                        </div>
                        <div class="text-sm font-bold text-titan-navy">{{ $address }}</div>
                    </div>
                </a>
                <a href="tel:{{ str_replace(' ', '', $phone) }}"
                    class="flex items-center gap-4 p-7 group hover:bg-gray-50/50 transition-colors">
                    <div
                        class="w-11 h-11 bg-titan-red/10 text-titan-red rounded-xl flex items-center justify-center shrink-0 group-hover:bg-titan-red group-hover:text-white transition-all duration-300">
                        <x-lucide-phone class="w-5 h-5" />
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-titan-navy/30 mb-0.5">
                            {{ __('Phone') }}
                        </div>
                        <div class="text-sm font-bold text-titan-navy">{{ $phone }}</div>
                    </div>
                </a>
                <a href="mailto:{{ $email }}"
                    class="flex items-center gap-4 p-7 group hover:bg-gray-50/50 transition-colors rounded-b-2xl md:rounded-r-2xl md:rounded-bl-none">
                    <div
                        class="w-11 h-11 bg-titan-red/10 text-titan-red rounded-xl flex items-center justify-center shrink-0 group-hover:bg-titan-red group-hover:text-white transition-all duration-300">
                        <x-lucide-mail class="w-5 h-5" />
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-titan-navy/30 mb-0.5">
                            {{ __('Email') }}
                        </div>
                        <div class="text-sm font-bold text-titan-navy">{{ $email }}</div>
                    </div>
                </a>
            </div>
        </section>

        <!-- FORM + SIDEBAR -->
        <section class="py-24 max-w-[1200px] mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">

                <!-- FORM (2/3) -->
                <div class="lg:col-span-2">
                    <div class="border border-gray-100 rounded-2xl p-8 md:p-12">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-1 h-8 bg-titan-red rounded-full"></div>
                            <div>
                                <h2 class="text-xl font-black text-titan-navy uppercase tracking-tight">
                                    {{ __('Send a Message') }}
                                </h2>
                                <p class="text-titan-navy/35 text-xs mt-0.5">
                                    {{ __('We will get back to you within 24 hours.') }}
                                </p>
                            </div>
                        </div>

                        @if(session('success'))
                            <div
                                class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 text-sm font-semibold border border-green-100 flex items-center gap-2">
                                <x-lucide-check-circle class="w-4 h-4 text-green-500 shrink-0" />
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-titan-navy/40 mb-2">{{ __('First Name') }}
                                        <span class="text-titan-red">*</span></label>
                                    <div class="relative">
                                        <x-lucide-user
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" />
                                        <input type="text" name="first_name" required
                                            class="w-full bg-gray-50 border border-gray-100 rounded-xl pl-11 pr-4 py-3.5 text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/20 focus:border-titan-red/40 focus:bg-white transition-all outline-none placeholder:text-gray-300"
                                            placeholder="{{ __('John') }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-titan-navy/40 mb-2">{{ __('Last Name') }}
                                        <span class="text-titan-red">*</span></label>
                                    <div class="relative">
                                        <x-lucide-user
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" />
                                        <input type="text" name="last_name" required
                                            class="w-full bg-gray-50 border border-gray-100 rounded-xl pl-11 pr-4 py-3.5 text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/20 focus:border-titan-red/40 focus:bg-white transition-all outline-none placeholder:text-gray-300"
                                            placeholder="{{ __('Doe') }}">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-titan-navy/40 mb-2">{{ __('Email Address') }}
                                    <span class="text-titan-red">*</span></label>
                                <div class="relative">
                                    <x-lucide-at-sign
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" />
                                    <input type="email" name="email" required
                                        class="w-full bg-gray-50 border border-gray-100 rounded-xl pl-11 pr-4 py-3.5 text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/20 focus:border-titan-red/40 focus:bg-white transition-all outline-none placeholder:text-gray-300"
                                        placeholder="{{ __('email@example.com') }}">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-titan-navy/40 mb-2">{{ __('Phone') }}</label>
                                    <div class="relative">
                                        <x-lucide-phone
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" />
                                        <input type="text" name="phone"
                                            class="w-full bg-gray-50 border border-gray-100 rounded-xl pl-11 pr-4 py-3.5 text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/20 focus:border-titan-red/40 focus:bg-white transition-all outline-none placeholder:text-gray-300"
                                            placeholder="+855 12 345 678">
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-titan-navy/40 mb-2">{{ __('Subject') }}</label>
                                    <div class="relative">
                                        <x-lucide-file-text
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" />
                                        <input type="text" name="subject"
                                            class="w-full bg-gray-50 border border-gray-100 rounded-xl pl-11 pr-4 py-3.5 text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/20 focus:border-titan-red/40 focus:bg-white transition-all outline-none placeholder:text-gray-300"
                                            placeholder="{{ __('Project discussion') }}">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-titan-navy/40 mb-2">{{ __('Message') }} <span
                                        class="text-titan-red">*</span></label>
                                <textarea name="message" required rows="5"
                                    class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3.5 text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/20 focus:border-titan-red/40 focus:bg-white transition-all outline-none resize-none placeholder:text-gray-300"
                                    placeholder="{{ __('Tell us about the details...') }}"></textarea>
                            </div>
                            <div class="flex items-center justify-between pt-2">
                                <p class="text-[11px] text-titan-navy/25 hidden sm:block">
                                    {{ __('All fields marked with * are required') }}
                                </p>
                                <button type="submit"
                                    class="bg-titan-red hover:bg-titan-navy text-white px-8 py-4 rounded-xl font-bold text-sm uppercase tracking-widest transition-all duration-300 flex items-center gap-3 shadow-md hover:shadow-lg group">
                                    {{ __('Send Message') }}
                                    <x-lucide-send
                                        class="w-4 h-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- SIDEBAR (1/3) -->
                <div class="lg:sticky lg:top-32 space-y-6 self-start">
                    <!-- Hours -->
                    <div class="bg-titan-navy text-white rounded-2xl p-8 relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-titan-red/10 rounded-full blur-[60px] pointer-events-none">
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-6">
                                <x-lucide-clock class="w-4 h-4 text-titan-red" />
                                <h3 class="text-xs font-black uppercase tracking-widest text-white/70">
                                    {{ __('Working Hours') }}
                                </h3>
                            </div>
                            <div class="space-y-3 text-sm">
                                <div class="text-white/80 font-medium leading-relaxed">
                                    {{ $workingHours }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social -->
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <div class="flex items-center gap-3 mb-5">
                            <x-lucide-share-2 class="w-4 h-4 text-titan-red" />
                            <h3 class="text-xs font-black uppercase tracking-widest text-titan-navy/40">
                                {{ __('Follow Us') }}
                            </h3>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ $facebook }}" target="_blank"
                                class="w-11 h-11 rounded-xl bg-white text-titan-navy flex items-center justify-center hover:bg-titan-red hover:text-white transition-all duration-300 shadow-sm border border-gray-100"><x-lucide-facebook
                                    class="w-4 h-4" /></a>
                            <a href="{{ $linkedin }}" target="_blank"
                                class="w-11 h-11 rounded-xl bg-white text-titan-navy flex items-center justify-center hover:bg-titan-red hover:text-white transition-all duration-300 shadow-sm border border-gray-100"><x-lucide-linkedin
                                    class="w-4 h-4" /></a>
                            <a href="{{ $youtube }}" target="_blank"
                                class="w-11 h-11 rounded-xl bg-white text-titan-navy flex items-center justify-center hover:bg-titan-red hover:text-white transition-all duration-300 shadow-sm border border-gray-100"><x-lucide-youtube
                                    class="w-4 h-4" /></a>
                            <a href="{{ $instagram }}" target="_blank"
                                class="w-11 h-11 rounded-xl bg-white text-titan-navy flex items-center justify-center hover:bg-titan-red hover:text-white transition-all duration-300 shadow-sm border border-gray-100"><x-lucide-instagram
                                    class="w-4 h-4" /></a>
                            @if($telegram !== '#')
                                <a href="{{ $telegram }}" target="_blank"
                                    class="w-11 h-11 rounded-xl bg-white text-titan-navy flex items-center justify-center hover:bg-titan-red hover:text-white transition-all duration-300 shadow-sm border border-gray-100"><x-lucide-send
                                        class="w-4 h-4" /></a>
                            @endif
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="rounded-2xl overflow-hidden h-[260px] relative group border border-gray-100 shadow-sm">
                        <iframe src="{{ $googleMapsUrl }}"
                            class="w-full h-full border-0 grayscale group-hover:grayscale-0 transition-all duration-500"
                            allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                        <div
                            class="absolute top-4 left-4 bg-white px-3 py-1.5 rounded-lg shadow text-[10px] font-bold uppercase tracking-widest text-titan-navy flex items-center gap-1.5">
                            <div class="w-1.5 h-1.5 bg-titan-red rounded-full animate-pulse"></div>
                            {{ __('Phnom Penh') }}
                        </div>

                        <!-- Open in Maps Button -->
                        <div
                            class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <a href="{{ $googleMapsLink }}" target="_blank"
                                class="bg-white hover:bg-titan-red hover:text-white text-titan-navy px-3 py-2 rounded-lg shadow-xl text-[10px] font-bold uppercase tracking-widest flex items-center gap-2 transition-all">
                                <x-lucide-external-link class="w-3 h-3" />
                                {{ __('Open in Maps') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA BANNER -->
        <section class="bg-titan-navy py-16 relative overflow-hidden">
            <div
                class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]">
            </div>
            <div
                class="max-w-[1200px] mx-auto px-6 relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-2">
                        {{ __('Ready to Start Your Project?') }}
                    </h3>
                    <p class="text-white/40 text-sm">
                        {{ __('Our expert team is here to help you every step of the way.') }}
                    </p>
                </div>
                <a href="tel:{{ str_replace(' ', '', $phone) }}"
                    class="flex items-center gap-3 bg-titan-red hover:bg-white hover:text-titan-navy text-white px-8 py-4 rounded-xl font-bold text-sm uppercase tracking-widest transition-all duration-300 shrink-0 shadow-lg group">
                    <x-lucide-phone class="w-4 h-4 group-hover:animate-pulse" />
                    {{ __('Call Us Now') }}
                </a>
            </div>
        </section>
    </div>

</x-layouts.app>