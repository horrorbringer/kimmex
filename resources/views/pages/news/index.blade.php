<x-layouts.app title="News & Updates" description="Read the latest news, updates, and announcements from Kimmex.">

    @php
        $newsArticles = [
            ['slug' => 'award', 'category' => __('Updates'), 'image' => '/images/projects/Thumbnail-4.jpg', 'title' => __('Kimmex awarded the mega Ministry infrastructure project.'), 'date' => 'Oct 12, 2026', 'excerpt' => __('Our dedication to quality has earned us a major role in expanding the Ministry headquarters...')],
            ['slug' => 'milestone', 'category' => __('Milestone'), 'image' => '/images/projects/Thumbnail-5.jpg', 'title' => __('Celebrating 25 years of excellence in Cambodia.'), 'date' => 'Sep 05, 2026', 'excerpt' => __('Looking back at our incredible journey of building the infrastructure of tomorrow...')],
            ['slug' => 'safety', 'category' => __('Safety'), 'image' => '/images/projects/Thumbnail-6.jpg', 'title' => __('New safety standards implemented across all active sites.'), 'date' => 'Aug 21, 2026', 'excerpt' => __('Safety is paramount. We have introduced rigorous check protocols for our workforce...')],
            ['slug' => 'mep', 'category' => __('Expertise'), 'image' => '/images/projects/Thumbnail-1.jpg', 'title' => __('Advancing MEP capabilities in South East Asia.'), 'date' => 'Jul 10, 2026', 'excerpt' => __('Exploring the intricacies of modern mechanical and plumbing installations in high rises...')],
            ['slug' => 'partnership', 'category' => __('Partnership'), 'image' => '/images/projects/Thumbnail-2.jpg', 'title' => __('Strategic partnership with leading Japanese engineering firm.'), 'date' => 'Jun 18, 2026', 'excerpt' => __('A new collaboration that brings world-class technology and standards to our projects...')],
            ['slug' => 'green-building', 'category' => __('Sustainability'), 'image' => '/images/projects/Thumbnail-3.jpg', 'title' => __('Pioneering green building practices in the region.'), 'date' => 'May 02, 2026', 'excerpt' => __('Our commitment to sustainable construction is shaping the future of urban development...')]
        ];
    @endphp

    <div class="bg-white min-h-screen text-titan-navy">

        <!-- HERO -->
        <section class="relative z-10 flex items-center justify-center overflow-hidden bg-titan-navy"
            style="min-height: 480px;">
            <div class="absolute inset-0">
                <img src="/images/projects/Thumbnail-6.jpg" alt="News" class="w-full h-full object-cover opacity-45" />
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/50 via-titan-navy/30 to-titan-navy/80">
                </div>
            </div>

            <div class="relative z-20 text-center max-w-4xl px-6 pt-64" x-data="{ shown: false }"
                x-init="setTimeout(() => shown = true, 100)">
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-6'"
                    class="transition-all duration-700 delay-100 inline-flex items-center gap-2 px-5 py-2.5 bg-white/5 backdrop-blur-sm rounded-full text-white/80 text-[11px] font-bold uppercase tracking-widest mb-8 border border-white/10">
                    <x-lucide-newspaper class="w-3.5 h-3.5 text-titan-red" />
                    {{ __('Insights') }}
                </div>

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-700 delay-300 font-black text-white mb-6 leading-none tracking-tighter uppercase"
                    style="font-size: clamp(2.5rem, 7vw, 5.5rem);">
                    {{ __('News & Updates') }}
                </h1>

                <p :class="shown ? 'opacity-100' : 'opacity-0'"
                    class="transition-all duration-700 delay-500 text-base text-white/40 max-w-xl mx-auto leading-relaxed">
                    {{ __('Stay up to date with the latest developments from Kimmex Construction.') }}
                </p>
            </div>
        </section>

        <!-- FEATURED ARTICLE (First article as hero card) -->
        <section class="max-w-[1200px] mx-auto px-6 relative z-40 -mt-16">
            <a href="/news/{{ $newsArticles[0]['slug'] }}"
                class="group block bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-gray-100 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <!-- Image -->
                    <div class="relative h-64 lg:h-auto overflow-hidden">
                        <img src="{{ $newsArticles[0]['image'] }}" alt="{{ $newsArticles[0]['title'] }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                        <div
                            class="absolute top-5 left-5 px-4 py-2 bg-titan-red text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-lg">
                            {{ $newsArticles[0]['category'] }}
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="p-8 lg:p-14 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-5">
                            <span
                                class="bg-titan-red/10 text-titan-red text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg">{{ __('Featured') }}</span>
                            <span class="text-[11px] font-bold text-titan-navy/30 flex items-center gap-1.5">
                                <x-lucide-calendar class="w-3 h-3" /> {{ $newsArticles[0]['date'] }}
                            </span>
                        </div>
                        <h2
                            class="text-2xl lg:text-3xl font-black text-titan-navy leading-tight mb-4 group-hover:text-titan-red transition-colors duration-300 uppercase tracking-tight">
                            {{ $newsArticles[0]['title'] }}
                        </h2>
                        <p class="text-titan-navy/50 text-[15px] leading-relaxed mb-8">
                            {{ $newsArticles[0]['excerpt'] }}
                        </p>
                        <div
                            class="flex items-center gap-3 text-titan-red font-bold text-xs uppercase tracking-widest group-hover:gap-4 transition-all duration-300">
                            {{ __('Read Full Story') }}
                            <x-lucide-arrow-right class="w-4 h-4" />
                        </div>
                    </div>
                </div>
            </a>
        </section>

        <!-- NEWS GRID -->
        <section class="py-24 max-w-[1200px] mx-auto px-6">
            <!-- Section Header -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-8 bg-titan-red rounded-full"></div>
                    <div>
                        <h2 class="text-xl font-black text-titan-navy uppercase tracking-tight">{{ __('All Articles') }}
                        </h2>
                        <p class="text-titan-navy/30 text-xs mt-0.5">{{ count($newsArticles) - 1 }} {{ __('stories') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach(array_slice($newsArticles, 1) as $i => $article)
                    <a href="/news/{{ $article['slug'] }}"
                        class="group block bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-[0_20px_50px_-15px_rgba(0,0,0,0.08)] transition-all duration-500 hover:-translate-y-1">
                        <!-- Image -->
                        <div class="aspect-[16/10] relative overflow-hidden">
                            <div
                                class="absolute top-4 left-4 z-20 px-3 py-1.5 bg-white/90 backdrop-blur-sm text-titan-navy text-[10px] font-black uppercase tracking-widest rounded-lg">
                                {{ $article['category'] }}
                            </div>
                            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                                class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700" />
                        </div>

                        <!-- Content -->
                        <div class="p-7">
                            <div class="text-[10px] font-bold text-titan-navy/30 mb-3 flex items-center gap-1.5">
                                <x-lucide-calendar class="w-3 h-3 text-titan-red" /> {{ $article['date'] }}
                            </div>

                            <h3
                                class="text-lg font-black text-titan-navy leading-snug mb-3 group-hover:text-titan-red transition-colors duration-300">
                                {{ $article['title'] }}
                            </h3>

                            <p class="text-titan-navy/40 text-sm leading-relaxed mb-6 line-clamp-2">
                                {{ $article['excerpt'] }}
                            </p>

                            <div class="flex items-center justify-between pt-5 border-t border-gray-50">
                                <span
                                    class="text-[11px] font-bold uppercase tracking-widest text-titan-navy/30 group-hover:text-titan-red transition-colors">{{ __('Read More') }}</span>
                                <div
                                    class="w-8 h-8 rounded-full bg-gray-50 text-titan-navy/40 flex items-center justify-center group-hover:bg-titan-red group-hover:text-white transition-all duration-300">
                                    <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
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
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-2">{{ __('Stay Connected') }}
                    </h3>
                    <p class="text-white/40 text-sm">
                        {{ __('Follow us for the latest updates and project announcements.') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-titan-red transition-all duration-300 border border-white/5"><x-lucide-facebook
                            class="w-5 h-5" /></a>
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-titan-red transition-all duration-300 border border-white/5"><x-lucide-linkedin
                            class="w-5 h-5" /></a>
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-titan-red transition-all duration-300 border border-white/5"><x-lucide-youtube
                            class="w-5 h-5" /></a>
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-titan-red transition-all duration-300 border border-white/5"><x-lucide-instagram
                            class="w-5 h-5" /></a>
                </div>
            </div>
        </section>
    </div>

</x-layouts.app>