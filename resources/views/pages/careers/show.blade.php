@php
    $job = \Illuminate\Support\Facades\Cache::remember("career_job_show_data_{$slug}_".app()->getLocale(), now()->addHours(12), function() use ($slug) {
        $jobDb = \App\Models\JobPosting::where('isActive', true)->where('slug', $slug)->first();
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
            'id' => 'gen',
            'title' => __('Visionary Talent'),
            'dept' => __('General'),
            'loc' => __('Phnom Penh'),
            'type' => __('Full-time'),
            'salary' => __('Competitive'),
            'experience' => __('Mixed'),
            'postedDate' => now()->format('M d, Y'),
            'description' => __('We are always looking for exceptional engineers and managers. Even if there is no specific opening that matches your profile, we encourage you to submit your general application.'),
            'responsibilities' => '<ul><li>' . __('Willingness to learn and grow within the Kimmex ecosystem') . '</li><li>' . __('Contributing to various projects across departments') . '</li><li>' . __('Maintaining professional excellence in all tasks') . '</li></ul>',
            'requirements' => '<ul><li>' . __('Strong technical background in engineering or construction') . '</li><li>' . __('Passion for innovation and quality') . '</li><li>' . __('Excellent teamwork and communication skills') . '</li></ul>',
            'benefits' => '<ul><li>' . __('Competitive compensation package') . '</li><li>' . __('Continuous professional development') . '</li><li>' . __('Opportunity to work on landmark projects') . '</li></ul>',
        ];
    }

    if (!$job) {
        abort(404);
    }

    $heroSummary = \Illuminate\Support\Str::limit(strip_tags($job['description'] ?? ''), 180);
    $pageTitle = $job['title'] ?? __('Job Details');
    $pageDesc = $heroSummary ?: __('Join our team of experts in the construction and investment industry.');
    $canonicalUrl = $slug === 'gen' ? url('/careers/gen') : route('careers.show', ['slug' => $slug]);

    $renderRichText = fn (?string $content) => \App\Support\RichContent::renderProject($content);

    $renderParagraphContent = function (?string $content) {
        $content = trim((string) $content);

        if ($content === '') {
            return '';
        }

        if (preg_match('/<\s*(p|h[1-6]|blockquote|table|img|br)\b/i', $content)) {
            return $content;
        }

        $content = preg_replace('/\s+/u', ' ', $content) ?: $content;

        return '<p>' . e($content) . '</p>';
    };
@endphp

<x-layouts.app :title="$pageTitle" :description="$pageDesc" :canonical="$canonicalUrl">
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
    @endpush

@if(!$job)
    <div class="py-40 text-center bg-gray-50 min-h-screen">
         <div class="max-w-[1200px] mx-auto px-6">
            <h1 class="text-4xl font-black text-titan-navy mb-4">{{ __('Position Not Found') }}</h1>
            <p class="text-titan-navy/50 mb-8">{{ __('The role you are looking for may have been filled or the link is outdated.') }}</p>
            <a href="{{ route('careers') }}" class="inline-block bg-titan-navy text-white px-8 py-4 rounded font-bold text-xs uppercase tracking-widest hover:bg-titan-red transition-all">
                {{ __('Return to Openings') }}
            </a>
         </div>
    </div>
