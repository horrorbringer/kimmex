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

    $renderRichText = function (?string $content) {
        $content = trim((string) $content);

        if ($content === '') {
            return '';
        }

        if (preg_match('/<\s*(ul|ol|li|p|h[1-6]|blockquote|table|img|br)\b/i', $content)) {
            return $content;
        }

        $lines = preg_split('/\R+/', $content) ?: [];
        $lines = array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));

        if (count($lines) > 1) {
            $items = array_map(function ($line) {
                return '<li>' . e($line) . '</li>';
            }, $lines);

            return '<ul>' . implode('', $items) . '</ul>';
        }

        return '<p>' . e($content) . '</p>';
    };

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
        
        <!-- HERO SECTION (Editorial Light Layout) -->
        <section class="relative overflow-hidden bg-white pt-32 pb-14 md:py-28">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(220,38,38,0.05),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(15,23,42,0.03),transparent_32%)]"></div>

            <div class="max-w-[1000px] mx-auto px-4 sm:px-6 relative z-10">
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <a href="{{ route('careers') }}" class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full border border-slate-200 bg-white text-[10px] font-bold uppercase tracking-[0.14em] sm:tracking-[0.18em] text-titan-navy/65 hover:text-titan-red hover:border-titan-red/20 transition-all shadow-sm">
                        <x-lucide-arrow-left class="w-3.5 h-3.5" />
                        {{ __('Return to Openings') }}
                    </a>
                    <nav class="hidden sm:flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.18em] text-titan-navy/35">
                        <a href="{{ route('home') }}" class="hover:text-titan-navy transition-colors">{{ __('Home') }}</a>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <a href="{{ route('careers') }}" class="hover:text-titan-navy transition-colors">{{ __('Careers') }}</a>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="text-titan-navy/60 max-w-[18rem] truncate">{{ $job['title'] }}</span>
                    </nav>
                </div>

                <div class="inline-flex max-w-full items-center gap-3 px-3 sm:px-4 py-2.5 bg-slate-50 rounded-full border border-slate-200 mb-6">
                    <div class="w-2 h-2 rounded-full bg-titan-red"></div>
                    <span class="min-w-0 truncate text-[10px] font-bold text-titan-navy/70 uppercase tracking-[0.14em] sm:tracking-[0.18em]">{{ __($job['dept']) }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-titan-navy tracking-normal mb-5 leading-tight md:leading-[0.94] max-w-4xl break-words">
                    {{ $job['title'] }}
                </h1>

                @if($heroSummary)
                    <p class="max-w-2xl text-titan-navy/60 text-sm sm:text-base md:text-lg leading-relaxed mb-8">
                        {{ $heroSummary }}
                    </p>
                @endif

                <div class="grid grid-cols-1 sm:flex sm:flex-wrap gap-2 sm:gap-4 text-titan-navy/70 text-[11px] font-bold">
                    <div class="inline-flex min-w-0 items-center gap-2 px-3 sm:px-4 py-2 rounded-full bg-slate-50 border border-slate-200 shadow-sm">
                        <x-lucide-map-pin class="w-3.5 h-3.5 text-titan-red" />
                        <span class="truncate">{{ $job['loc'] }}</span>
                    </div>
                    <div class="inline-flex min-w-0 items-center gap-2 px-3 sm:px-4 py-2 rounded-full bg-slate-50 border border-slate-200 shadow-sm">
                        <x-lucide-briefcase class="w-3.5 h-3.5 text-titan-red" />
                        <span class="truncate">{{ $job['experience'] }}</span>
                    </div>
                    <div class="inline-flex min-w-0 items-center gap-2 px-3 sm:px-4 py-2 rounded-full bg-slate-50 border border-slate-200 shadow-sm">
                        <x-lucide-clock class="w-3.5 h-3.5 text-titan-red" />
                        <span class="truncate">{{ $job['type'] }}</span>
                    </div>
                    <div class="hidden sm:inline-flex min-w-0 items-center gap-2 px-3 sm:px-4 py-2 rounded-full bg-slate-50 border border-slate-200 shadow-sm">
                        <x-lucide-calendar class="w-3.5 h-3.5 text-titan-red" />
                        <span class="truncate">{{ __('Posted') }} {{ $job['postedDate'] }}</span>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="#apply-form" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-titan-navy text-white px-5 py-3.5 font-bold text-[11px] uppercase tracking-[0.16em] sm:tracking-[0.18em] hover:bg-titan-red transition-all shadow-sm">
                        {{ __('Jump to Form') }}
                        <x-lucide-arrow-down class="w-4 h-4" />
                    </a>
                    <a href="mailto:careers@kimmex.com?subject=Application for {{ $job['title'] }}" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3.5 font-bold text-[11px] uppercase tracking-[0.16em] sm:tracking-[0.18em] text-titan-navy hover:border-titan-red/20 hover:text-titan-red transition-all shadow-sm">
                        {{ __('Or apply via email') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- CONTENT SECTION -->
        <section class="py-12 md:py-20 max-w-[1200px] mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 xl:gap-12">
                
                <!-- Main Content (8 cols) -->
                <div class="lg:col-span-8">
                    
                    <!-- Simplified Job Content Area -->
                    <div class="prose prose-lg max-w-none text-titan-navy/70 space-y-5 md:space-y-8">
                        
                        @if($job['description'])
                        <section class="rounded-xl border border-slate-200 bg-white p-5 md:p-8 shadow-sm">
                            <div class="flex items-center gap-3 mb-4 md:mb-5">
                                <div class="w-8 md:w-10 h-px bg-titan-red"></div>
                                <h2 class="text-lg md:text-xl font-bold text-titan-navy">{{ __('Job Summary') }}</h2>
                            </div>
                            <div class="rich-text-content">{!! $renderParagraphContent($job['description'] ?? '') !!}</div>
                        </section>
                        @endif

                        <style>
                            .rich-text-content {
                                color: rgb(15 23 42 / 0.78);
                                line-height: 1.85;
                                font-size: 1rem;
                            }
                            .rich-text-content > :first-child { margin-top: 0; }
                            .rich-text-content > :last-child { margin-bottom: 0; }
                            .rich-text-content h1,
                            .rich-text-content h2,
                            .rich-text-content h3,
                            .rich-text-content h4 {
                                color: rgb(15 23 42);
                                font-weight: 700;
                                line-height: 1.2;
                                margin: 1.75rem 0 0.9rem;
                            }
                            .rich-text-content h1 { font-size: 1.6rem; }
                            .rich-text-content h2 { font-size: 1.35rem; }
                            .rich-text-content h3 { font-size: 1.15rem; }
                            .rich-text-content p,
                            .rich-text-content ul,
                            .rich-text-content ol,
                            .rich-text-content blockquote,
                            .rich-text-content table {
                                margin-bottom: 1rem;
                            }
                            .rich-text-content ul,
                            .rich-text-content ol {
                                padding-left: 1.25rem;
                            }
                            .rich-text-content ul { list-style: disc; }
                            .rich-text-content ol { list-style: decimal; }
                            .rich-text-content li { margin: 0.35rem 0; }
                            .rich-text-content a {
                                color: rgb(220 38 38);
                                text-decoration: underline;
                                text-underline-offset: 0.15em;
                            }
                            .rich-text-content strong { color: rgb(15 23 42); font-weight: 700; }
                            .rich-text-content blockquote {
                                border-left: 3px solid rgb(220 38 38);
                                background: rgb(248 250 252);
                                padding: 0.85rem 1rem;
                                border-radius: 0.75rem;
                                color: rgb(51 65 85);
                            }
                            .rich-text-content img {
                                width: 100%;
                                height: auto;
                                border-radius: 1rem;
                                margin: 1.25rem 0;
                            }
                            .rich-text-content table {
                                width: 100%;
                                border-collapse: collapse;
                                overflow: hidden;
                                border-radius: 1rem;
                            }
                            .rich-text-content th,
                            .rich-text-content td {
                                border: 1px solid rgb(226 232 240);
                                padding: 0.75rem 0.9rem;
                                text-align: left;
                                vertical-align: top;
                            }
                            .rich-text-content th {
                                background: rgb(248 250 252);
                                color: rgb(15 23 42);
                                font-weight: 700;
                            }
                        </style>

                        @if(!empty(trim(strip_tags($job['responsibilities']))))
                        <section class="rounded-xl border border-slate-200 bg-slate-50/70 p-5 md:p-8 shadow-sm">
                            <div class="flex items-center gap-3 mb-4 md:mb-5">
                                <div class="w-8 md:w-10 h-px bg-titan-red"></div>
                                <h2 class="text-lg md:text-xl font-bold text-titan-navy">{{ __('Key Responsibilities') }}</h2>
                            </div>
                            <div class="rich-text-content">{!! $renderRichText($job['responsibilities'] ?? '') !!}</div>
                        </section>
                        @endif

                        @if(!empty(trim(strip_tags($job['requirements']))))
                        <section class="rounded-xl border border-slate-200 bg-white p-5 md:p-8 shadow-sm">
                            <div class="flex items-center gap-3 mb-4 md:mb-5">
                                <div class="w-8 md:w-10 h-px bg-titan-red"></div>
                                <h2 class="text-lg md:text-xl font-bold text-titan-navy">{{ __('Requirements') }}</h2>
                            </div>
                            <div class="rich-text-content">{!! $renderRichText($job['requirements'] ?? '') !!}</div>
                        </section>
                        @endif

                        @if(!empty(trim(strip_tags($job['benefits']))))
                        <section class="rounded-xl border border-titan-red/10 bg-titan-red/5 p-5 md:p-8 shadow-sm">
                             <div class="flex items-center gap-3 mb-4 md:mb-5">
                                <div class="w-8 md:w-10 h-px bg-titan-red"></div>
                                <h2 class="text-lg md:text-xl font-bold text-titan-navy">{{ __('Benefits') }}</h2>
                            </div>
                             <div class="rich-text-content">{!! $renderRichText($job['benefits'] ?? '') !!}</div>
                        </section>
                        @endif

                    </div>

                    <section id="apply-form" class="pt-8 md:pt-12 mt-10 md:mt-14">
                        <div class="bg-white rounded-xl p-5 md:p-10 border border-slate-200 shadow-sm transition-all">
                            <div class="flex items-start sm:items-center gap-3 sm:gap-4 mb-6">
                                <div class="w-8 sm:w-10 h-px bg-titan-red mt-3 sm:mt-0 shrink-0"></div>
                                <div>
                                    <h3 class="text-xl md:text-2xl font-bold text-titan-navy">{{ __('Apply for this Role') }}</h3>
                                    <p class="text-titan-navy/40 text-sm mt-1">{{ __('Complete the form below to submit your application.') }}</p>
                                </div>
                            </div>

                            @if(session('success'))
                                <div class="bg-green-50 text-green-700 p-5 rounded-lg mb-8 text-xs font-bold border border-green-100 flex items-center gap-3 animate-fade-in-up">
                                    <x-lucide-check-circle class="w-5 h-5 text-green-500 shrink-0" />
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                
                                <!-- Honeypot Field (Hidden from humans) -->
                                <div class="hidden" aria-hidden="true">
                                    <input type="text" name="website_url" tabindex="-1" autocomplete="off" />
                                </div>

                                <input type="hidden" name="job_id" value="{{ $job['id'] }}">
                                <input type="hidden" name="job_title" value="{{ $job['title'] }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-titan-navy/40 uppercase tracking-[0.18em] ml-1">{{ __('Full Name') }} <span class="text-titan-red">*</span></label>
                                        <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="{{ __('Enter your full name') }}" class="w-full bg-gray-50/50 border border-gray-100 rounded px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:bg-white focus:ring-1 focus:ring-titan-navy/10 transition-all placeholder:text-gray-300 @error('full_name') border-titan-red @enderror" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-titan-navy/40 uppercase tracking-[0.18em] ml-1">{{ __('Email Address') }} <span class="text-titan-red">*</span></label>
                                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@email.com" class="w-full bg-gray-50/50 border border-gray-100 rounded px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:bg-white focus:ring-1 focus:ring-titan-navy/10 transition-all placeholder:text-gray-300 @error('email') border-titan-red @enderror" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-titan-navy/40 uppercase tracking-[0.18em] ml-1">{{ __('Phone Number') }} <span class="text-titan-red">*</span></label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+855 12 345 678" class="w-full bg-gray-50/50 border border-gray-100 rounded px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:bg-white focus:ring-1 focus:ring-titan-navy/10 transition-all placeholder:text-gray-300 @error('phone') border-titan-red @enderror" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-titan-navy/40 uppercase tracking-[0.18em] ml-1">{{ __('Resume / CV') }} <span class="text-titan-red">*</span></label>
                                        <div class="relative w-full h-[54px]" x-data="{ fileName: '' }">
                                            <input type="file" name="resume" required class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full" accept=".pdf,.doc,.docx" @change="fileName = $event.target.files[0]?.name || ''" />
                                            <div class="w-full h-full bg-gray-50/50 border border-gray-100 rounded px-5 py-4 flex items-center justify-between text-titan-navy/30 group-hover:border-titan-navy/20 transition-all overflow-hidden @error('resume') border-titan-red @enderror">
                                                <span class="text-sm font-semibold truncate" :class="fileName ? 'text-titan-navy' : 'text-gray-300'" x-text="fileName || '{{ __('Choose File (PDF, DOCX)') }}'"></span>
                                                <x-lucide-upload class="w-4 h-4 text-titan-navy/30 shrink-0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-titan-navy/40 uppercase tracking-[0.18em] ml-1">{{ __('Cover Letter / Message') }}</label>
                                    <textarea name="message" rows="4" placeholder="{{ __('Briefly introduce yourself and why you are interested in this role...') }}" class="w-full bg-gray-50/50 border border-gray-100 rounded px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:bg-white focus:ring-1 focus:ring-titan-navy/10 transition-all resize-none placeholder:text-gray-300"></textarea>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" class="w-full bg-titan-navy text-white py-4 rounded-xl font-bold text-[12px] uppercase tracking-[0.18em] hover:bg-titan-red transition-all shadow-sm flex items-center justify-center gap-4 group">
                                        {{ __('Submit My Application') }}
                                        <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                                    </button>
                                </div>

                                <p class="text-center text-[9px] text-titan-navy/25 font-bold uppercase tracking-[0.2em] pt-4">
                                    {{ __('By submitting, you agree to our privacy policy regarding recruitment data.') }}
                                </p>
                            </form>
                        </div>
                    </section>
                </div>

                <!-- Sidebar (4 cols) -->
                <div class="lg:col-span-4 lg:sticky lg:top-[120px] h-fit space-y-5">
                    
                    <!-- Quick Apply Box -->
                    <div class="p-5 md:p-6 rounded-xl bg-white border border-slate-200 shadow-sm relative overflow-hidden group">
                        <h3 class="text-xl font-bold text-titan-navy mb-2">{{ __('Apply for this position') }}</h3>
                        <p class="text-titan-navy/45 text-sm leading-relaxed mb-5">{{ __('Join a team of visionaries shaping the skyline of Cambodia. Submit your profile today.') }}</p>
                        
                        <a href="#apply-form" class="w-full bg-titan-navy text-white py-4 rounded-xl font-bold text-[12px] uppercase tracking-[0.18em] hover:bg-titan-red transition-all flex items-center justify-center gap-3 shadow-sm mb-3 px-6 border border-titan-navy">
                            {{ __('Jump to Form') }}
                            <x-lucide-arrow-down class="w-4 h-4" />
                        </a>
                        
                        <a href="mailto:careers@kimmex.com?subject=Application for {{ $job['title'] }}" class="text-center block text-[10px] text-titan-navy/35 hover:text-titan-red transition-colors font-bold uppercase tracking-[0.18em] w-full py-2">{{ __('Or apply via email') }}</a>
                    </div>

                    <!-- Share Role -->
                    <div class="hidden sm:block p-5 md:p-6 border border-slate-200 rounded-xl space-y-4 bg-white shadow-sm">
                         <h4 class="text-[10px] font-bold text-titan-navy/35 uppercase tracking-[0.2em]">{{ __('Share this role') }}</h4>
                          <div class="flex gap-3">
                             <div x-data="{ 
                                 copied: false, 
                                 copyLink() {
                                     const url = window.location.href;
                                     if (navigator.clipboard && navigator.clipboard.writeText) {
                                         navigator.clipboard.writeText(url).catch(() => {});
                                     } else {
                                         const el = document.createElement('textarea');
                                         el.value = url;
                                         document.body.appendChild(el);
                                         el.select();
                                         document.execCommand('copy');
                                         document.body.removeChild(el);
                                     }
                                     this.copied = true;
                                     setTimeout(() => this.copied = false, 2000);
                                 }
                             }" class="relative">
                                 <button @click="copyLink()"
                                     class="w-10 h-10 flex items-center justify-center rounded transition-all duration-300 transform hover:-translate-y-1 active:scale-95 shadow-lg group/link"
                                     :class="copied ? 'bg-titan-red text-white border-titan-red' : 'bg-gray-100 text-titan-navy border border-transparent hover:border-titan-red/30 hover:text-titan-red'">
                                     <x-lucide-link class="w-4 h-4" x-show="!copied" />
                                     <x-lucide-check class="w-4 h-4" x-show="copied" x-cloak />
                                 </button>

                                 <!-- Tooltip -->
                                 <div x-show="copied" 
                                      x-transition:enter="transition ease-out duration-300"
                                      x-transition:enter-start="opacity-0 translate-y-2"
                                      x-transition:enter-end="opacity-100 translate-y-0"
                                      x-transition:leave="transition ease-in duration-200"
                                      x-transition:leave-start="opacity-100 translate-y-0"
                                      x-transition:leave-end="opacity-0 translate-y-2"
                                      class="absolute -top-10 left-1/2 -translate-x-1/2 px-3 py-1.5 bg-titan-navy text-white text-[9px] font-black uppercase tracking-widest rounded whitespace-nowrap shadow-xl z-50"
                                      style="display: none;">
                                     {{ __('Copied!') }}
                                 </div>
                             </div>
                             <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                 class="w-10 h-10 rounded bg-social-facebook flex items-center justify-center text-white hover:brightness-110 transition-all shadow-lg shadow-social-facebook/20">
                                 <x-lucide-facebook class="w-4 h-4" />
                             </a>
                              <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                 class="w-10 h-10 rounded bg-social-linkedin flex items-center justify-center text-white hover:brightness-110 transition-all shadow-lg shadow-social-linkedin/20">
                                 <x-lucide-linkedin class="w-4 h-4" />
                             </a>
                          </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="p-5 md:p-6 border border-slate-200 rounded-xl bg-slate-50/70">
                        <h4 class="text-xs font-bold text-titan-navy uppercase tracking-[0.18em] mb-5">{{ __('Kimmex Recruitment') }}</h4>
                        <p class="text-xs text-titan-navy/45 leading-relaxed mb-5">
                            {{ __('Kimmex is an equal opportunity employer. We celebrate diversity and are committed to creating an inclusive environment for all employees.') }}
                        </p>
                        <a href="{{ route('about') }}" class="text-[10px] font-bold underline tracking-[0.18em] uppercase text-titan-red">{{ __('Learn about our culture') }}</a>
                    </div>

                </div>

            </div>
        </section>

    </div>
@endif

</x-layouts.app>
