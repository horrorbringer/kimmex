<x-layouts.app title="Privacy Policy" description="Kimmex's privacy policy outlining how we collect, use, and protect your personal information.">

    <div class="bg-white min-h-screen text-titan-navy">

        <!-- === HERO SECTION === -->
        <section class="relative z-10 flex items-end overflow-hidden bg-titan-navy min-h-[320px] sm:min-h-[360px] md:min-h-[400px]">
            <div class="absolute inset-0">
                <img src="/images/webp/projects/Thumbnail-6.webp" alt="Privacy" class="w-full h-full object-cover opacity-50" decoding="async" loading="eager" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/90 via-titan-navy/40 to-titan-navy/20"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/50 via-transparent to-transparent"></div>
            </div>

            <div class="relative z-20 w-full max-w-[900px] mx-auto px-5 sm:px-6 pb-10 sm:pb-14 md:pb-16 pt-32 sm:pt-40" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-6'" class="transition-all duration-700 delay-100 inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-white/5 backdrop-blur-sm rounded-full text-white/80 text-[9px] sm:text-[10px] md:text-[11px] font-bold uppercase tracking-widest mb-4 sm:mb-6 border border-white/10">
                    <x-lucide-shield-check class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-titan-red" />
                    {{ __('Legal & Compliance') }}
                </div>

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" class="transition-all duration-700 delay-300 font-heading font-[900] !text-white mb-3 sm:mb-4 leading-[1.05] tracking-tight uppercase text-2xl sm:text-3xl md:text-4xl lg:text-5xl">
                    {{ __('Privacy Policy') }}
                </h1>

                <p :class="shown ? 'opacity-100' : 'opacity-0'" class="transition-all duration-700 delay-500 text-xs sm:text-sm md:text-base text-white/50 max-w-xl leading-relaxed">
                    {{ __('Last Updated: January 1, 2026') }}
                </p>
            </div>
        </section>

        <!-- === TABLE OF CONTENTS === -->
        <section class="max-w-[900px] mx-auto px-4 sm:px-6 relative z-40 -mt-10 sm:-mt-14">
            <div class="bg-white rounded-xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-gray-100 p-5 sm:p-8 lg:p-10">
                <h2 class="text-xs sm:text-sm font-black uppercase tracking-widest text-titan-navy/40 mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                    <x-lucide-list class="w-4 h-4 text-titan-red" />
                    {{ __('Table of Contents') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    @php
                        $toc = [
                            ['id' => 'introduction', 'label' => __('1. Introduction')],
                            ['id' => 'information-collected', 'label' => __('2. Information We Collect')],
                            ['id' => 'how-we-use', 'label' => __('3. How We Use Your Information')],
                            ['id' => 'data-storage', 'label' => __('4. Data Storage & Security')],
                            ['id' => 'third-party', 'label' => __('5. Third-Party Sharing')],
                            ['id' => 'your-rights', 'label' => __('6. Your Rights')],
                            ['id' => 'cookies', 'label' => __('7. Cookie Policy')],
                            ['id' => 'contact', 'label' => __('8. Contact Information')],
                        ];
                    @endphp
                    @foreach($toc as $item)
                        <a href="#{{ $item['id'] }}" class="text-xs sm:text-sm font-bold text-titan-navy/60 hover:text-titan-red transition-colors flex items-center gap-2 group py-1">
                            <span class="w-1.5 h-1.5 bg-titan-red rounded-full group-hover:scale-125 transition-transform shrink-0"></span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- === MAIN CONTENT === -->
        <section class="max-w-[900px] mx-auto px-5 sm:px-6 py-14 sm:py-20 md:py-24">

            <!-- Introduction -->
            <div id="introduction" class="scroll-mt-32 mb-12 sm:mb-16" x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-700">
                <div class="flex items-center gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <span class="w-8 sm:w-12 h-1 bg-titan-red"></span>
                    <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-[0.3em] sm:tracking-[0.4em] text-titan-navy/40">{{ __('Introduction') }}</span>
                </div>
                <p class="text-base sm:text-lg text-titan-navy/70 leading-relaxed mb-5 sm:mb-6">
                    {{ __('KIMMEX Construction & Investment Co., Ltd. ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or interact with our services.') }}
                </p>
                <p class="text-base sm:text-lg text-titan-navy/70 leading-relaxed">
                    {{ __('We take data protection seriously and comply with applicable Cambodian laws and international best practices for data privacy. By using our website or services, you consent to the data practices described in this policy.') }}
                </p>
            </div>

            <!-- Information We Collect -->
            <div id="information-collected" class="scroll-mt-32 mb-12 sm:mb-16" x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-700 delay-100">
                <div class="flex items-center gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <span class="w-8 sm:w-12 h-1 bg-titan-red"></span>
                    <h2 class="text-xl sm:text-2xl font-black text-titan-navy uppercase tracking-tight">{{ __('Information We Collect') }}</h2>
                </div>

                <div class="space-y-4 sm:space-y-6">
                    @php
                        $infoTypes = [
                            [
                                'title' => __('Personal Information'),
                                'desc' => __('When you contact us, apply for positions, or request services, we may collect personal information such as your name, email address, phone number, company name, and any other information you voluntarily provide to us.'),
                                'icon' => 'lucide-user'
                            ],
                            [
                                'title' => __('Professional Information'),
                                'desc' => __('For job applications and business inquiries, we may collect professional information including your resume, work experience, qualifications, and career objectives.'),
                                'icon' => 'lucide-briefcase'
                            ],
                            [
                                'title' => __('Technical Data'),
                                'desc' => __('When you visit our website, we automatically collect certain technical information such as your IP address, browser type, operating system, referring URLs, pages visited, and dates/times of visits.'),
                                'icon' => 'lucide-monitor'
                            ],
                            [
                                'title' => __('Cookies and Similar Technologies'),
                                'desc' => __('We use cookies and similar tracking technologies to enhance your browsing experience, analyze website traffic, and personalize content. You can control cookie settings through your browser preferences.'),
                                'icon' => 'lucide-cookie'
                            ],
                        ];
                    @endphp
                    @foreach($infoTypes as $item)
                        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 p-4 sm:p-6 bg-gray-50 rounded-xl border border-gray-100 hover:border-titan-red/20 transition-colors">
                            <div class="shrink-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-titan-red/10 flex items-center justify-center text-titan-red">
                                    <x-dynamic-component :component="$item['icon']" class="w-5 h-5" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-titan-navy uppercase tracking-tight mb-1.5 sm:mb-2">{{ $item['title'] }}</h3>
                                <p class="text-titan-navy/60 text-sm leading-relaxed">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- How We Use Your Information -->
            <div id="how-we-use" class="scroll-mt-32 mb-12 sm:mb-16" x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-700">
                <div class="flex items-center gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <span class="w-8 sm:w-12 h-1 bg-titan-red"></span>
                    <h2 class="text-xl sm:text-2xl font-black text-titan-navy uppercase tracking-tight">{{ __('How We Use Your Information') }}</h2>
                </div>

                <p class="text-base sm:text-lg text-titan-navy/70 leading-relaxed mb-6 sm:mb-8">
                    {{ __('We use the information we collect for the following purposes:') }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    @php
                        $uses = [
                            ['icon' => 'lucide-mail', 'text' => __('To respond to your inquiries and provide requested information')],
                            ['icon' => 'lucide-user-plus', 'text' => __('To process job applications and communicate with candidates')],
                            ['icon' => 'lucide-file-text', 'text' => __('To provide services and fulfill contractual obligations')],
                            ['icon' => 'lucide-bell', 'text' => __('To send updates about projects, news, and company announcements')],
                            ['icon' => 'lucide-chart-bar', 'text' => __('To improve our website and user experience')],
                            ['icon' => 'lucide-shield', 'text' => __('To detect and prevent fraud and ensure security')],
                            ['icon' => 'lucide-circle-check', 'text' => __('To comply with legal and regulatory requirements')],
                            ['icon' => 'lucide-users', 'text' => __('To communicate about events, opportunities, and partnerships')],
                        ];
                    @endphp
                    @foreach($uses as $use)
                        <div class="flex items-start gap-3 sm:gap-4 p-4 sm:p-5 bg-white rounded-xl border border-gray-100 hover:shadow-md hover:border-titan-red/20 transition-all">
                            <div class="w-8 h-8 rounded-lg bg-titan-red/10 flex items-center justify-center text-titan-red shrink-0">
                                <x-dynamic-component :component="$use['icon']" class="w-4 h-4" />
                            </div>
                            <p class="text-titan-navy/70 text-xs sm:text-sm font-medium leading-relaxed">{{ $use['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Data Storage & Security -->
            <div id="data-storage" class="scroll-mt-32 mb-12 sm:mb-16" x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-700">
                <div class="flex items-center gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <span class="w-8 sm:w-12 h-1 bg-titan-red"></span>
                    <h2 class="text-xl sm:text-2xl font-black text-titan-navy uppercase tracking-tight">{{ __('Data Storage & Security') }}</h2>
                </div>

                <div class="bg-titan-navy rounded-xl p-5 sm:p-8 lg:p-10 !text-white">
                    <div class="flex flex-col sm:flex-row items-start gap-4 mb-6">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/10 flex items-center justify-center !text-white shrink-0">
                            <x-lucide-lock class="w-5 h-5 sm:w-6 sm:h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg !text-white sm:text-xl font-black uppercase tracking-tight mb-2 sm:mb-3">{{ __('Our Security Commitment') }}</h3>
                            <p class="text-white/70 text-sm sm:text-base leading-relaxed">
                                {{ __('We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. These include:') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-6 sm:mt-8">
                        @php
                            $securityMeasures = [
                                __('Encrypted data transmission (SSL/TLS)'),
                                __('Secure server environments'),
                                __('Access controls and authentication'),
                                __('Regular security audits'),
                                __('Employee training on data protection'),
                                __('Backup and disaster recovery'),
                            ];
                        @endphp
                        @foreach($securityMeasures as $measure)
                            <div class="flex items-center gap-2 sm:gap-3">
                                <x-lucide-check class="w-4 h-4 sm:w-5 sm:h-5 text-titan-red shrink-0" />
                                <span class="text-white/80 text-xs sm:text-sm">{{ $measure }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Third-Party Sharing -->
            <div id="third-party" class="scroll-mt-32 mb-12 sm:mb-16" x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-700">
                <div class="flex items-center gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <span class="w-8 sm:w-12 h-1 bg-titan-red"></span>
                    <h2 class="text-xl sm:text-2xl font-black text-titan-navy uppercase tracking-tight">{{ __('Third-Party Sharing') }}</h2>
                </div>

                <p class="text-base sm:text-lg text-titan-navy/70 leading-relaxed mb-5 sm:mb-6">
                    {{ __('We do not sell, trade, or rent your personal information to third parties. We may share your information in the following circumstances:') }}
                </p>

                <div class="space-y-3 sm:space-y-4">
                    @php
                        $sharing = [
                            ['title' => __('Service Providers'), 'desc' => __('With trusted third-party vendors who assist us in operating our website, conducting business, or servicing you (e.g., hosting providers, payment processors, email services).')],
                            ['title' => __('Legal Requirements'), 'desc' => __('When required by law, regulation, legal process, or governmental request.')],
                            ['title' => __('Business Transfers'), 'desc' => __('In connection with a merger, acquisition, or sale of assets, where your information may be transferred as part of the transaction.')],
                            ['title' => __('With Consent'), 'desc' => __('When you have given us explicit consent to share your information for a specific purpose.')],
                        ];
                    @endphp
                    @foreach($sharing as $item)
                        <div class="p-4 sm:p-6 bg-gray-50 rounded-xl border-l-4 border-titan-red">
                            <h3 class="font-black text-titan-navy uppercase tracking-tight text-sm sm:text-base mb-1.5 sm:mb-2">{{ $item['title'] }}</h3>
                            <p class="text-titan-navy/60 text-xs sm:text-sm leading-relaxed">{{ $item['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Your Rights -->
            <div id="your-rights" class="scroll-mt-32 mb-12 sm:mb-16" x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-700">
                <div class="flex items-center gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <span class="w-8 sm:w-12 h-1 bg-titan-red"></span>
                    <h2 class="text-xl sm:text-2xl font-black text-titan-navy uppercase tracking-tight">{{ __('Your Rights') }}</h2>
                </div>

                <p class="text-base sm:text-lg text-titan-navy/70 leading-relaxed mb-6 sm:mb-8">
                    {{ __('You have the following rights regarding your personal information:') }}
                </p>

                <div class="space-y-3 sm:space-y-4">
                    @php
                        $rights = [
                            ['icon' => 'lucide-eye', 'title' => __('Right to Access'), 'desc' => __('Request a copy of the personal information we hold about you.')],
                            ['icon' => 'lucide-pencil', 'title' => __('Right to Correction'), 'desc' => __('Request correction of inaccurate or incomplete information.')],
                            ['icon' => 'lucide-trash-2', 'title' => __('Right to Deletion'), 'desc' => __('Request deletion of your personal information, subject to legal obligations.')],
                            ['icon' => 'lucide-circle-pause', 'title' => __('Right to Object'), 'desc' => __('Object to processing of your personal information in certain circumstances.')],
                            ['icon' => 'lucide-file-output', 'title' => __('Right to Portability'), 'desc' => __('Request transfer of your data to another organization.')],
                        ];
                    @endphp
                    @foreach($rights as $right)
                        <div class="flex items-start gap-3 sm:gap-4 p-4 sm:p-6 bg-white rounded-xl border border-gray-100 hover:border-titan-red/30 transition-colors">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-titan-red/10 flex items-center justify-center text-titan-red shrink-0">
                                <x-dynamic-component :component="$right['icon']" class="w-4 h-4 sm:w-5 sm:h-5" />
                            </div>
                            <div>
                                <h3 class="font-black text-titan-navy uppercase tracking-tight text-sm sm:text-base mb-1">{{ $right['title'] }}</h3>
                                <p class="text-titan-navy/60 text-xs sm:text-sm">{{ $right['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 sm:mt-8 p-4 sm:p-6 bg-titan-red/10 rounded-xl border border-titan-red/20">
                    <p class="text-titan-navy/80 text-xs sm:text-sm leading-relaxed">
                        <strong class="text-titan-navy font-black">{{ __('To exercise these rights:') }}</strong>
                        {{ __('Contact us at privacy@kimmex.com.kh with your request. We will respond within 30 days.') }}
                    </p>
                </div>
            </div>

            <!-- Cookie Policy -->
            <div id="cookies" class="scroll-mt-32 mb-12 sm:mb-16" x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-700">
                <div class="flex items-center gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <span class="w-8 sm:w-12 h-1 bg-titan-red"></span>
                    <h2 class="text-xl sm:text-2xl font-black text-titan-navy uppercase tracking-tight">{{ __('Cookie Policy') }}</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                    <div>
                        <p class="text-base sm:text-lg text-titan-navy/70 leading-relaxed mb-5 sm:mb-6">
                            {{ __('Cookies are small data files stored on your device when you visit our website. We use the following types of cookies:') }}
                        </p>

                        <div class="space-y-4">
                            @php
                                $cookieTypes = [
                                    ['name' => __('Essential Cookies'), 'desc' => __('Required for website functionality.')],
                                    ['name' => __('Analytics Cookies'), 'desc' => __('Help us understand website usage and improve performance.')],
                                    ['name' => __('Functionality Cookies'), 'desc' => __('Remember your preferences and settings.')],
                                ];
                            @endphp
                            @foreach($cookieTypes as $cookie)
                                <div class="flex items-start gap-3">
                                    <x-lucide-circle-dot class="w-4 h-4 text-titan-red shrink-0 mt-1" />
                                    <div>
                                        <span class="font-bold text-titan-navy text-sm">{{ $cookie['name'] }}</span>
                                        <p class="text-titan-navy/50 text-xs">{{ $cookie['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5 sm:p-8 border border-gray-100">
                        <h3 class="font-black text-titan-navy uppercase tracking-tight mb-4 flex items-center gap-3">
                            <x-lucide-settings class="w-5 h-5 text-titan-red" />
                            {{ __('Manage Cookies') }}
                        </h3>
                        <p class="text-titan-navy/60 text-sm leading-relaxed mb-6">
                            {{ __('You can control cookies through your browser settings. Most browsers allow you to:') }}
                        </p>
                        <ul class="space-y-2 text-sm text-titan-navy/70">
                            <li class="flex items-center gap-2"><x-lucide-check class="w-4 h-4 text-titan-red" /> {{ __('View and delete cookies') }}</li>
                            <li class="flex items-center gap-2"><x-lucide-check class="w-4 h-4 text-titan-red" /> {{ __('Block all cookies') }}</li>
                            <li class="flex items-center gap-2"><x-lucide-check class="w-4 h-4 text-titan-red" /> {{ __('Allow only first-party cookies') }}</li>
                            <li class="flex items-center gap-2"><x-lucide-check class="w-4 h-4 text-titan-red" /> {{ __('Block cookies from specific sites') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div id="contact" class="scroll-mt-32 mb-12 sm:mb-16" x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-700">
                <div class="flex items-center gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <span class="w-8 sm:w-12 h-1 bg-titan-red"></span>
                    <h2 class="text-xl sm:text-2xl font-black text-titan-navy uppercase tracking-tight">{{ __('Contact Information') }}</h2>
                </div>

                <div class="bg-titan-navy rounded-xl p-5 sm:p-8 lg:p-12 text-white relative overflow-hidden">
                    <!-- Decorative elements -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-titan-red/10 rounded-full blur-[100px] pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full blur-[80px] pointer-events-none"></div>

                    <div class="relative z-10">
                        <p class="text-white/70 text-sm sm:text-base leading-relaxed mb-6 sm:mb-8 max-w-2xl">
                            {{ __('If you have any questions about this Privacy Policy or our data practices, please contact us:') }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                            <a href="mailto:privacy@kimmex.com.kh" class="flex items-center gap-3 sm:gap-4 p-4 sm:p-6 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-colors group">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-titan-red/20 flex items-center justify-center text-titan-red group-hover:scale-110 transition-transform shrink-0">
                                    <x-lucide-mail class="w-4 h-4 sm:w-5 sm:h-5" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-white/50">{{ __('Email') }}</div>
                                    <div class="font-bold text-white text-xs sm:text-sm truncate">privacy@kimmex.com.kh</div>
                                </div>
                            </a>

                            <a href="tel:+85523999999" class="flex items-center gap-3 sm:gap-4 p-4 sm:p-6 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-colors group">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-titan-red/20 flex items-center justify-center text-titan-red group-hover:scale-110 transition-transform shrink-0">
                                    <x-lucide-phone class="w-4 h-4 sm:w-5 sm:h-5" />
                                </div>
                                <div>
                                    <div class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-white/50">{{ __('Phone') }}</div>
                                    <div class="font-bold text-white text-xs sm:text-sm">+855 23 999 999</div>
                                </div>
                            </a>

                            <a href="/contact" class="flex items-center gap-3 sm:gap-4 p-4 sm:p-6 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-colors group">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-titan-red/20 flex items-center justify-center text-titan-red group-hover:scale-110 transition-transform shrink-0">
                                    <x-lucide-message-circle class="w-4 h-4 sm:w-5 sm:h-5" />
                                </div>
                                <div>
                                    <div class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-white/50">{{ __('Contact Form') }}</div>
                                    <div class="font-bold text-white text-xs sm:text-sm">{{ __('Send Message') }}</div>
                                </div>
                            </a>
                        </div>

                        <div class="mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-white/10">
                            <p class="text-white/50 text-xs sm:text-sm flex flex-wrap items-center gap-2">
                                <x-lucide-building class="w-4 h-4 shrink-0" />
                                <span>{{ __('KIMMEX Construction & Investment Co., Ltd.') }}</span>
                                <span class="hidden sm:inline mx-1">|</span>
                                <span>{{ __('Phnom Penh, Cambodia') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- === CTA SECTION === -->
        <section class="bg-gray-50 py-14 sm:py-20 px-5 sm:px-6 border-t border-gray-100">
            <div class="max-w-[1200px] mx-auto text-center">
                <h2 class="text-2xl sm:text-3xl font-black text-titan-navy uppercase tracking-tight mb-3 sm:mb-4">
                    {{ __('Questions About Privacy?') }}
                </h2>
                <p class="text-titan-navy/60 text-sm sm:text-base mb-6 sm:mb-8 max-w-xl mx-auto">
                    {{ __('Our team is here to address any privacy-related questions or concerns you may have.') }}
                </p>
                <a href="/contact" class="inline-flex items-center gap-2 sm:gap-3 bg-titan-red hover:bg-titan-navy text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-black text-xs sm:text-sm uppercase tracking-widest transition-all duration-300 shadow-lg hover:shadow-xl">
                    <x-lucide-message-circle class="w-4 h-4" />
                    {{ __('Contact Us') }}
                </a>
            </div>
        </section>

    </div>

</x-layouts.app>
