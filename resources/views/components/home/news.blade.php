@php
    $fallbackImage = '/images/webp/projects/Thumbnail-5.webp';
    $allNews = \Illuminate\Support\Facades\Cache::remember('home_news_array_'.app()->getLocale(), now()->addHours(12), function() use ($fallbackImage) {
        $newsDb = \App\Models\NewsArticle::where('isActive', true)
            ->where('publishedAt', '<=', now())
            ->orderBy('publishedAt', 'desc')
            ->take(3)
            ->get();

        return $newsDb->map(function ($n) use ($fallbackImage) {
            $imageUrl = \App\Support\PublicStorage::urlIfExists($n->coverImage, $fallbackImage);

            return [
                'id' => $n->slug,
                'image' => $imageUrl,
                'date' => $n->publishedAt ? $n->publishedAt->format('M d, Y') : $n->created_at->format('M d, Y'),
                'title' => $n->getTranslation('title', app()->getLocale()),
                'category' => $n->getTranslation('category', app()->getLocale()) ?: __('Updates'),
            ];
        })->toArray();
    });

    if (empty($allNews)) {
        $allNews = [
            ['id' => 'safety', 'category' => __('Updates'), 'image' => '/images/webp/projects/Thumbnail-6.webp', 'title' => __('Kimmex Safety Milestone at HQ'), 'date' => 'MAR 30, 2026'],
            ['id' => 'tech', 'category' => __('Milestone'), 'image' => '/images/webp/projects/Thumbnail-5.webp', 'title' => __('New MEP Integration Techniques'), 'date' => 'MAR 15, 2026'],
            ['id' => 'award', 'category' => __('Award'), 'image' => '/images/webp/projects/Thumbnail-4.webp', 'title' => __('Excellence in Construction 2026'), 'date' => 'MAR 05, 2026'],
        ];
    }
@endphp

<section class="py-24 bg-gray-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-row justify-center items-baseline gap-3 md:gap-6 mb-10 md:mb-16 text-center transition-all duration-1000">
            <div class="min-w-0 flex items-baseline gap-2">
                <span
                    class="text-titan-red font-bold uppercase tracking-widest text-[8px] sm:text-[10px] md:text-sm block whitespace-nowrap">{{ __('News & Updates') }}</span>
                <h2 class="text-base sm:text-2xl md:text-4xl font-heading font-black text-titan-navy leading-tight whitespace-nowrap">{{ __('Latest Insights') }}</h2>
            </div>
            <a href="/news"
                class="inline-flex items-center gap-1 md:gap-2 text-titan-red font-bold uppercase tracking-widest text-[8px] sm:text-[10px] md:text-sm hover:text-titan-navy transition-colors whitespace-nowrap shrink-0">
                {{ __('View All News') }} <x-lucide-arrow-right class="w-4 h-4" />
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($allNews as $index => $news)
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000">
                    <a href="/news/{{ $news['id'] }}"
                        class="group cursor-pointer bg-white rounded overflow-hidden shadow-sm hover:shadow-xl transition-all h-full flex flex-col">
                        <div class="aspect-[16/10] relative overflow-hidden bg-titan-navy">
                            <div
                                class="absolute top-4 left-4 bg-titan-navy/90 backdrop-blur-sm text-white text-[8px] font-black uppercase tracking-[0.2em] px-2.5 py-1.5 z-10 rounded-md">
                                {{ $news['category'] }}
                            </div>
                            <img src="{{ $news['image'] }}" alt="{{ $news['title'] }}"
                                class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <div
                                class="text-xs font-bold uppercase tracking-widest text-titan-navy/40 mb-3 flex items-center gap-2">
                                <x-lucide-calendar class="w-3.5 h-3.5" /> {{ $news['date'] }}
                            </div>
                            <h3
                                class="text-xl font-heading font-bold text-titan-navy group-hover:text-accent-orange transition-colors leading-tight mb-4">
                                {{ $news['title'] }}
                            </h3>
                            <span class="text-sm font-bold text-accent-orange flex items-center gap-2 mt-auto">
                                {{ __('Read Story') }} <x-lucide-arrow-right class="w-3.5 h-3.5" />
                            </span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
