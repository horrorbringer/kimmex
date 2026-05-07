<x-layouts.app :title="__('Job Details')" description="Join our team of experts in the construction and investment industry.">

@php
    $job = \Illuminate\Support\Facades\Cache::remember("career_job_show_data_{$slug}_".app()->getLocale(), now()->addHours(12), function() use ($slug) {
        $jobDb = \App\Models\JobPosting::where('isActive', true)->where('slug', $slug)->first();
        if ($jobDb) {
            return [
                'id' => $jobDb->id,
                'title' => $jobDb->getTranslation('title', app()->getLocale()),
                'dept' => $jobDb->department ? $jobDb->department->getTranslation('name', app()->getLocale()) : __('General'),
                'loc' => $jobDb->getTranslation('location', app()->getLocale()),
                'type' => __($jobDb->type ?? 'FULL_TIME'),
                'salary' => $jobDb->getTranslation('salary', app()->getLocale()) ?: __('Negotiable'),
                'experience' => $jobDb->getTranslation('experience', app()->getLocale()) ?: __('2-3 Years'),
                'postedDate' => $jobDb->created_at ? $jobDb->created_at->format('M d, Y') : now()->format('M d, Y'),
                'description' => $jobDb->getTranslation('summary', app()->getLocale()),
                'responsibilities' => $jobDb->getTranslation('responsibilities', app()->getLocale()),
                'requirements' => $jobDb->getTranslation('requirements', app()->getLocale()),
                'benefits' => $jobDb->getTranslation('benefits', app()->getLocale()),
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
@endphp

@if(!$job)
    <div class="py-40 text-center bg-gray-50 min-h-screen">
         <div class="max-w-[1200px] mx-auto px-6">
            <h1 class="text-4xl font-black text-titan-navy mb-4">{{ __('Position Not Found') }}</h1>
            <p class="text-titan-navy/50 mb-8">{{ __('The role you are looking for may have been filled or the link is outdated.') }}</p>
            <a href="{{ route('careers') }}" class="inline-block bg-titan-navy text-white px-8 py-4 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-titan-red transition-all">
                {{ __('Return to Openings') }}
            </a>
         </div>
    </div>
@else
    <div class="bg-white min-h-screen font-sans text-titan-navy">
        
        <!-- HERO SECTION (Cinematic Image Background) -->
        <section class="relative bg-titan-navy overflow-hidden pt-52 pb-20 min-h-[380px] flex items-start">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0">
                <img src="/images/projects/Thumbnail-1.jpg" alt="{{ $job['title'] }}" class="w-full h-full object-cover opacity-60 scale-105" />
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/90 via-titan-navy/60 to-titan-navy/40"></div>
            </div>

            <div class="max-w-[1200px] mx-auto px-6 relative z-10 w-full">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-3 text-[9px] font-black uppercase tracking-[0.2em] text-white/30 mb-12">
                    <a href="{{ route('home') }}" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                    <span class="w-1 h-1 rounded-full bg-white/10"></span>
                    <a href="{{ route('careers') }}" class="hover:text-white transition-colors">{{ __('Careers') }}</a>
                    <span class="w-1 h-1 rounded-full bg-white/10"></span>
                    <span class="text-white/60">{{ $job['title'] }}</span>
                </nav>

                <div class="max-w-4xl">
                    <!-- Department Badge -->
                    <div class="inline-flex items-center gap-3 px-4 py-2 bg-white/5 rounded-lg border border-white/10 mb-8">
                        <div class="w-1.5 h-1.5 rounded-full bg-titan-red"></div>
                        <span class="text-[9px] font-black text-white/80 uppercase tracking-[0.2em]">{{ __($job['dept']) }}</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-7xl font-black text-white uppercase tracking-tighter mb-10 leading-[0.9]">{{ $job['title'] }}</h1>
                    
                    <!-- Metadata Grid -->
                    <div class="flex flex-wrap items-center gap-x-10 gap-y-6 text-white/50 text-[10px] font-black uppercase tracking-[0.2em]">
                        <div class="flex items-center gap-2.5">
                            <x-lucide-map-pin class="w-3.5 h-3.5 text-titan-red/50" />
                            {{ $job['loc'] }}
                        </div>
                        <div class="flex items-center gap-2.5">
                            <x-lucide-briefcase class="w-3.5 h-3.5 text-titan-red/50" />
                            {{ $job['experience'] }}
                        </div>
                        <div class="flex items-center gap-2.5">
                            <x-lucide-clock class="w-3.5 h-3.5 text-titan-red/50" />
                            {{ $job['type'] }}
                        </div>
                        <div class="flex items-center gap-2.5">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-titan-red/50" />
                            {{ __('Posted') }} {{ $job['postedDate'] }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTENT SECTION -->
        <section class="py-24 max-w-[1200px] mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                
                <!-- Main Content (8 cols) -->
                <div class="lg:col-span-8">
                    
                    <!-- Simplified Job Content Area -->
                    <div class="prose prose-lg max-w-none text-titan-navy/70 space-y-12">
                        
                        <!-- 01: Job Summary -->
                        @if($job['description'])
                        <section>
                            <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tighter mb-6">{{ __('Job Summary') }}</h2>
                            <div class="leading-relaxed text-lg prose-p:mb-4">{!! $job['description'] !!}</div>
                        </section>
                        @endif

                        <style>
                            .rich-text-content ul { list-style: none; padding: 0; }
                            .rich-text-content ul li { display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1rem; }
                            .rich-text-content ul li::before { content: ''; display: block; width: 6px; height: 6px; border-radius: 9999px; background-color: #ff2a00; margin-top: 10px; flex-shrink: 0; }
                            .rich-text-content p { margin-bottom: 1rem; }
                            .rich-text-content p:last-child { margin-bottom: 0; }
                        </style>

                        <!-- 02: Key Responsibilities -->
                        @if(!empty(trim(strip_tags($job['responsibilities']))))
                        <section>
                            <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tighter mb-6">{{ __('Key Responsibilities') }}</h2>
                            <div class="leading-relaxed text-lg rich-text-content">{!! $job['responsibilities'] !!}</div>
                        </section>
                        @endif

                        <!-- 03: Requirements -->
                        @if(!empty(trim(strip_tags($job['requirements']))))
                        <section>
                            <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tighter mb-6">{{ __('Requirements') }}</h2>
                            <div class="leading-relaxed text-lg rich-text-content">{!! $job['requirements'] !!}</div>
                        </section>
                        @endif

                        <!-- 04: Benefits -->
                        @if(!empty(trim(strip_tags($job['benefits']))))
                        <section class="pt-10 border-t border-gray-100">
                             <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tighter mb-8">{{ __('Benefits') }}</h2>
                             <div class="leading-relaxed text-lg rich-text-content">{!! $job['benefits'] !!}</div>
                        </section>
                        @endif

                    </div>

                    <!-- 05: Application Form (Aligned with Backend) -->
                    <section id="apply-form" class="pt-16 border-t border-gray-100 mt-20">
                    <!-- 05: Application Form (Aligned with Backend) -->
                    <section id="apply-form" class="pt-16 border-t border-gray-100 mt-20">
                        <div class="bg-white rounded-3xl p-8 md:p-12 border border-gray-100 shadow-sm transition-all">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-1 h-10 bg-titan-navy rounded-full"></div>
                                <div>
                                    <h3 class="text-2xl font-black text-titan-navy uppercase tracking-tight">{{ __('Apply for this Role') }}</h3>
                                    <p class="text-titan-navy/30 text-xs mt-1">{{ __('Complete the form below to submit your application for the') }} <span class="text-titan-navy font-bold">{{ $job['title'] }}</span></p>
                                </div>
                            </div>

                            @if(session('success'))
                                <div class="bg-green-50 text-green-700 p-6 rounded-2xl mb-10 text-xs font-bold border border-green-100 flex items-center gap-3 animate-fade-in-up">
                                    <x-lucide-check-circle class="w-5 h-5 text-green-500 shrink-0" />
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf
                                
                                <!-- Honeypot Field (Hidden from humans) -->
                                <div class="hidden" aria-hidden="true">
                                    <input type="text" name="website_url" tabindex="-1" autocomplete="off" />
                                </div>

                                <input type="hidden" name="job_id" value="{{ $job['id'] }}">
                                <input type="hidden" name="job_title" value="{{ $job['title'] }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-widest ml-1">{{ __('Full Name') }} <span class="text-titan-red">*</span></label>
                                        <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="{{ __('Enter your full name') }}" class="w-full bg-gray-50/50 border border-gray-100 rounded-xl px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:bg-white focus:ring-1 focus:ring-titan-navy/10 transition-all placeholder:text-gray-300 @error('full_name') border-titan-red @enderror" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-widest ml-1">{{ __('Email Address') }} <span class="text-titan-red">*</span></label>
                                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@email.com" class="w-full bg-gray-50/50 border border-gray-100 rounded-xl px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:bg-white focus:ring-1 focus:ring-titan-navy/10 transition-all placeholder:text-gray-300 @error('email') border-titan-red @enderror" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-widest ml-1">{{ __('Phone Number') }} <span class="text-titan-red">*</span></label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+855 12 345 678" class="w-full bg-gray-50/50 border border-gray-100 rounded-xl px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:bg-white focus:ring-1 focus:ring-titan-navy/10 transition-all placeholder:text-gray-300 @error('phone') border-titan-red @enderror" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-widest ml-1">{{ __('Resume / CV') }} <span class="text-titan-red">*</span></label>
                                        <div class="relative w-full h-[54px]" x-data="{ fileName: '' }">
                                            <input type="file" name="resume" required class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full" accept=".pdf,.doc,.docx" @change="fileName = $event.target.files[0]?.name || ''" />
                                            <div class="w-full h-full bg-gray-50/50 border border-gray-100 rounded-xl px-5 py-4 flex items-center justify-between text-titan-navy/30 group-hover:border-titan-navy/20 transition-all overflow-hidden @error('resume') border-titan-red @enderror">
                                                <span class="text-sm font-semibold truncate" :class="fileName ? 'text-titan-navy' : 'text-gray-300'" x-text="fileName || '{{ __('Choose File (PDF, DOCX)') }}'"></span>
                                                <x-lucide-upload class="w-4 h-4 text-titan-navy/30 shrink-0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-widest ml-1">{{ __('Cover Letter / Message') }}</label>
                                    <textarea name="message" rows="4" placeholder="{{ __('Briefly introduce yourself and why you are interested in this role...') }}" class="w-full bg-gray-50/50 border border-gray-100 rounded-xl px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:bg-white focus:ring-1 focus:ring-titan-navy/10 transition-all resize-none placeholder:text-gray-300"></textarea>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" class="w-full bg-titan-navy text-white py-5 rounded-2xl font-black text-[13px] uppercase tracking-widest hover:bg-titan-red transition-all shadow-xl shadow-titan-navy/10 flex items-center justify-center gap-4 group">
                                        {{ __('Submit My Application') }}
                                        <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                                    </button>
                                </div>

                                <p class="text-center text-[9px] text-titan-navy/20 font-bold uppercase tracking-[0.2em] pt-4">
                                    {{ __('By submitting, you agree to our privacy policy regarding recruitment data.') }}
                                </p>
                            </form>
                        </div>
                    </section>
                    </section>
                </div>

                <!-- Sidebar (4 cols) -->
                <div class="lg:col-span-4 lg:sticky lg:top-[120px] h-fit space-y-8">
                    
                    <!-- Quick Apply Box -->
                    <div class="p-8 rounded-3xl bg-white border border-gray-100 shadow-sm relative overflow-hidden group">
                        <h3 class="text-2xl font-black text-titan-navy uppercase tracking-tight mb-4">{{ __('Apply for this position') }}</h3>
                        <p class="text-titan-navy/40 text-xs leading-relaxed mb-8">{{ __('Join a team of visionaries shaping the skyline of Cambodia. Submit your profile today.') }}</p>
                        
                        <a href="#apply-form" class="w-full bg-titan-navy text-white py-5 rounded-2xl font-black text-[13px] uppercase tracking-widest hover:bg-titan-red transition-all flex items-center justify-center gap-4 shadow-xl shadow-titan-navy/5 mb-4 px-6 border border-titan-navy">
                            {{ __('Jump to Form') }}
                            <x-lucide-arrow-down class="w-4 h-4 animate-bounce" />
                        </a>
                        
                        <a href="mailto:careers@kimmex.com?subject=Application for {{ $job['title'] }}" class="text-center block text-[10px] text-titan-navy/30 hover:text-titan-red transition-colors font-bold uppercase tracking-widest w-full py-2">{{ __('Or apply via email') }}</a>
                    </div>

                    <!-- Share Role -->
                    <div class="p-8 border border-gray-100 rounded-3xl space-y-6 bg-white shadow-sm">
                         <h4 class="text-[10px] font-black text-titan-navy/30 uppercase tracking-[0.2em]">{{ __('Share this role') }}</h4>
                          <div class="flex gap-3">
                             <a href="javascript:void(0)" onclick="navigator.clipboard.writeText(window.location.href); alert('{{ __('Link copied to clipboard!') }}')" 
                                 class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-titan-navy hover:bg-titan-navy hover:text-white transition-all shadow-lg shadow-gray-200">
                                 <x-lucide-link class="w-4 h-4" />
                             </a>
                             <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                 class="w-10 h-10 rounded-xl bg-social-facebook flex items-center justify-center text-white hover:brightness-110 transition-all shadow-lg shadow-social-facebook/20">
                                 <x-lucide-facebook class="w-4 h-4" />
                             </a>
                              <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                 class="w-10 h-10 rounded-xl bg-social-linkedin flex items-center justify-center text-white hover:brightness-110 transition-all shadow-lg shadow-social-linkedin/20">
                                 <x-lucide-linkedin class="w-4 h-4" />
                             </a>
                          </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="p-8 border border-gray-100 rounded-3xl bg-gray-50/50">
                        <h4 class="text-xs font-black text-titan-navy uppercase tracking-widest mb-6">{{ __('Kimmex Recruitment') }}</h4>
                        <p class="text-xs text-titan-navy/40 leading-relaxed mb-6">
                            {{ __('Kimmex is an equal opportunity employer. We celebrate diversity and are committed to creating an inclusive environment for all employees.') }}
                        </p>
                        <a href="{{ route('about') }}" class="text-[10px] font-black underline tracking-widest uppercase text-titan-red">{{ __('Learn about our culture') }}</a>
                    </div>

                </div>

            </div>
        </section>

    </div>
@endif

</x-layouts.app>
