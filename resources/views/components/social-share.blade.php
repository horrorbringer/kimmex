@props(['url' => '', 'title' => '', 'description' => ''])

@php
    $encodedUrl = urlencode($url);
    $encodedTitle = urlencode($title);
    $facebookUrl = "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}";
    $linkedinUrl = "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}";
    $telegramUrl = "https://t.me/share/url?url={$encodedUrl}&text={$encodedTitle}";
    $twitterUrl = "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}";
@endphp

<div x-data="{
    copied: false
}" class="flex items-center gap-1.5 sm:gap-2 flex-nowrap shrink-0">
    {{-- Facebook --}}
    <a href="{{ $facebookUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'share-facebook', 'width=580,height=400,toolbar=no,menubar=no'); return false;"
       class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-[#1877F2] text-white shrink-0 shadow-xs transition-all duration-200 hover:opacity-90 hover:-translate-y-0.5"
       aria-label="Share on Facebook">
        <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
    </a>

    {{-- LinkedIn --}}
    <a href="{{ $linkedinUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'share-linkedin', 'width=580,height=400,toolbar=no,menubar=no'); return false;"
       class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-[#0A66C2] text-white shrink-0 shadow-xs transition-all duration-200 hover:opacity-90 hover:-translate-y-0.5"
       aria-label="Share on LinkedIn">
        <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
        </svg>
    </a>

    {{-- Telegram --}}
    <a href="{{ $telegramUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'share-telegram', 'width=580,height=400,toolbar=no,menubar=no'); return false;"
       class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-[#0088cc] text-white shrink-0 shadow-xs transition-all duration-200 hover:opacity-90 hover:-translate-y-0.5"
       aria-label="Share on Telegram">
        <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0h-.056zm5.63 7.09l-1.97 9.29c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295l.213-3.053 5.56-5.023c.24-.213-.054-.334-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.832.943z"/>
        </svg>
    </a>

    {{-- Twitter/X --}}
    <a href="{{ $twitterUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'share-twitter', 'width=580,height=400,toolbar=no,menubar=no'); return false;"
       class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-black text-white shrink-0 shadow-xs transition-all duration-200 hover:opacity-85 hover:-translate-y-0.5"
       aria-label="Share on Twitter/X">
        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
    </a>

    {{-- Copy Link --}}
    <button
        type="button"
        @click="
            const url = '{{ $url }}';
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
            copied = true;
            setTimeout(() => copied = false, 2000);
        "
        :class="copied ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-titan-navy border-titan-navy text-white hover:bg-titan-red hover:border-titan-red'"
        class="inline-flex items-center justify-center gap-1.5 h-8 sm:h-9 px-3 sm:px-4 rounded-full border text-[11px] sm:text-xs font-bold tracking-wider uppercase shrink-0 shadow-xs transition-all duration-200 hover:-translate-y-0.5 cursor-pointer"
        aria-label="Copy link to clipboard">
        {{-- Link icon --}}
        <svg x-show="!copied" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
            <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
        </svg>
        {{-- Check icon --}}
        <svg x-show="copied" x-cloak class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        <span x-text="copied ? '{{ __("Copied!") }}' : '{{ __("Copy Link") }}'"></span>
    </button>
</div>
