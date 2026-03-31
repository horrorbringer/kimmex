@php
    $newsDb = \App\Models\NewsArticle::where('publishedAt', '<=', now())
        ->orderBy('publishedAt', 'desc')
        ->take(3)
        ->get();

    $allNews = $newsDb->map(function ($n) {
        return [
            'id' => $n->slug,
            'image' => $n->image ? \Illuminate\Support\Facades\Storage::url($n->image) : '/images/projects/Thumbnail-4.jpg',
            'date' => $n->publishedAt ? $n->publishedAt->format('M d, Y') : $n->created_at->format('M d, Y'),
            'title' => $n->getTranslation('title', app()->getLocale()),
            'category' => __('Updates'),
        ];
    })->toArray();

    if (empty($allNews)) {
        $allNews = [
            ['id' => 'safety', 'category' => __('Updates'), 'image' => '/images/projects/Thumbnail-6.jpg', 'title' => __('Kimmex Safety Milestone at HQ'), 'date' => 'MAR 30, 2026'],
            ['id' => 'tech', 'category' => __('Milestone'), 'image' => '/images/projects/Thumbnail-5.jpg', 'title' => __('New MEP Integration Techniques'), 'date' => 'MAR 15, 2026'],
            ['id' => 'award', 'category' => __('Award'), 'image' => '/images/projects/Thumbnail-4.jpg', 'title' => __('Excellence in Construction 2026'), 'date' => 'MAR 05, 2026'],
        ];
    }
@endphp

<section class="py-24 bg-gray-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-col md:flex-row justify-between items-end mb-16 transition-all duration-1000">
            <div>
                <span
                    class="text-accent-orange font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('News & Updates') }}</span>
                <h2 class="text-4xl font-heading font-black text-titan-navy">{{ __('Latest Insights') }}</h2>
            </div>
            <a href="/news"
                class="mt-6 md:mt-0 inline-flex items-center gap-2 text-accent-orange font-bold uppercase tracking-widest text-sm hover:text-titan-navy transition-colors">
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
                        class="group cursor-pointer bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all h-full flex flex-col">
                        <div class="aspect-[16/10] relative overflow-hidden">
                            <div
                                class="absolute top-4 left-4 bg-accent-orange text-white text-xs font-bold uppercase px-3 py-1 z-10 rounded">
                                {{ $news['category'] }}
                            </div>
                            <img src="{{ $news['image'] }}" alt="{{ $news['title'] }}"
                                class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700" />
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