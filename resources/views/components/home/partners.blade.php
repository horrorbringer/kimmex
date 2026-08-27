@props(['partners' => null])

@php
    $partners = $partners ?? app(\App\Services\HomePageService::class)->getPartners();
    $shouldUseMarquee = count($partners) > 12;
    $displayPartners = $shouldUseMarquee ? array_merge($partners, $partners) : $partners;
@endphp

<section class="border-y border-titan-navy/10 py-10 md:py-14">
    <div class="mx-auto max-w-[1240px] px-5 md:px-6">
        <div class="mb-6 max-w-2xl md:mb-8">
            <div class="mb-3 flex items-center gap-3">
                <span class="text-[10px] font-black uppercase tracking-[0.22em]" style="color: var(--primary-color, #E31E24);">
                    {{ __('Our Partners') }}
                </span>
                <span class="h-px w-8" style="background: var(--primary-color, #E31E24);"></span>
            </div>
            <h2 class="font-heading !text-xl font-black tracking-tight text-titan-navy md:text-4xl">
                {{ __('Trusted By Leading Institutions') }}
            </h2>
            <p class="mt-3 max-w-xl text-sm leading-relaxed text-titan-navy/60 md:text-base">
                {{ __('Collaboration with organizations that help shape Cambodia’s future.') }}
            </p>
        </div>

        @if($shouldUseMarquee)
            <div class="partner-marquee">
        @endif

        <div class="{{ $shouldUseMarquee ? 'partner-marquee-track' : 'grid grid-cols-2 border-l border-t border-titan-navy/10 sm:grid-cols-3 lg:grid-cols-6' }}">
            @foreach($displayPartners as $partner)
                @php
                    $partnerCellClass = $shouldUseMarquee
                        ? 'partner-marquee-item group'
                        : 'group relative flex h-28 min-w-0 items-center justify-center border-b border-r border-titan-navy/10 p-1.5 focus-visible:z-10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-titan-red sm:h-32 md:h-36 sm:p-2';
                @endphp
                @if(filled($partner['website']))
                    <a href="{{ $partner['website'] }}" target="_blank" rel="noopener noreferrer"
                    class="{{ $partnerCellClass }}"
                    aria-label="{{ __('Visit') }} {{ $partner['name'] }}">
                @else
                    <div class="{{ $partnerCellClass }}">
                @endif
                    <img
                        src="{{ $partner['logo'] }}"
                        alt="{{ $partner['name'] }}"
                        title="{{ $partner['name'] }}"
                        class="h-full w-full max-h-[92%] max-w-[94%] object-contain mix-blend-multiply transition-transform duration-300 ease-out group-hover:scale-[1.04] dark:mix-blend-normal"
                        loading="lazy"
                        decoding="async"
                        onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');" />
                    <span class="hidden max-w-full text-center text-sm font-black leading-snug text-titan-navy" aria-label="{{ $partner['name'] }}">
                        {{ $partner['name'] }}
                    </span>
                @if(filled($partner['website']))
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>

        @if($shouldUseMarquee)
            </div>
        @endif
    </div>
</section>

@once
    <style>
        .partner-marquee {
            overflow: hidden;
            mask-image: linear-gradient(to right, transparent, #000 5%, #000 95%, transparent);
        }

        .partner-marquee-track {
            display: flex;
            width: max-content;
            animation: partner-marquee-scroll 48s linear infinite;
        }

        .partner-marquee:hover .partner-marquee-track {
            animation-play-state: paused;
        }

        .partner-marquee-item {
            position: relative;
            display: flex;
            flex: 0 0 clamp(10rem, 16vw, 13rem);
            height: 8.5rem;
            min-width: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid rgb(11 43 92 / 0.1);
            border-right: 0;
            padding: 0.35rem 0.6rem;
        }

        @keyframes partner-marquee-scroll {
            to {
                transform: translateX(-50%);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .partner-marquee {
                overflow-x: auto;
                mask-image: none;
            }

            .partner-marquee-track {
                animation: none;
            }
        }
    </style>
@endonce
