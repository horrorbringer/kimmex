<div class="flex items-center gap-2 mr-4">
    <x-filament::button
        tag="a"
        href="{{ route('lang.switch', 'en') }}"
        color="{{ app()->getLocale() === 'en' ? 'primary' : 'gray' }}"
        size="sm"
        outlined
    >
        EN
    </x-filament::button>

    <x-filament::button
        tag="a"
        href="{{ route('lang.switch', 'km') }}"
        color="{{ app()->getLocale() === 'km' ? 'primary' : 'gray' }}"
        size="sm"
        outlined
    >
        KH
    </x-filament::button>
</div>