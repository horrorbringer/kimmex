<div x-data="{ open: false }" class="relative flex items-center">
    <button 
        @click="open = !open"
        type="button"
        class="flex items-center justify-center w-9 h-9 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-white/5 text-gray-500 shadow-sm border border-gray-200 dark:border-white/10"
        title="{{ __('Switch Language') }}"
    >
        @if(app()->getLocale() === 'en')
            <span class="text-lg">🇬🇧</span>
        @else
            <span class="text-lg">🇰🇭</span>
        @endif
    </button>

    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 top-full mt-2 w-40 rounded-xl bg-white dark:bg-gray-900 shadow-xl border border-gray-100 dark:border-white/10 z-50 p-1"
        style="display: none;"
    >
        <a 
            href="{{ route('lang.switch', 'en') }}" 
            class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors {{ app()->getLocale() === 'en' ? 'bg-primary-50/50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300' }}"
        >
            <div class="flex items-center gap-x-2">
                <span class="text-base">🇬🇧</span>
                <span class="text-sm font-medium">English</span>
            </div>
            @if(app()->getLocale() === 'en')
                <x-heroicon-m-check class="w-4 h-4" />
            @endif
        </a>

        <a 
            href="{{ route('lang.switch', 'km') }}" 
            class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors {{ app()->getLocale() === 'km' ? 'bg-primary-50/50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300' }}"
        >
            <div class="flex items-center gap-x-2">
                <span class="text-base">🇰🇭</span>
                <span class="text-sm font-medium">ភាសាខ្មែរ</span>
            </div>
            @if(app()->getLocale() === 'km')
                <x-heroicon-m-check class="w-4 h-4" />
            @endif
        </a>
    </div>
</div>
