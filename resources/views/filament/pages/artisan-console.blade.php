<x-filament-panels::page>
    <div class="fi-page-content-ctn">

        {{-- Lock Screen --}}
        @if(!$unlocked)
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-lock-closed style="width: 20px; height: 20px; color: #dc2626;" />
                        {{ __('Console Locked') }}
                    </div>
                </x-slot>
                <x-slot name="description">
                    {{ __('Enter your admin password to unlock the Artisan Console. All actions are logged.') }}
                </x-slot>

                <form wire:submit="unlock">
                    <div style="max-width: 400px;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 6px;">
                            {{ __('Admin Password') }}
                        </label>
                        <input
                            type="password"
                            wire:model="password"
                            placeholder="{{ __('Enter your password...') }}"
                            class="fi-input"
                            style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 6px;"
                            autocomplete="current-password"
                            required
                        />
                    </div>
                    <div style="margin-top: 16px;">
                        <x-filament::button type="submit" color="danger" icon="heroicon-o-lock-open">
                            {{ __('Unlock Console') }}
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::section>

        @else
            {{-- Unlocked Console --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-command-line style="width: 20px; height: 20px; color: #16a34a;" />
                        {{ __('Artisan Console') }}
                        <x-filament::badge color="success" size="sm">{{ __('Unlocked') }}</x-filament::badge>
                    </div>
                </x-slot>
                <x-slot name="description">
                    {{ __('Execute whitelisted artisan commands. All actions are logged for audit.') }}
                </x-slot>
                <x-slot name="headerEnd">
                    <x-filament::button
                        wire:click="lock"
                        color="gray"
                        size="sm"
                        icon="heroicon-o-lock-closed"
                    >
                        {{ __('Lock') }}
                    </x-filament::button>
                </x-slot>

                <form wire:submit="execute">
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
                        <div style="flex: 1; min-width: 300px;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 6px;">
                                {{ __('Select Command') }}
                            </label>
                            <select
                                wire:model="command"
                                class="fi-input"
                                style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 6px; background: white;"
                            >
                                <option value="">{{ __('-- Choose a command --') }}</option>
                                @foreach(\App\Filament\Pages\ArtisanConsole::allowedCommands() as $cmd => $description)
                                    <option value="{{ $cmd }}">{{ $description }} ({{ $cmd }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-filament::button type="submit" color="primary" icon="heroicon-o-play">
                                {{ __('Execute') }}
                            </x-filament::button>
                        </div>
                    </div>
                </form>
            </x-filament::section>

            {{-- Output --}}
            @if($output !== null)
                <div class="mt-6">
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-document-text style="width: 18px; height: 18px;" />
                                {{ __('Output') }}
                                @if($executedCommand)
                                    <code style="font-size: 0.75rem; background: var(--gray-100); padding: 2px 8px; border-radius: 4px; color: var(--gray-600);">
                                        {{ $executedCommand }}
                                    </code>
                                @endif
                                @if($executedAt)
                                    <span style="font-size: 0.75rem; color: var(--gray-400);">@ {{ $executedAt }}</span>
                                @endif
                            </div>
                        </x-slot>

                        <pre style="background: #1e293b; color: #e2e8f0; padding: 16px; border-radius: 8px; font-size: 0.8rem; font-family: 'JetBrains Mono', 'Fira Code', monospace; overflow-x: auto; white-space: pre-wrap; word-break: break-word; max-height: 400px; overflow-y: auto; line-height: 1.6;">{{ $output ?: __('(no output)') }}</pre>
                    </x-filament::section>
                </div>
            @endif

            {{-- Security Notice --}}
            <div class="mt-6">
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-shield-check style="width: 18px; height: 18px; color: #2563eb;" />
                            {{ __('Security Info') }}
                        </div>
                    </x-slot>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 0.875rem;">
                        <div>
                            <span style="color: var(--gray-500);">{{ __('User') }}:</span>
                            <span style="font-weight: 600;">{{ auth()->user()->email }}</span>
                        </div>
                        <div>
                            <span style="color: var(--gray-500);">{{ __('Role') }}:</span>
                            <x-filament::badge color="danger" size="sm">ADMIN</x-filament::badge>
                        </div>
                        <div>
                            <span style="color: var(--gray-500);">{{ __('IP') }}:</span>
                            <span style="font-weight: 600;">{{ request()->ip() }}</span>
                        </div>
                        <div>
                            <span style="color: var(--gray-500);">{{ __('Session') }}:</span>
                            <span style="font-weight: 600;">{{ now()->format('M d, H:i') }}</span>
                        </div>
                    </div>
                </x-filament::section>
            </div>
        @endif
    </div>
</x-filament-panels::page>
