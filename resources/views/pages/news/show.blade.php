<x-layouts.app :title="__('News Details')" description="Read the latest news and updates from Kimmex.">

    @php
        $newsArticles = [
            [
                'slug' => 'award',
                'category' => __('Updates'),
                'image' => '/images/projects/Thumbnail-4.jpg',
                'title' => __('Kimmex awarded the mega Ministry infrastructure project.'),
                'date' => 'Oct 12, 2026',
                'author' => 'Kimmex Editorial',
                'readTime' => '5 min read',
                'content' => __('Our dedication to quality and architectural precision has earned us a major role in expanding the Ministry headquarters. This landmark project represents a significant milestone in our history of delivering governmental infrastructure. The expansion will feature sustainable building materials, high-efficiency MEP systems, and integrated security protocols as part of our commitment to excellence.')
            ],
            [
                'slug' => 'milestone',
                'category' => __('Milestone'),
                'image' => '/images/projects/Thumbnail-5.jpg',
                'title' => __('Celebrating 25 years of excellence in Cambodia.'),
                'date' => 'Sep 05, 2026',
                'author' => 'Communications Team',
                'readTime' => '4 min read',
                'content' => __('Looking back at our incredible journey of building the infrastructure of tomorrow. Over the last quarter-century, Kimmex has evolved from a small engineering firm into a diversified construction and investment group. We thank our partners, employees, and the government for their trust.')
            ],
            [
                'slug' => 'safety',
                'category' => __('Safety'),
                'image' => '/images/projects/Thumbnail-6.jpg',
                'title' => __('New safety standards implemented across all active sites.'),
                'date' => 'Aug 21, 2026',
                'author' => 'EHS Department',
                'readTime' => '6 min read',
                'content' => __('Safety is paramount. We have introduced rigorous new check protocols for our entire workforce, emphasizing Zero-Harm strategies. Every site now features real-time monitoring of safety compliance and augmented training programs for operators.')
            ],
            [
                'slug' => 'mep',
                'category' => __('Expertise'),
                'image' => '/images/projects/Thumbnail-1.jpg',
                'title' => __('Advancing MEP capabilities in South East Asia.'),
                'date' => 'Jul 10, 2026',
                'author' => 'Engineering Lead',
                'readTime' => '7 min read',
                'content' => __('Exploring the intricacies of modern mechanical and plumbing installations in high rises. Our team is now utilizing BIM and Digital Twin technologies to optimize the lifespan and efficiency of complex building systems.')
            ],
            [
                'slug' => 'partnership',
                'category' => __('Partnership'),
                'image' => '/images/projects/Thumbnail-2.jpg',
                'title' => __('Strategic partnership with leading Japanese engineering firm.'),
                'date' => 'Jun 18, 2026',
                'author' => 'Executive Office',
                'readTime' => '3 min read',
                'content' => __('A new collaboration that brings world-class technology and standards to our projects. This partnership focuses on seismic-resistant structures and advanced modular construction techniques.')
            ],
            [
                'slug' => 'green-building',
                'category' => __('Sustainability'),
                'image' => '/images/projects/Thumbnail-3.jpg',
                'title' => __('Pioneering green building practices in the region.'),
                'date' => 'May 02, 2026',
                'author' => 'Sustainability Group',
                'readTime' => '6 min read',
                'content' => __('Our commitment to sustainable construction is shaping the future of urban development. We are now integrating solar thermal energy and greywater recycling into all our new commercial developments.')
            ]
        ];

        $article = collect($newsArticles)->firstWhere('slug', $slug) ?? $newsArticles[0];
    @endphp

    <div class="bg-white min-h-screen text-titan-navy pb-32">

        <!-- === HERO SECTION (Editorial Style) === -->
        <header class="relative bg-titan-navy overflow-hidden pt-64 pb-32">
            <!-- Background Image with Depth -->
            <div class="absolute inset-0">
                <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                    class="w-full h-full object-cover opacity-20 scale-105" />
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/50 via-titan-navy to-titan-navy/95"></div>
            </div>

            <div class="relative z-10 max-w-[1200px] mx-auto px-6">
                <nav
                    class="flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-white/40 mb-12">
                    <a href="/" class="hover:text-titan-red transition-colors">{{ __('Home') }}</a>
                    <span class="w-1 h-1 rounded-full bg-white/10"></span>
                    <a href="/news" class="hover:text-titan-red transition-colors">{{ __('News') }}</a>
                    <span class="w-1 h-1 rounded-full bg-titan-red"></span>
                    <span class="text-white/60">{{ __('Article') }}</span>
                </nav>

                <div class="max-w-4xl" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">
                    <div class="inline-flex items-center gap-3 px-4 py-2 bg-white/5 border border-white/10 rounded-lg mb-8"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                        class="transition-all duration-700 delay-100">
                        <span class="w-2 h-2 rounded-full bg-titan-red"></span>
                        <span
                            class="text-[10px] font-black text-white uppercase tracking-[0.3em]">{{ $article['category'] }}</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-7xl font-black text-white uppercase tracking-tighter leading-[0.9] mb-12"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-700 delay-300">
                        {{ $article['title'] }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-10 text-[11px] font-bold text-white/50 uppercase tracking-widest border-t border-white/10 pt-10"
                        :class="shown ? 'opacity-100' : 'opacity-0'" class="transition-all duration-1000 delay-500">
                        <div class="flex items-center gap-3">
                            <x-lucide-user class="w-4 h-4 text-titan-red" />
                            {{ $article['author'] }}
                        </div>
                        <div class="flex items-center gap-3">
                            <x-lucide-calendar class="w-4 h-4 text-titan-red" />
                            {{ $article['date'] }}
                        </div>
                        <div class="flex items-center gap-3">
                            <x-lucide-clock class="w-4 h-4 text-titan-red" />
                            {{ $article['readTime'] }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- === ARTICLE CONTENT === -->
        <main class="relative z-20 -mt-10 lg:-mt-20 max-w-[1200px] mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">

                <!-- Left: Main Content -->
                <div class="lg:col-span-8">
                    <div
                        class="bg-white rounded-3xl p-8 md:p-16 lg:p-20 shadow-[0_32px_128px_-32px_rgba(0,0,0,0.1)] border border-gray-100">
                        <!-- Dynamic Featured Image -->
                        <div class="aspect-[16/9] rounded-2xl overflow-hidden mb-16 shadow-2xl">
                            <img src="{{ $article['image'] }}" class="w-full h-full object-cover" />
                        </div>

                        <!-- Article Text -->
                        <div
                            class="prose prose-xl prose-slate max-w-none text-titan-navy/80 leading-relaxed font-medium">
                            <p
                                class="mb-10 text-2xl font-black text-titan-navy leading-tight border-l-4 border-titan-red pl-8">
                                {{ $article['excerpt'] ?? __('Leading the way in high-precision construction and infrastructure management.') }}
                            </p>

                            <div class="space-y-8">
                                {{ $article['content'] }}
                            </div>
                        </div>

                        <!-- Share Section -->
                        <div
                            class="mt-20 pt-10 border-t border-gray-100 flex items-center justify-between flex-wrap gap-8">
                            <div class="flex items-center gap-4">
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-titan-navy/30">{{ __('Share Article') }}</span>
                                <div class="flex gap-2">
                                    <a href="#"
                                        class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all"><x-lucide-facebook
                                            class="w-4 h-4" /></a>
                                    <a href="#"
                                        class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all"><x-lucide-linkedin
                                            class="w-4 h-4" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Sidebar -->
                <div class="lg:col-span-4 space-y-12">
                    <!-- Related News -->
                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                        <h3
                            class="text-xs font-black uppercase tracking-widest text-titan-navy mb-8 border-b border-titan-red/20 pb-4">
                            {{ __('Latest Updates') }}</h3>
                        <div class="space-y-8">
                            @foreach(collect($newsArticles)->where('slug', '!=', $slug)->take(3) as $related)
                                <a href="/news/{{ $related['slug'] }}" class="group block">
                                    <div class="text-[9px] font-black text-titan-red uppercase tracking-widest mb-2">
                                        {{ $related['category'] }}</div>
                                    <h4
                                        class="text-sm font-black text-titan-navy group-hover:text-titan-red transition-colors leading-snug mb-2">
                                        {{ $related['title'] }}</h4>
                                    <div class="text-[10px] text-titan-navy/40 font-bold uppercase">{{ $related['date'] }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Newsletter / CTA -->
                    <div class="bg-titan-navy rounded-3xl p-10 text-white relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-titan-red/10 rounded-full blur-3xl group-hover:bg-titan-red/20 transition-all duration-700">
                        </div>
                        <h3 class="text-2xl font-black uppercase mb-4 relative z-10">{{ __('Newsletter') }}</h3>
                        <p class="text-xs text-white/40 leading-relaxed mb-8 relative z-10">
                            {{ __('Subscribe to receive the latest industry insights and project updates from Kimmex.') }}
                        </p>
                        <form class="space-y-4 relative z-10">
                            <input type="email" placeholder="example@email.com"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-xs font-bold text-white outline-none focus:border-titan-red transition-all" />
                            <button
                                class="w-full bg-titan-red text-white py-4 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-white hover:text-titan-navy transition-all shadow-xl shadow-titan-red/20">{{ __('Subscribe') }}</button>
                        </form>
                    </div>
                </div>

            </div>
        </main>

    </div>

</x-layouts.app>