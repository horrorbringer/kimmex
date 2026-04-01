@php
    $brandProfile = \App\Models\SystemSetting::get('brand_identity', []);
    $orgProfile = \App\Models\SystemSetting::get('organization_profile', []);
    $locale = app()->getLocale();
    $localeKey = $locale === 'kh' ? 'km' : $locale;
    $brand = $brandProfile[$localeKey] ?? ($brandProfile['en'] ?? []);
    $org = $orgProfile[$localeKey] ?? ($orgProfile['en'] ?? []);
    
    $tagline = $org['tagline'] ?? "Cambodia's Premier Construction Partner";
    $story = $brand['company_story'] ?? __("With over 25 years of experience, we have established ourselves as Cambodia's most trusted construction partner, delivering projects that stand the test of time and elevate communities.");

    $mvg_items = [
        [
            'id' => 'vision',
            'icon' => 'eye',
            'title' => __('Our Vision'),
            'desc' => $brand['vision'] ?? __('To be the most trusted and innovative construction partner in Cambodia.')
        ],
        [
            'id' => 'mission',
            'icon' => 'flag',
            'title' => __('Our Mission'),
            'desc' => $brand['mission'] ?? __('To bridge the gap between concept and reality through exceptional engineering and safety.')
        ],
        [
            'id' => 'goal',
            'icon' => 'target',
            'title' => __('Our Goal'),
            'desc' => $brand['goal'] ?? __('To complete every project on time and within budget with zero-accident safety.')
        ],
    ];
@endphp

<section class="py-24 bg-white overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            
            <!-- Left: Staggered Image Grid -->
            <div class="relative" x-data="{ shown: false }" x-intersect.once="shown = true">
                <div :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
                    class="grid grid-cols-2 gap-6 transition-all duration-1000 relative">
                    <div class="space-y-6">
                        <div class="aspect-[4/5] rounded-[2.5rem] overflow-hidden shadow-2xl">
                            <img src="/images/projects/Thumbnail-1.jpg"
                                class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" />
                        </div>
                        <div class="aspect-square rounded-[2.5rem] overflow-hidden shadow-2xl">
                            <img src="/images/projects/Thumbnail-3.jpg"
                                class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" />
                        </div>
                    </div>
                    <div class="space-y-6 pt-12">
                        <div class="aspect-square rounded-[2.5rem] overflow-hidden shadow-2xl">
                            <img src="/images/projects/Thumbnail-2.jpg"
                                class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" />
                        </div>
                        <div class="aspect-[4/5] rounded-[2.5rem] overflow-hidden shadow-2xl relative">
                            <img src="/images/projects/Thumbnail-4.jpg"
                                class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" />

                            <!-- Floating 25+ Years Badge -->
                            <div class="absolute -bottom-4 -right-4 bg-accent-orange text-white p-6 rounded-3xl shadow-xl z-20 flex flex-col items-center justify-center min-w-[120px]">
                                <span class="text-3xl font-black leading-none">25+</span>
                                <span class="text-[10px] font-black uppercase tracking-widest mt-1">{{ __('Years') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Content -->
            <div x-data="{ shown: false }" x-intersect.once="shown = true" 
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" 
                class="transition-all duration-1000 delay-200">
                
                <span class="text-accent-orange font-bold uppercase tracking-widest text-sm mb-4 block">
                    {{ __('WHO WE ARE') }}
                </span>
                
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-8">
                    {{ $tagline }}
                </h2>
                
                <p class="text-gray-500 text-lg leading-relaxed mb-12 whitespace-pre-line">
                    {{ $story }}
                </p>

                <!-- MVG List -->
                <div class="space-y-6" x-data="{ active: 'vision' }">
                    @foreach($mvg_items as $item)
                        <div class="group cursor-pointer border-b border-gray-100 last:border-b-0 pb-6"
                             @click="active = (active === '{{ $item['id'] }}' ? null : '{{ $item['id'] }}')">
                            <div class="flex gap-6 items-start">
                                <!-- Icon Box -->
                                <div class="w-14 h-14 rounded-[1rem] bg-[#FFF0E6] text-accent-orange flex items-center justify-center shrink-0 transition-colors duration-500"
                                     :class="active === '{{ $item['id'] }}' ? 'bg-accent-orange text-white' : ''">
                                    @if($item['icon'] === 'eye')
                                        <x-lucide-eye class="w-6 h-6" stroke-width="2" />
                                    @elseif($item['icon'] === 'flag')
                                        <x-lucide-flag class="w-6 h-6" stroke-width="2" />
                                    @elseif($item['icon'] === 'target')
                                        <x-lucide-target class="w-6 h-6" stroke-width="2" />
                                    @endif
                                </div>

                                <!-- Text -->
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-xl font-bold text-gray-900 transition-colors duration-500"
                                            :class="active === '{{ $item['id'] }}' ? 'text-accent-orange' : ''">
                                            {{ $item['title'] }}
                                        </h3>
                                        <div class="w-8 h-8 rounded-full border flex items-center justify-center transition-all duration-500"
                                             :class="active === '{{ $item['id'] }}' ? 'border-accent-orange text-accent-orange rotate-180' : 'border-gray-200 text-gray-400'">
                                            <x-lucide-chevron-down class="w-4 h-4" />
                                        </div>
                                    </div>
                                    
                                    <div x-show="active === '{{ $item['id'] }}'" x-collapse>
                                        <p class="text-gray-500 text-base leading-relaxed whitespace-pre-line pb-2">
                                            {{ $item['desc'] }}
                                        </p>
                                    </div>
                                    
                                    <p x-show="active !== '{{ $item['id'] }}'" class="text-gray-400 text-sm italic truncate max-w-sm">
                                        {{ \Illuminate\Support\Str::limit($item['desc'], 80) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    <a href="/about" class="inline-flex items-center gap-3 bg-titan-navy text-white px-8 py-4 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-titan-red transition-all shadow-lg hover:shadow-titan-red/20">
                        {{ __('Discover More') }} <x-lucide-arrow-right class="w-4 h-4" />
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
