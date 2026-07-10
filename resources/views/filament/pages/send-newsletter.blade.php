<x-filament-panels::page>
    <form wire:submit="send" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center gap-4 pt-4 border-t">
            <x-filament::button type="submit" color="primary" icon="heroicon-o-paper-airplane">
                {{ __('Send to All Subscribers') }}
            </x-filament::button>

            <p class="text-sm text-gray-500">
                {{ \App\Models\Subscriber::active()->count() }} {{ __('active subscribers') }}
            </p>
        </div>
    </form>
</x-filament-panels::page>
