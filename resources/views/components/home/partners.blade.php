@php
    $fallbacks = [1, 2, 3, 4, 5, 6, 7, 9, 10, 11];

    $partners = \Illuminate\Support\Facades\Cache::remember('home_partners_array_v3_'.app()->getLocale(), now()->addHours(12), function () use ($fallbacks) {
        return \App\Models\Partner::query()
            ->where('isActive', true)
            ->orderBy('orderIndex')
            ->get()
            ->map(function (\App\Models\Partner $partner, int $index) use ($fallbacks): array {
                $fallbackLogo = '/partners/'.$fallbacks[$index % count($fallbacks)].'.png';
                $logo = $partner->logoUrl;

                return [
                    'name' => $partner->getTranslation('name', app()->getLocale()),
                    'logo' => $logo === 'partners/placeholder.png'
                        ? $fallbackLogo
                        : \App\Support\PublicStorage::urlIfExists($logo, $fallbackLogo),
                    'website' => $partner->website,
                ];
            })
            ->all();
    });

    if ($partners === []) {
        $partners = collect($fallbacks)
            ->map(fn (int $fallback): array => [
                'name' => __('Partner'),
                'logo' => '/partners/'.$fallback.'.png',
                'website' => null,
            ])
            ->all();
    }

    $partnerCount = count($partners);
@endphp

<section class="border-t border-gray-100 bg-slate-50 py-14 md:py-20">
    <div class="mx-auto max-w-[1280px] px-5 md:px-6">
        <div class="mb-9 flex flex-col gap-5 sm:mb-11 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                <div class="mb-3 flex items-center gap-3">
                    <span class="h-[2px] w-8" style="background: var(--primary-color, #E31E24);"></span>
                    <span class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--primary-color, #E31E24);">
                        {{ __('Our Partners') }}
                    </span>
                </div>
                <h2 class="font-heading text-2xl font-black tracking-tight text-titan-navy md:text-3xl">
                    {{ __('Trusted By Leading Institutions') }}
                </h2>
                <p class="mt-2 text-sm leading-relaxed text-titan-navy/60 md:text-base">
                    {{ __('Collaboration with organizations that help shape Cambodia’s future.') }}
                </p>
            </div>

            <div class="flex w-fit items-center gap-3 rounded-full border border-titan-navy/10 bg-white px-4 py-2.5 shadow-sm">
                <span class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-black text-white" style="background: var(--primary-color, #E31E24);">
                    {{ $partnerCount }}
                </span>
                <span class="text-xs font-bold uppercase tracking-[0.14em] text-titan-navy/60">
                    {{ __('Trusted Partners') }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 xl:grid-cols-7">
            @foreach($partners as $partner)
                @if(filled($partner['website']))
                    <a href="{{ $partner['website'] }}" target="_blank" rel="noopener noreferrer"
                    class="group relative flex aspect-[5/3] min-w-0 flex-col items-center justify-center overflow-hidden rounded-xl border border-titan-navy/10 bg-white p-4 shadow-[0_8px_22px_-20px_rgba(11,43,92,0.45)] transition duration-300 hover:-translate-y-1 hover:border-titan-red/30 hover:shadow-[0_14px_28px_-18px_rgba(11,43,92,0.28)]"
                    aria-label="{{ __('Visit') }} {{ $partner['name'] }}">
                @else
                    <div class="group relative flex aspect-[5/3] min-w-0 flex-col items-center justify-center overflow-hidden rounded-xl border border-titan-navy/10 bg-white p-4 shadow-[0_8px_22px_-20px_rgba(11,43,92,0.45)] transition duration-300 hover:-translate-y-1 hover:border-titan-red/30 hover:shadow-[0_14px_28px_-18px_rgba(11,43,92,0.28)]">
                @endif
                    <img
                        src="{{ $partner['logo'] }}"
                        alt="{{ $partner['name'] }}"
                        title="{{ $partner['name'] }}"
                        class="h-12 w-full object-contain opacity-80 grayscale transition duration-300 group-hover:opacity-100 group-hover:grayscale-0 sm:h-14"
                        loading="lazy"
                        decoding="async"
                        onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');" />
                    <span class="hidden max-w-full text-center text-sm font-black leading-snug text-titan-navy" aria-label="{{ $partner['name'] }}">
                        {{ $partner['name'] }}
                    </span>
                    <span class="pointer-events-none absolute inset-x-4 bottom-2 truncate text-center text-[9px] font-bold uppercase tracking-[0.12em] text-titan-navy/45 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        {{ $partner['name'] }}
                    </span>
                @if(filled($partner['website']))
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
