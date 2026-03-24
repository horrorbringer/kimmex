<x-layouts.app :title="__('Job Details')" description="Join our team of experts in the construction and investment industry.">

@php
    $jobs = [
        [
            'id' => 'senior-civil-engineer',
            'title' => __('Senior Civil Engineer'),
            'dept' => 'Engineering',
            'loc' => __('Phnom Penh'),
            'type' => __('Full-time'),
            'salary' => __('Negotiable'),
            'experience' => __('2-3 Years'),
            'postedDate' => '3/6/2026',
            'tags' => [__('Engineering')],
            'summary' => __('We are seeking a highly experienced Senior Civil Engineer to lead complex structural projects.'),
            'description' => __('Lead engineering teams through complex structural projects, ensuring precision, safety, and operational excellence from design to completion.'),
            'responsibilities' => [
                __('Lead structural design and analysis for major construction projects.'),
                __('Coordinate with project managers and stakeholders to ensure timeline adherence.'),
                __('Review and approve structural engineering drawings and calculations.'),
                __('Ensure compliance with local building codes, safety regulations, and quality standards.'),
                __('Provide technical guidance and mentorship to junior engineering staff.')
            ],
            'requirements' => [
                __('Bachelor’s or Master’s degree in Civil Engineering or related field.'),
                __("At least 5 years of experience in structural engineering within the construction sector."),
                __('Proficiency in AutoCAD, Civil 3D, and structural analysis software.'),
                __('Strong leadership, communication, and project management skills.'),
                __('Registered Professional Engineer (PE) license is a major plus.')
            ],
            'benefits' => [
                __('Competitive salary package with annual performance-based bonuses.'),
                __('Comprehensive health and life insurance coverage.'),
                __('Opportunities for professional development and certified training.'),
                __('Relocation support and housing allowance for offshore projects.'),
                __('A dynamic, high-growth environment with clear career progression paths.')
            ]
        ],
        [
            'id' => 'operation-assistant',
            'title' => __('Operation Assistant'),
            'dept' => 'Operations',
            'loc' => __('Tamork'),
            'type' => __('Full-time'),
            'salary' => __('Negotiable'),
            'experience' => __('2-3 Years'),
             'postedDate' => '3/6/2026',
            'tags' => [__('Operations')],
            'summary' => __('Support daily operations and project coordination across multiple sites.'),
             'description' => __('Facilitate seamless site operations by coordinating resources, schedules, and communication across multiple high-stakes construction projects.'),
            'responsibilities' => [
                __('Coordinate daily site activities and resource allocation.'),
                __('Maintain detailed operational schedules and progress reports.'),
                __('Bridge communication between field teams and regional headquarters.'),
                __('Inventory management and procurement support for operational supplies.')
            ],
            'requirements' => [
                __('Degree in Business Administration, Operations, or related field.'),
                __('Minimum 2 years of experience in field operations or site management.'),
                __('Strong organizational and problem-solving abilities.'),
                __('Willingness to travel to various project sites across Cambodia.')
            ],
            'benefits' => [
                __('Competitive base salary with travel allowances.'),
                __('Paid time off and flexible scheduling opportunities.'),
                __('Health insurance and wellness programs.'),
                __('Ongoing mentorship from seasoned operational leaders.')
            ]
        ],
        [
            'id' => 'site-supervisor',
            'title' => __('Site Supervisor'),
            'dept' => 'Operations',
            'loc' => __('Sihanoukville'),
            'type' => __('Full-time'),
            'salary' => __('Negotiable'),
            'experience' => __('3-5 Years'),
            'postedDate' => '3/10/2026',
            'tags' => [__('Construction')],
            'summary' => __('Expert oversight of construction sites and project coordination.'),
             'description' => __('Direct site operations to ensure high-quality construction delivery, maintaining strict safety standards and operational efficiency.'),
            'responsibilities' => [
                __('Directly oversee construction activities on-site.'),
                __('Enforce strict occupational health and safety (OHS) protocols.'),
                __("Perform quality control inspections at all stages of the project."),
                __('Supervise sub-contractors and ensure workmanship standards are met.')
            ],
            'requirements' => [
                 __('Certified technical background or degree in Construction Management.'),
                 __('At least 3 years as a supervisor or lead on major building sites.'),
                 __('Demonstrable knowledge of modern construction methods and materials.'),
                 __('Excellent command of safety standards and regulatory requirements.')
            ],
            'benefits' => [
                __('High-growth salary path with project completion bonuses.'),
                __('On-site accommodation and meal allowances provided.'),
                __('Career advancement into site management or operations lead.'),
                __('Comprehensive medical support and accident insurance.')
            ]
        ]
    ];

    $job = collect($jobs)->where('id', $id)->first();
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
    <div class="bg-white min-h-screen font-sans text-titan-navy pt-[100px]">
        
        <!-- HERO SECTION (Cinematic Image Background) -->
        <section class="relative bg-titan-navy overflow-hidden py-32 min-h-[480px] flex items-center">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0">
                <img src="/images/projects/Thumbnail-1.jpg" alt="{{ $job['title'] }}" class="w-full h-full object-cover opacity-30 blur-[2px] scale-105" />
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy via-titan-navy/95 to-titan-navy/80"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-titan-navy/20"></div>
            </div>

            <div class="max-w-[1200px] mx-auto px-6 relative z-10 w-full">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-12">
                    <a href="{{ route('home') }}" class="hover:text-titan-red transition-colors">{{ __('Home') }}</a>
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <a href="{{ route('careers') }}" class="hover:text-titan-red transition-colors">{{ __('Careers') }}</a>
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <span class="text-white/80">{{ $job['title'] }}</span>
                </nav>

                <div class="max-w-4xl">
                    <!-- Department Status Badge -->
                    <div class="inline-flex items-center gap-3 px-4 py-2 bg-white/10 backdrop-blur-md rounded-lg border border-white/10 mb-8">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-titan-red opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-titan-red"></span>
                        </span>
                        <span class="text-[10px] font-black text-white uppercase tracking-[0.2em]">{{ __($job['dept']) }}</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-7xl font-black text-white uppercase tracking-tighter mb-10 leading-[0.85]">{{ $job['title'] }}</h1>
                    
                    <!-- Metadata Grid -->
                    <div class="flex flex-wrap items-center gap-x-10 gap-y-6 text-white/60 text-[11px] font-bold uppercase tracking-[0.15em]">
                        <div class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-titan-red/20 transition-all">
                                <x-lucide-map-pin class="w-4 h-4 text-titan-red" />
                            </div>
                            {{ $job['loc'] }}
                        </div>
                        <div class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-titan-red/20 transition-all">
                                <x-lucide-briefcase class="w-4 h-4 text-titan-red" />
                            </div>
                            {{ $job['experience'] }}
                        </div>
                        <div class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-titan-red/20 transition-all">
                                <x-lucide-clock class="w-4 h-4 text-titan-red" />
                            </div>
                            {{ $job['type'] }}
                        </div>
                        <div class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-titan-red/20 transition-all">
                                <x-lucide-calendar class="w-4 h-4 text-titan-red" />
                            </div>
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
                        <section>
                            <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tighter mb-6">{{ __('Job Summary') }}</h2>
                            <p class="leading-relaxed text-lg">{{ $job['description'] }}</p>
                        </section>

                        <!-- 02: Key Responsibilities -->
                        <section>
                            <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tighter mb-6">{{ __('Key Responsibilities') }}</h2>
                            <ul class="list-none space-y-4 p-0">
                                @foreach($job['responsibilities'] as $item)
                                    <li class="flex gap-4 items-start">
                                        <div class="w-1.5 h-1.5 rounded-full bg-titan-red mt-2.5 shrink-0"></div>
                                        <span class="text-base">{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>

                        <!-- 03: Requirements -->
                        <section>
                            <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tighter mb-6">{{ __('Requirements') }}</h2>
                            <ul class="list-none space-y-4 p-0">
                                @foreach($job['requirements'] as $req)
                                    <li class="flex gap-4 items-start">
                                        <div class="w-1.5 h-1.5 rounded-full bg-titan-red mt-2.5 shrink-0"></div>
                                        <span class="text-base">{{ $req }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>

                        <!-- 04: Benefits -->
                        <section class="pt-10 border-t border-gray-100">
                             <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tighter mb-8">{{ __('Benefits') }}</h2>
                             <ul class="list-none space-y-4 p-0">
                                @foreach($job['benefits'] as $benefit)
                                    <li class="flex gap-4 items-start">
                                        <div class="w-1.5 h-1.5 rounded-full bg-titan-red mt-2.5 shrink-0"></div>
                                        <span class="text-base">{{ $benefit }}</span>
                                    </li>
                                @endforeach
                             </ul>
                        </section>

                    </div>
                </div>

                    </div>

                    <!-- 05: Application Form (Aligned with Backend) -->
                    <section id="apply-form" class="pt-16 border-t border-gray-100 mt-20">
                        <div class="bg-gray-50 rounded-3xl p-8 md:p-12 border border-gray-100 shadow-sm transition-all hover:shadow-md">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-1.5 h-10 bg-titan-red rounded-full"></div>
                                <div>
                                    <h3 class="text-2xl font-black text-titan-navy uppercase tracking-tight">{{ __('Apply for this Role') }}</h3>
                                    <p class="text-titan-navy/40 text-sm mt-1">{{ __('Complete the form below to submit your application for the') }} <span class="text-titan-red font-bold">{{ $job['title'] }}</span> {{ __('position.') }}</p>
                                </div>
                            </div>

                            @if(session('success'))
                                <div class="bg-green-50 text-green-700 p-6 rounded-2xl mb-10 text-sm font-bold border border-green-100 flex items-center gap-3 animate-fade-in-up">
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
                                        <label class="block text-[11px] font-black text-titan-navy uppercase tracking-widest ml-1">{{ __('Full Name') }} <span class="text-titan-red">*</span></label>
                                        <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="{{ __('Enter your full name') }}" class="w-full bg-white border border-gray-100 rounded-xl px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:ring-2 focus:ring-titan-red/10 focus:border-titan-red/20 transition-all placeholder:text-gray-300 @error('full_name') border-titan-red @enderror" />
                                        @error('full_name') <p class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-black text-titan-navy uppercase tracking-widest ml-1">{{ __('Email Address') }} <span class="text-titan-red">*</span></label>
                                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@email.com" class="w-full bg-white border border-gray-100 rounded-xl px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:ring-2 focus:ring-titan-red/10 focus:border-titan-red/20 transition-all placeholder:text-gray-300 @error('email') border-titan-red @enderror" />
                                        @error('email') <p class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-black text-titan-navy uppercase tracking-widest ml-1">{{ __('Phone Number') }} <span class="text-titan-red">*</span></label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+855 12 345 678" class="w-full bg-white border border-gray-100 rounded-xl px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:ring-2 focus:ring-titan-red/10 focus:border-titan-red/20 transition-all placeholder:text-gray-300 @error('phone') border-titan-red @enderror" />
                                        @error('phone') <p class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-black text-titan-navy uppercase tracking-widest ml-1">{{ __('Resume / CV') }} <span class="text-titan-red">*</span></label>
                                        <div class="relative w-full h-[54px]" x-data="{ fileName: '' }">
                                            <input type="file" name="resume" required class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full" accept=".pdf,.doc,.docx" @change="fileName = $event.target.files[0]?.name || ''" />
                                            <div class="w-full h-full bg-white border border-gray-100 rounded-xl px-5 py-4 flex items-center justify-between text-titan-navy/30 group-hover:border-titan-red/20 transition-all overflow-hidden @error('resume') border-titan-red @enderror">
                                                <span class="text-sm font-semibold truncate" :class="fileName ? 'text-titan-navy' : 'text-gray-300'" x-text="fileName || '{{ __('Choose File (PDF, DOCX)') }}'"></span>
                                                <x-lucide-upload class="w-4 h-4 text-titan-red shrink-0" />
                                            </div>
                                            @error('resume') <p class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-titan-navy uppercase tracking-widest ml-1">{{ __('Cover Letter / Message') }}</label>
                                    <textarea name="message" rows="4" placeholder="{{ __('Briefly introduce yourself and why you are interested in this role...') }}" class="w-full bg-white border border-gray-100 rounded-xl px-5 py-4 text-sm font-semibold text-titan-navy outline-none focus:ring-2 focus:ring-titan-red/10 focus:border-titan-red/20 transition-all resize-none placeholder:text-gray-300"></textarea>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" class="w-full bg-titan-red text-white py-5 rounded-2xl font-black text-[13px] uppercase tracking-widest hover:bg-titan-navy transition-all shadow-xl shadow-titan-red/20 flex items-center justify-center gap-4 group">
                                        {{ __('Submit My Application') }}
                                        <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                                    </button>
                                </div>

                                <p class="text-center text-[10px] text-titan-navy/30 font-bold uppercase tracking-[0.2em] pt-4">
                                    {{ __('By submitting, you agree to our privacy policy regarding recruitment data.') }}
                                </p>
                            </form>
                        </div>
                    </section>
                </div>

                <!-- Sidebar (4 cols) -->
                <div class="lg:col-span-4 lg:sticky lg:top-[120px] h-fit space-y-8">
                    
                    <!-- Quick Apply Box -->
                    <div class="bg-titan-navy p-8 rounded-3xl text-white shadow-2xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-titan-red/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                        <h3 class="text-2xl font-black uppercase tracking-tight mb-4">{{ __('Apply for this position') }}</h3>
                        <p class="text-white/40 text-xs leading-relaxed mb-8">{{ __('Join a team of visionaries shaping the skyline of Cambodia. Submit your profile today.') }}</p>
                        
                        <a href="#apply-form" class="w-full bg-titan-red text-white py-5 rounded-2xl font-black text-[13px] uppercase tracking-widest hover:bg-white hover:text-titan-red transition-all flex items-center justify-center gap-4 shadow-xl shadow-titan-red/20 mb-4 px-6 border border-titan-red">
                            {{ __('Jump to Form') }}
                            <x-lucide-arrow-down class="w-4 h-4 animate-bounce" />
                        </a>
                        
                        <a href="mailto:careers@kimmex.com?subject=Application for {{ $job['title'] }}" class="text-center block text-[10px] text-white/40 hover:text-white transition-colors font-bold uppercase tracking-widest w-full py-2">{{ __('Or apply via email') }}</a>
                    </div>

                    <!-- Share Role -->
                    <div class="p-8 border border-gray-100 rounded-3xl space-y-6">
                         <h4 class="text-xs font-black text-titan-navy uppercase tracking-widest">{{ __('Share this role') }}</h4>
                         <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all">
                                <x-lucide-link class="w-4 h-4" />
                            </a>
                            <a href="#" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all">
                                <x-lucide-facebook class="w-4 h-4" />
                            </a>
                             <a href="#" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all">
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
