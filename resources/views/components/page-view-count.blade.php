<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-[0.16em]',
    'text-white/85' => $light,
    'text-titan-navy/55' => ! $light,
]) }}>
    @unless ($countOnly)
        <x-lucide-eye class="w-3.5 h-3.5" />
    @endunless
    <span>{{ number_format($count) }}@unless ($countOnly) {{ __('views') }}@endunless</span>
</span>
