<div>
    @if($subscribed)
        <div class="flex items-center gap-2 text-green-400 text-sm font-semibold">
            <x-lucide-check-circle class="w-4 h-4 shrink-0" />
            <span>{{ __('Thank you! You\'re subscribed.') }}</span>
        </div>
    @else
        <form wire:submit="subscribe" class="flex flex-col sm:flex-row gap-2">
            <div class="flex-grow relative">
                <input type="email" wire:model="email" required
                    placeholder="{{ __('Enter your email') }}"
                    class="w-full h-10 px-4 rounded bg-white/10 border border-white/15 text-sm text-white placeholder:text-white/40 focus:outline-none focus:border-titan-red/50 focus:ring-1 focus:ring-titan-red/20 transition-all" />
            </div>
            <button type="submit" wire:loading.attr="disabled"
                class="h-10 px-5 rounded bg-titan-red text-white text-[10px] font-black uppercase tracking-[0.18em] hover:bg-white hover:text-titan-navy transition-all shrink-0 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove>{{ __('Subscribe') }}</span>
                <span wire:loading class="inline-flex items-center gap-1">
                    <svg class="animate-spin w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
            </button>
        </form>
        @if($error)
            <p class="text-[11px] text-red-400 mt-1.5">{{ $error }}</p>
        @endif
        @error('email')
            <p class="text-[11px] text-red-400 mt-1.5">{{ $message }}</p>
        @enderror
    @endif
</div>