@else
    <div class="bg-white min-h-screen font-sans text-titan-navy">
        
        <!-- HERO -->
        <section class="bg-white border-b border-gray-100 pt-28 pb-10 md:pt-32 md:pb-12">
            <div class="max-w-[1200px] mx-auto px-6">
                {{-- Breadcrumb --}}
                <div class="flex flex-wrap items-center gap-2 mb-6 text-[9px] font-black uppercase tracking-[0.2em]">
                    <a href="{{ route('careers') }}" class="flex items-center gap-1.5 text-titan-navy/40 hover:text-titan-red transition-colors">
                        <x-lucide-arrow-left class="w-3 h-3" />{{ __('Openings') }}
                    </a>
                    <span class="text-gray-200">/</span>
                    <span class="text-titan-navy/30 truncate max-w-[200px]">{{ $job['title'] }}</span>
                </div>

                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="flex-1 min-w-0">
                        {{-- Dept badge --}}
                        <span class="inline-flex items-center gap-1.5 mb-3 text-[9px] font-black uppercase tracking-[0.25em] text-titan-red">
                            <span class="w-1.5 h-1.5 rounded-full bg-titan-red"></span>
                            {{ $job['dept'] }}
                        </span>

                        <h1 class="job-page-title font-black text-titan-navy uppercase leading-tight mb-4 break-words"
                            style="font-size: clamp(1.4rem, 3.5vw, 2.2rem) !important; font-weight: 900 !important;">
                            {{ $job['title'] }}
                        </h1>

                        {{-- Meta chips --}}
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1.5 h-7 px-3 rounded-full bg-gray-50 border border-gray-100 text-[10px] font-bold text-titan-navy/60">
                                <x-lucide-map-pin class="w-3 h-3 text-titan-red shrink-0" />{{ $job['loc'] }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 h-7 px-3 rounded-full bg-gray-50 border border-gray-100 text-[10px] font-bold text-titan-navy/60">
                                <x-lucide-briefcase class="w-3 h-3 text-titan-red shrink-0" />{{ $job['experience'] }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 h-7 px-3 rounded-full bg-gray-50 border border-gray-100 text-[10px] font-bold text-titan-navy/60">
                                <x-lucide-clock class="w-3 h-3 text-titan-red shrink-0" />{{ $job['type'] }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 h-7 px-3 rounded-full bg-gray-50 border border-gray-100 text-[10px] font-bold text-titan-navy/60">
                                <x-lucide-dollar-sign class="w-3 h-3 text-titan-red shrink-0" />{{ $job['salary'] }}
                            </span>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="flex gap-2 shrink-0">
                        <a href="#apply-form"
                            class="inline-flex items-center gap-2 h-10 px-5 rounded bg-titan-red text-white text-[9px] font-black uppercase tracking-[0.2em] hover:bg-titan-navy transition-colors">
                            {{ __('Apply Now') }}<x-lucide-arrow-down class="w-3.5 h-3.5" />
                        </a>
                        <a href="{{ route('careers') }}"
                            class="inline-flex items-center gap-2 h-10 px-4 rounded border border-gray-200 text-titan-navy/50 text-[9px] font-black uppercase tracking-[0.2em] hover:border-gray-300 hover:text-titan-navy transition-colors">
                            {{ __('All Roles') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTENT -->
        <section class="py-10 md:py-14 max-w-[1200px] mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- Main: 8 cols -->
                <div class="lg:col-span-8 space-y-4">

                    @php
                        $sections = [
                            ['key' => 'description',      'label' => __('Job Summary'),           'bg' => 'bg-white',          'border' => 'border-gray-100'],
                            ['key' => 'responsibilities', 'label' => __('Key Responsibilities'),  'bg' => 'bg-gray-50/60',     'border' => 'border-gray-100'],
                            ['key' => 'requirements',     'label' => __('Requirements'),          'bg' => 'bg-white',          'border' => 'border-gray-100'],
                            ['key' => 'benefits',         'label' => __('Benefits'),              'bg' => 'bg-titan-red/[0.03]','border' => 'border-titan-red/10'],
                        ];
                    @endphp

                    @foreach($sections as $s)
                        @if(!empty(trim(strip_tags($job[$s['key']] ?? ''))))
                        <div class="{{ $s['bg'] }} border {{ $s['border'] }} rounded-lg p-5 md:p-6">
                            <div class="flex items-center gap-2.5 mb-4">
                                <div class="w-5 h-[2px] bg-titan-red rounded-full"></div>
                                <h2 class="job-section-title text-[11px] font-black text-titan-navy uppercase tracking-[0.2em]">{{ $s['label'] }}</h2>
                            </div>
                            <div class="job-rich-content">{!! $renderRichText($job[$s['key']] ?? '') !!}</div>
                        </div>
                        @endif
                    @endforeach

                    <!-- Apply Form -->
                    <div id="apply-form" class="bg-white border border-gray-100 rounded-lg p-5 md:p-6 mt-2">
                        <div class="flex items-center gap-2.5 mb-5">
                            <div class="w-5 h-[2px] bg-titan-red rounded-full"></div>
                            <div>
                                <h2 class="job-section-title text-[11px] font-black text-titan-navy uppercase tracking-[0.2em]">{{ __('Apply for this Role') }}</h2>
                                <p class="text-[10px] text-titan-navy/35 mt-0.5">{{ __('Fill in the form below to submit your application.') }}</p>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="flex items-center gap-2 bg-green-50 border border-green-100 text-green-700 rounded p-3 mb-5 text-[11px] font-semibold">
                                <x-lucide-check-circle class="w-4 h-4 text-green-500 shrink-0" />
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="hidden" aria-hidden="true">
                                <input type="text" name="website_url" tabindex="-1" autocomplete="off" />
                            </div>
                            <input type="hidden" name="job_id" value="{{ $job['id'] }}">
                            <input type="hidden" name="job_title" value="{{ $job['title'] }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">{{ __('Full Name') }} <span class="text-titan-red">*</span></label>
                                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                        placeholder="{{ __('Your full name') }}"
                                        class="w-full h-10 px-3 rounded border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all @error('full_name') border-titan-red bg-red-50 @enderror" />
                                    @error('full_name')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">{{ __('Email') }} <span class="text-titan-red">*</span></label>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                        placeholder="you@example.com"
                                        class="w-full h-10 px-3 rounded border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all @error('email') border-titan-red bg-red-50 @enderror" />
                                    @error('email')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div x-data="{ phoneVal: '{{ old('phone') }}', phoneError: '' }">
                                    <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">{{ __('Phone') }} <span class="text-titan-red">*</span></label>
                                    <input type="tel" name="phone" required inputmode="tel"
                                        x-model="phoneVal"
                                        @blur="phoneError = phoneVal && !/^\+?[\d\s\-(). ]{7,25}$/.test(phoneVal.trim()) ? '{{ __('Enter a valid number, e.g. +855 12 345 678') }}' : ''"
                                        @input="if(phoneError) phoneError = ''"
                                        :class="phoneError ? 'border-titan-red bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-titan-red/40 focus:bg-white'"
                                        class="w-full h-10 px-3 rounded border text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:ring-1 focus:ring-titan-red/10 transition-all @error('phone') border-titan-red bg-red-50 @enderror"
                                        placeholder="+855 12 345 678" />
                                    <p x-show="phoneError" x-text="phoneError" class="text-[9px] text-titan-red font-bold mt-1" style="display:none"></p>
                                    @error('phone')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div x-data="{ fileName: '' }">
                                    <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">{{ __('Resume / CV') }} <span class="text-titan-red">*</span></label>
                                    <div class="relative h-10 rounded border transition-all duration-200 cursor-pointer overflow-hidden"
                                        :class="fileName ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300'">
                                        <input type="file" name="resume" required accept=".pdf,.doc,.docx"
                                            class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full"
                                            @change="fileName = $event.target.files[0]?.name || ''" />
                                        <div class="flex items-center justify-between h-full px-3">
                                            <span class="text-[11px] font-semibold truncate"
                                                :class="fileName ? 'text-green-700' : 'text-titan-navy/30'"
                                                x-text="fileName || '{{ __('Choose PDF or DOCX…') }}'"></span>
                                            <template x-if="!fileName"><x-lucide-upload class="w-3.5 h-3.5 text-titan-navy/25 shrink-0" /></template>
                                            <template x-if="fileName"><x-lucide-file-check class="w-3.5 h-3.5 text-green-500 shrink-0" /></template>
                                        </div>
                                    </div>
                                    @error('resume')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">
                                    {{ __('Cover Letter') }} <span class="text-[10px] font-medium normal-case tracking-normal text-titan-navy/20">{{ __('(optional)') }}</span>
                                </label>
                                <textarea name="message" rows="4"
                                    placeholder="{{ __('Brief intro and why you are interested in this role…') }}"
                                    class="w-full px-3 py-2.5 rounded border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all resize-none">{{ old('message') }}</textarea>
                            </div>

                            <div class="flex items-center justify-between gap-3 pt-1 border-t border-gray-100">
                                <p class="text-[9px] text-titan-navy/25">* {{ __('required fields') }}</p>
                                <button type="submit"
                                    class="inline-flex items-center gap-2 h-9 px-6 rounded bg-titan-red hover:bg-titan-navy text-white font-black text-[9px] uppercase tracking-[0.2em] transition-all group">
                                    {{ __('Submit Application') }}
                                    <x-lucide-arrow-right class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar: 4 cols -->
                <div class="lg:col-span-4 lg:sticky lg:top-28 h-fit space-y-3">

                    <!-- Quick apply -->
                    <div class="bg-titan-navy rounded-lg p-5">
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-white/30 mb-1">{{ __('Open Position') }}</p>
                        <p class="text-sm font-black text-white leading-tight mb-4">{{ $job['title'] }}</p>
                        <a href="#apply-form"
                            class="flex items-center justify-center gap-2 h-9 w-full rounded bg-titan-red text-white text-[9px] font-black uppercase tracking-[0.2em] hover:bg-white hover:text-titan-navy transition-all mb-2">
                            {{ __('Apply Now') }}<x-lucide-arrow-down class="w-3.5 h-3.5" />
                        </a>
                        <a href="mailto:careers@kimmex.com?subject={{ urlencode('Application: '.$job['title']) }}"
                            class="block text-center text-[9px] text-white/30 hover:text-white/60 font-bold uppercase tracking-[0.15em] transition-colors">
                            {{ __('or email us') }}
                        </a>
                    </div>

                    <!-- Job meta -->
                    <div class="bg-white border border-gray-100 rounded-lg p-5 space-y-3">
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-titan-navy/30 mb-3">{{ __('Job Details') }}</p>
                        @foreach([
                            ['lucide-map-pin',    $job['loc']],
                            ['lucide-briefcase',  $job['experience']],
                            ['lucide-clock',      $job['type']],
                            ['lucide-dollar-sign',$job['salary']],
                            ['lucide-calendar',   __('Posted').' '.$job['postedDate']],
                        ] as [$icon, $val])
                        <div class="flex items-center gap-2.5">
                            <x-dynamic-component :component="$icon" class="w-3.5 h-3.5 text-titan-red shrink-0" />
                            <span class="text-[11px] font-semibold text-titan-navy/60">{{ $val }}</span>
                        </div>
                        @endforeach
                    </div>

                    <!-- Share -->
                    <div class="bg-white border border-gray-100 rounded-lg p-5">
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-titan-navy/30 mb-3">{{ __('Share this role') }}</p>
                        <div class="flex gap-2" x-data="{ copied: false }">
                            <button @click="navigator.clipboard?.writeText(window.location.href); copied=true; setTimeout(()=>copied=false,2000)"
                                class="w-8 h-8 rounded border flex items-center justify-center transition-all"
                                :class="copied ? 'bg-titan-red border-titan-red text-white' : 'border-gray-200 text-titan-navy/40 hover:border-titan-red/30 hover:text-titan-red'">
                                <template x-if="!copied"><x-lucide-link class="w-3.5 h-3.5" /></template>
                                <template x-if="copied"><x-lucide-check class="w-3.5 h-3.5" /></template>
                            </button>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                class="w-8 h-8 rounded bg-social-facebook flex items-center justify-center text-white hover:brightness-110 transition-all">
                                <x-social-icon network="facebook" class="w-3.5 h-3.5" />
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                class="w-8 h-8 rounded bg-social-linkedin flex items-center justify-center text-white hover:brightness-110 transition-all">
                                <x-social-icon network="linkedin" class="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>

                    <!-- Back -->
                    <a href="{{ route('careers') }}"
                        class="flex items-center justify-center gap-2 h-9 w-full rounded border border-gray-200 text-titan-navy/40 text-[9px] font-black uppercase tracking-[0.2em] hover:border-gray-300 hover:text-titan-navy transition-colors">
                        <x-lucide-arrow-left class="w-3.5 h-3.5" />{{ __('All Openings') }}
                    </a>
                </div>

            </div>
        </section>

    </div>
@endif

</x-layouts.app>
