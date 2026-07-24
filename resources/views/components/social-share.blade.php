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
    copied: false,
    baseStyle: 'display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 40px; padding: 0 16px; border-radius: 999px; border: 1px solid #0B2B5C; background-color: #0B2B5C; color: #ffffff; font-size: 12px; font-weight: 700; letter-spacing: 0.02em; cursor: pointer; box-shadow: 0 4px 12px rgba(11, 43, 92, 0.22); transition: background-color 0.2s, border-color 0.2s, transform 0.2s, box-shadow 0.2s;',
    copiedStyle: 'display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 40px; padding: 0 16px; border-radius: 999px; border: 1px solid #16a34a; background-color: #16a34a; color: #ffffff; font-size: 12px; font-weight: 700; letter-spacing: 0.02em; cursor: pointer; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.24); transition: background-color 0.2s, border-color 0.2s, transform 0.2s, box-shadow 0.2s;'
}" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
    {{-- Facebook --}}
    <a href="{{ $facebookUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'share-facebook', 'width=580,height=400,toolbar=no,menubar=no'); return false;"
       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 6px; background-color: #1877F2; color: #fff; text-decoration: none; transition: opacity 0.2s, transform 0.2s;"
       onmouseover="this.style.opacity='0.85'; this.style.transform='translateY(-2px)';"
       onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';"
       aria-label="Share on Facebook">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
    </a>

    {{-- LinkedIn --}}
    <a href="{{ $linkedinUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'share-linkedin', 'width=580,height=400,toolbar=no,menubar=no'); return false;"
       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 6px; background-color: #0A66C2; color: #fff; text-decoration: none; transition: opacity 0.2s, transform 0.2s;"
       onmouseover="this.style.opacity='0.85'; this.style.transform='translateY(-2px)';"
       onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';"
       aria-label="Share on LinkedIn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
        </svg>
    </a>

    {{-- Telegram --}}
    <a href="{{ $telegramUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'share-telegram', 'width=580,height=400,toolbar=no,menubar=no'); return false;"
       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 6px; background-color: #0088cc; color: #fff; text-decoration: none; transition: opacity 0.2s, transform 0.2s;"
       onmouseover="this.style.opacity='0.85'; this.style.transform='translateY(-2px)';"
       onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';"
       aria-label="Share on Telegram">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0h-.056zm5.63 7.09l-1.97 9.29c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295l.213-3.053 5.56-5.023c.24-.213-.054-.334-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.832.943z"/>
        </svg>
    </a>

    {{-- Twitter/X --}}
    <a href="{{ $twitterUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'share-twitter', 'width=580,height=400,toolbar=no,menubar=no'); return false;"
       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 6px; background-color: #000000; color: #fff; text-decoration: none; transition: opacity 0.2s, transform 0.2s;"
       onmouseover="this.style.opacity='0.75'; this.style.transform='translateY(-2px)';"
       onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';"
       aria-label="Share on Twitter/X">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
    </a>

    {{-- Copy Link --}}
    <button
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
        :style="copied ? copiedStyle : baseStyle"
        onmouseover="if (!copied) { this.style.borderColor='#E31E24'; this.style.backgroundColor='#E31E24'; this.style.color='#ffffff'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 18px rgba(227, 30, 36, 0.26)'; }"
        onmouseout="if (!copied) { this.style.borderColor='#0B2B5C'; this.style.backgroundColor='#0B2B5C'; this.style.color='#ffffff'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(11, 43, 92, 0.22)'; }"
        aria-label="Copy link to clipboard">
        {{-- Link icon --}}
        <svg x-show="!copied" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
            <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
        </svg>
        {{-- Check icon --}}
        <svg x-show="copied" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        <span x-text="copied ? '{{ __("Copied!") }}' : '{{ __("Copy Link") }}'"></span>
    </button>
</div>
