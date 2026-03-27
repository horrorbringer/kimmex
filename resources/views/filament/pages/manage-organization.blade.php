<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="mt-8 flex items-center justify-end gap-x-3">
            <x-filament::button type="submit" color="primary" icon="heroicon-o-check-circle">
                {{ __('Save Organization Profile') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>