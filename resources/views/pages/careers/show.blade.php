@php
    $job = \Illuminate\Support\Facades\Cache::remember("career_job_show_data_{$slug}_".app()->getLocale(), now()->addHours(12), function() use ($slug) {
        $jobDb = \App\Models\JobPosting::where('status', \App\Enums\JobPostingStatus::OPEN)->where('slug', $slug)->first();
        if ($jobDb) {
            $pickTranslation = function ($model, string $field, array $fallbackLocales = []) {
                $translations = $model->getTranslations($field);
                $locales = $fallbackLocales ?: [app()->getLocale(), 'km', 'kh', 'en'];
                foreach ($locales as $locale) {
                    $value = trim((string) ($translations[$locale] ?? ''));
                    if ($value !== '' && !str_contains($value, "\u{FFFD}")) {
                        return $value;
                    }
                }
                return trim((string) ($translations['km'] ?? $translations['kh'] ?? $translations['en'] ?? ''));
            };
            return [
                'id' => $jobDb->id,
                'title' => $pickTranslation($jobDb, 'title'),
                'dept' => $jobDb->department ? $pickTranslation($jobDb->department, 'name') : __('General'),
                'loc' => $pickTranslation($jobDb, 'location'),
                'type' => __($jobDb->type ?? 'FULL_TIME'),
                'salary' => $pickTranslation($jobDb, 'salary') ?: __('Negotiable'),
                'experience' => $pickTranslation($jobDb, 'experience') ?: __('2-3 Years'),
                'postedDate' => $jobDb->created_at ? $jobDb->created_at->format('M d, Y') : now()->format('M d, Y'),
                'description' => $pickTranslation($jobDb, 'summary'),
                'responsibilities' => $pickTranslation($jobDb, 'responsibilities'),
                'requirements' => $pickTranslation($jobDb, 'requirements'),
                'benefits' => $pickTranslation($jobDb, 'benefits'),
            ];
        }
        return null;
    });

    if (!$job && $slug === 'gen') {
        $job = [
            'id' => 'gen', 'title' => __('Visionary Talent'), 'dept' => __('General'),
            'loc' => __('Phnom Penh'), 'type' => __('Full-time'), 'salary' => __('Competitive'),
            'experience' => __('Mixed'), 'postedDate' => now()->format('M d, Y'),
            'description' => __('We are always looking for exceptional engineers and managers.'),
            'responsibilities' => '<ul><li>' . __('Willingness to learn and grow') . '</li><li>' . __('Contributing to various projects') . '</li></ul>',
            'requirements' => '<ul><li>' . __('Strong technical background') . '</li><li>' . __('Passion for quality') . '</li></ul>',
            'benefits' => '<ul><li>' . __('Competitive compensation') . '</li><li>' . __('Professional development') . '</li></ul>',
        ];
    }

    if (!$job) { abort(404); }

    $heroSummary = \Illuminate\Support\Str::limit(strip_tags($job['description'] ?? ''), 180);
    $pageTitle = $job['title'] ?? __('Job Details');
    $pageDesc = $heroSummary ?: __('Join our team of experts in construction.');
    $canonicalUrl = $slug === 'gen' ? url('/careers/gen') : route('careers.show', ['slug' => $slug]);
    $renderRichText = fn (?string $content) => \App\Support\RichContent::renderProject($content);
@endphp


