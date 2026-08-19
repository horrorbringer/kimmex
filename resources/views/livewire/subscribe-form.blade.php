<div>
    @if($subscribed)
        <div class="flex items-center gap-2 text-green-400 text-sm font-semibold py-1">
            <x-lucide-check-circle class="w-4 h-4 shrink-0" />
            <span>{{ __('Thank you! You\'re subscribed.') }}</span>
        </div>
    @else
        <form wire:submit.prevent="subscribe" class="w-full">
            <div class="flex flex-row items-center w-full bg-white/10 border border-white/15 rounded-xl overflow-hidden focus-within:border-titan-red focus-within:ring-1 focus-within:ring-titan-red/30 transition-all">
                <input type="email" wire:model="email" required
                    placeholder="{{ __('Enter your email') }}"
                    class="w-full min-w-0 h-10 sm:h-11 px-3.5 sm:px-4 bg-transparent border-0 text-xs sm:text-sm text-white placeholder:text-white/45 focus:outline-hidden focus:ring-0" />
                <button type="submit" wire:loading.attr="disabled"
                    class="h-10 sm:h-11 px-4 sm:px-5 bg-titan-red text-white text-[10px] sm:text-[11px] font-black uppercase tracking-wider hover:bg-white hover:text-titan-navy transition-all shrink-0 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>{{ __('Subscribe') }}</span>
                    <span wire:loading class="inline-flex items-center gap-1">
                        <svg class="animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                </button>
            </div>

            @if($error)
                <p class="text-[11px] text-red-400 mt-1.5">{{ $error }}</p>
            @endif
            @error('email')
                <p class="text-[11px] text-red-400 mt-1.5">{{ $message }}</p>
            @enderror
        </form>
    @endif
</div>