<x-layouts.app :title="$pageTitle" :description="$pageDesc" image="/images/career-detail.png" :image-alt="$pageTitle" :canonical="$canonicalUrl" og-type="article">
    @push('head')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => route('home')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => __('Careers'), 'item' => route('careers')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $pageTitle, 'item' => $canonicalUrl],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'JobPosting',
                'title' => $job['title'],
                'description' => strip_tags($job['description'] ?? ''),
                'datePosted' => $job['postedDate'],
                'employmentType' => str_contains(strtolower($job['type']), 'full') ? 'FULL_TIME' : 'PART_TIME',
                'hiringOrganization' => [
                    '@type' => 'Organization',
                    'name' => 'Kimmex Construction & Investment Co., Ltd',
                    'sameAs' => url('/'),
                    'logo' => url('/logo.png'),
                ],
                'jobLocation' => [
                    '@type' => 'Place',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => $job['loc'],
                        'addressCountry' => 'KH',
                    ],
                ],
                'baseSalary' => [
                    '@type' => 'MonetaryAmount',
                    'currency' => 'USD',
                    'value' => ['@type' => 'QuantitativeValue', 'value' => $job['salary'], 'unitText' => 'MONTH'],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush

    <div class="bg-gray-50 min-h-screen">

        <!-- ═══ HERO ═══ -->
        <section class="relative overflow-hidden" style="background: linear-gradient(135deg, #071A33, #0B2B5C);">
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>
            <div class="relative z-10 max-w-[1280px] mx-auto px-6 pt-32 pb-12 md:pt-36 md:pb-16">

                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-xs mb-8" style="color: rgba(255,255,255,0.4);">
                    <a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <a href="{{ route('careers') }}" class="hover:text-white transition-colors">{{ __('Careers') }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <span style="color: rgba(255,255,255,0.8);">{{ \Illuminate\Support\Str::limit($job['title'], 30) }}</span>
                </nav>

                <!-- Department Badge -->
                <div class="inline-flex items-center gap-2 mb-4 px-3 py-1.5 rounded-full" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="w-1.5 h-1.5 rounded-full" style="background: var(--primary-color, #E31E24);"></div>
                    <span class="text-xs font-bold uppercase tracking-wider" style="color: rgba(255,255,255,0.7);">{{ $job['dept'] }}</span>
                </div>

                <!-- Title -->
                <h1 class="font-heading font-[900] leading-tight tracking-tight mb-6 {{ app()->getLocale() === 'km' ? 'font-khmer' : '' }}"
                    style="font-size: clamp(1.5rem, 4vw, 2.5rem); color: #FFFFFF;">
                    {{ $job['title'] }}
                </h1>

                <!-- Meta Pills -->
                <div class="flex flex-wrap gap-3 mb-8">
                    @foreach([
                        ['icon' => 'map-pin', 'value' => $job['loc']],
                        ['icon' => 'clock', 'value' => $job['type']],
                        ['icon' => 'briefcase', 'value' => $job['experience']],
                        ['icon' => 'banknote', 'value' => $job['salary']],
                    ] as $meta)
                        <span class="inline-flex items-center gap-2 text-sm" style="color: rgba(255,255,255,0.55);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary-color, #E31E24); opacity: 0.8;">
                                @if($meta['icon'] === 'map-pin')<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                @elseif($meta['icon'] === 'clock')<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                @elseif($meta['icon'] === 'briefcase')<path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/>
                                @elseif($meta['icon'] === 'banknote')<rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>
                                @endif
                            </svg>
                            {{ $meta['value'] }}
                        </span>
                    @endforeach
                    <x-page-view-count light />
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-3">
                    <a href="#apply-form"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                        style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                        {{ __('Apply Now') }}
                        <x-lucide-arrow-down class="w-4 h-4" />
                    </a>
                    <a href="{{ route('careers') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all duration-300"
                        style="border: 2px solid rgba(255,255,255,0.15); color: #FFFFFF;">
                        <x-lucide-arrow-left class="w-4 h-4" />
                        {{ __('All Positions') }}
                    </a>
                </div>
            </div>
        </section>


        <!-- ═══ CONTENT ═══ -->
        <section class="py-12 md:py-16">
            <div class="max-w-[1280px] mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <!-- Main Content: 8 cols -->
                    <div class="lg:col-span-8 space-y-6">

                        @php
                            $sections = [
                                ['key' => 'description',      'label' => __('About This Role'),       'icon' => 'file-text'],
                                ['key' => 'responsibilities', 'label' => __('Key Responsibilities'),  'icon' => 'list-checks'],
                                ['key' => 'requirements',     'label' => __('Requirements'),          'icon' => 'clipboard-check'],
                                ['key' => 'benefits',         'label' => __('What We Offer'),         'icon' => 'gift'],
                            ];
                        @endphp

                        @foreach($sections as $s)
                            @if(!empty(trim(strip_tags($job[$s['key']] ?? ''))))
                            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                                <div class="career-detail-section-heading px-6 md:px-8 py-5 border-b border-gray-100">
                                    <div class="career-detail-section-icon w-8 h-8 rounded-lg flex items-center justify-center"
                                         style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                                        @if($s['icon'] === 'file-text')
                                            <x-lucide-file-text class="w-4 h-4" style="color: var(--primary-color, #E31E24);" />
                                        @elseif($s['icon'] === 'list-checks')
                                            <x-lucide-list-checks class="w-4 h-4" style="color: var(--primary-color, #E31E24);" />
                                        @elseif($s['icon'] === 'clipboard-check')
                                            <x-lucide-clipboard-check class="w-4 h-4" style="color: var(--primary-color, #E31E24);" />
                                        @elseif($s['icon'] === 'gift')
                                            <x-lucide-gift class="w-4 h-4" style="color: var(--primary-color, #E31E24);" />
                                        @endif
                                    </div>
                                    <h2 class="job-section-title text-base font-bold text-gray-900 {{ app()->getLocale() === 'km' ? 'font-khmer' : '' }}">{{ $s['label'] }}</h2>
                                </div>
                                <div class="px-6 md:px-8 py-6 job-rich-content">
                                    {!! $renderRichText($job[$s['key']] ?? '') !!}
                                </div>
                            </div>
                            @endif
                        @endforeach


                        <!-- Apply Form -->
                        <div id="apply-form" class="bg-white rounded-2xl border border-gray-100 overflow-hidden scroll-mt-28">
                            <div class="career-detail-section-heading px-6 md:px-8 py-5 border-b border-gray-100">
                                <div class="career-detail-section-icon w-8 h-8 rounded-lg flex items-center justify-center"
                                     style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                                    <x-lucide-send class="w-4 h-4" style="color: var(--primary-color, #E31E24);" />
                                </div>
                                <div>
                                    <h2 class="job-section-title text-base font-bold text-gray-900 {{ app()->getLocale() === 'km' ? 'font-khmer' : '' }}">{{ __('Apply for This Role') }}</h2>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ __('Submit your application below.') }}</p>
                                </div>
                            </div>

                            <div class="px-6 md:px-8 py-6">
                                @if(session('success'))
                                    <div class="flex items-center gap-2 bg-green-50 border border-green-100 text-green-700 rounded-lg p-3 mb-5 text-sm font-medium">
                                        <x-lucide-check-circle class="w-4 h-4 text-green-500 shrink-0" />
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                                    @csrf
                                    <div class="hidden" aria-hidden="true"><input type="text" name="website_url" tabindex="-1" autocomplete="off" /></div>
                                    <input type="hidden" name="job_id" value="{{ $job['id'] }}">
                                    <input type="hidden" name="job_title" value="{{ $job['title'] }}">

                                    <!-- Name + Email -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Full Name') }} <span class="text-red-500">*</span></label>
                                            <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                                class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition @error('full_name') border-red-300 bg-red-50 @enderror"
                                                placeholder="{{ __('Your full name') }}" />
                                            @error('full_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Email') }} <span class="text-red-500">*</span></label>
                                            <input type="email" name="email" value="{{ old('email') }}" required
                                                class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition @error('email') border-red-300 bg-red-50 @enderror"
                                                placeholder="you@example.com" />
                                            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    <!-- Phone + CV -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Phone') }} <span class="text-red-500">*</span></label>
                                            <input type="tel" name="phone" value="{{ old('phone') }}" required inputmode="tel"
                                                class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition @error('phone') border-red-300 bg-red-50 @enderror"
                                                placeholder="+855 12 345 678" />
                                            @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                        </div>
                                        <div x-data="{ fileName: '' }">
                                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Resume / CV') }} <span class="text-red-500">*</span></label>
                                            <div class="relative h-11 rounded-xl border-2 border-dashed transition-all cursor-pointer overflow-hidden"
                                                :class="fileName ? 'border-green-300 bg-green-50' : 'border-gray-200 hover:border-gray-300 bg-gray-50'">
                                                <input type="file" name="resume" required accept=".pdf,.doc,.docx"
                                                    class="absolute inset-0 opacity-0 cursor-pointer z-10"
                                                    @change="fileName = $event.target.files[0]?.name || ''" />
                                                <div class="flex items-center justify-between h-full px-4">
                                                    <span class="text-sm truncate"
                                                        :class="fileName ? 'text-green-700 font-medium' : 'text-gray-400'"
                                                        x-text="fileName || '{{ __('Upload PDF or DOCX') }}'"></span>
                                                    <template x-if="!fileName"><x-lucide-upload class="w-4 h-4 text-gray-300 shrink-0" /></template>
                                                    <template x-if="fileName"><x-lucide-file-check class="w-4 h-4 text-green-500 shrink-0" /></template>
                                                </div>
                                            </div>
                                            @error('resume')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    <!-- Cover Letter -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Cover Letter') }} <span class="text-gray-300 font-normal">({{ __('optional') }})</span></label>
                                        <textarea name="message" rows="4"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition resize-none"
                                            placeholder="{{ __('Tell us why you\'re interested in this role...') }}">{{ old('message') }}</textarea>
                                    </div>

                                    <!-- Submit -->
                                    <div class="flex items-center justify-between pt-2" x-data="{ submitting: false }">
                                        <p class="text-xs text-gray-400">* {{ __('required') }}</p>
                                        <button type="submit"
                                            x-on:click="submitting = true"
                                            x-bind:disabled="submitting"
                                            class="inline-flex items-center gap-2 h-11 px-7 rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 group disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-md"
                                            style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                                            <span x-show="!submitting">{{ __('Submit Application') }}</span>
                                            <span x-show="submitting" class="inline-flex items-center gap-2">
                                                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                {{ __('Submitting...') }}
                                            </span>
                                            <x-lucide-arrow-right x-show="!submitting" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- Sidebar: 4 cols -->
                    <aside class="lg:col-span-4 space-y-5 lg:sticky lg:top-28 h-fit">

                        <!-- Quick Apply Card -->
                        <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(135deg, #071A33, #0B2B5C);">
                            <div class="p-6">
                                <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.35);">{{ __('Open Position') }}</p>
                                <h3 class="text-base font-bold leading-snug mb-5 {{ app()->getLocale() === 'km' ? 'font-khmer' : '' }}" style="color: #FFFFFF;">{{ $job['title'] }}</h3>
                                <a href="#apply-form"
                                    class="flex items-center justify-center gap-2 w-full h-11 rounded-xl text-sm font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5 mb-3"
                                    style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                                    {{ __('Apply Now') }}
                                    <x-lucide-arrow-down class="w-4 h-4" />
                                </a>
                                <a href="mailto:careers@kimmex.com?subject={{ urlencode('Application: '.$job['title']) }}"
                                    class="block text-center text-xs transition-colors" style="color: rgba(255,255,255,0.35);">
                                    {{ __('or email us directly') }}
                                </a>
                            </div>
                        </div>

                        <!-- Job Details Card -->
                        <div class="bg-white rounded-2xl border border-gray-100 p-6">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-5 pb-3 border-b border-gray-100">{{ __('Job Details') }}</h4>
                            <div class="space-y-4">
                                @foreach([
                                    ['icon' => 'lucide-map-pin', 'label' => __('Location'), 'value' => $job['loc']],
                                    ['icon' => 'lucide-briefcase', 'label' => __('Experience'), 'value' => $job['experience']],
                                    ['icon' => 'lucide-clock', 'label' => __('Type'), 'value' => $job['type']],
                                    ['icon' => 'lucide-banknote', 'label' => __('Salary'), 'value' => $job['salary']],
                                    ['icon' => 'lucide-building-2', 'label' => __('Department'), 'value' => $job['dept']],
                                    ['icon' => 'lucide-calendar', 'label' => __('Posted'), 'value' => $job['postedDate']],
                                ] as $detail)
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 mt-0.5">
                                            <x-dynamic-component :component="$detail['icon']" class="w-3.5 h-3.5 text-gray-400" stroke-width="1.8" />
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $detail['label'] }}</p>
                                            <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $detail['value'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Share Card -->
                        <div class="bg-white rounded-2xl border border-gray-100 p-6">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">{{ __('Share This Role') }}</h4>
                            <div class="flex gap-2" x-data="{ copied: false }">
                                <button @click="navigator.clipboard?.writeText(window.location.href); copied=true; setTimeout(()=>copied=false,2000)"
                                    class="flex-1 h-10 rounded-xl border flex items-center justify-center gap-2 text-xs font-bold transition-all"
                                    :class="copied ? 'bg-green-50 border-green-200 text-green-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
                                    <template x-if="!copied"><x-lucide-link class="w-3.5 h-3.5" /></template>
                                    <template x-if="copied"><x-lucide-check class="w-3.5 h-3.5" /></template>
                                    <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy Link') }}'"></span>
                                </button>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                    class="w-10 h-10 rounded-xl bg-social-facebook flex items-center justify-center text-white hover:brightness-110 transition-all">
                                    <x-social-icon network="facebook" class="w-4 h-4" />
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                    class="w-10 h-10 rounded-xl bg-social-linkedin flex items-center justify-center text-white hover:brightness-110 transition-all">
                                    <x-social-icon network="linkedin" class="w-4 h-4" />
                                </a>
                                <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($job['title']) }}" target="_blank" rel="noopener"
                                    class="w-10 h-10 rounded-xl bg-social-telegram flex items-center justify-center text-white hover:brightness-110 transition-all">
                                    <x-social-icon network="telegram" class="w-4 h-4" />
                                </a>
                            </div>
                        </div>

                        <!-- Back to all -->
                        <a href="{{ route('careers') }}"
                            class="flex items-center justify-center gap-2 w-full h-11 rounded-xl border border-gray-200 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 transition-colors">
                            <x-lucide-arrow-left class="w-4 h-4" />
                            {{ __('All Open Positions') }}
                        </a>
                    </aside>
                </div>
            </div>
        </section>
    </div>

    <!-- Job content rich text styles -->
    <style>
        .job-rich-content {
            color: #4b5563;
            font-size: 0.9375rem;
            line-height: 1.8;
        }
        .job-rich-content p { margin-bottom: 1rem; }
        .job-rich-content p:last-child { margin-bottom: 0; }
        .job-rich-content ul, .job-rich-content ol {
            margin: 0.75rem 0;
            padding-left: 0;
            list-style: none !important;
        }
        .job-rich-content li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.6rem;
            font-size: 0.875rem;
            line-height: 1.7;
            list-style: none !important;
        }
        .job-rich-content li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0.6rem;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--primary-color, #E31E24);
            opacity: 0.6;
        }
        .job-rich-content strong, .job-rich-content b {
            color: #1f2937;
            font-weight: 700;
        }
        .job-rich-content h3, .job-rich-content h4 {
            color: #111827;
            font-weight: 700;
            margin: 1.25rem 0 0.5rem;
            font-size: 0.9375rem;
        }
    </style>

</x-layouts.app>
