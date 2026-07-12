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
                    <div style="max-width: 400px; margin-top: 12px;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 6px;">
                            {{ __('2FA Code') }}
                            @if(!auth()->user()->getAppAuthenticationSecret())
                                <span style="font-size: 0.75rem; color: var(--gray-400); font-weight: normal;">
                                    ({{ __('Set up 2FA in Profile first') }})
                                </span>
                            @endif
                        </label>
                        <input
                            type="text"
                            wire:model="totpCode"
                            placeholder="{{ __('6-digit code from authenticator app') }}"
                            class="fi-input"
                            style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 6px; letter-spacing: 4px; font-size: 1.1rem;"
                            maxlength="6"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            {{ auth()->user()->getAppAuthenticationSecret() ? 'required' : '' }}
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

            {{-- Bulk Presets --}}
            <div class="mt-6">
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-bolt style="width: 18px; height: 18px; color: #8b5cf6;" />
                            {{ __('Bulk Presets') }}
                        </div>
                    </x-slot>
                    <x-slot name="description">
                        {{ __('Run multiple commands in sequence with one click.') }}
                    </x-slot>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                        @foreach(\App\Filament\Pages\ArtisanConsole::bulkPresets() as $key => $preset)
                            <div
                                wire:click="executeBulk('{{ $key }}')"
                                style="padding: 14px 16px; border: 1px solid var(--gray-200); border-radius: 8px; cursor: pointer; transition: all 0.15s;"
                                onmouseover="this.style.borderColor='#6366f1'; this.style.background='rgba(99,102,241,0.04)'"
                                onmouseout="this.style.borderColor='var(--gray-200)'; this.style.background='transparent'"
                            >
                                <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">{{ $preset['label'] }}</p>
                                <p style="font-size: 0.7rem; color: var(--gray-400); margin: 4px 0 0;">
                                    {{ implode(' → ', $preset['commands']) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            </div>

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

                        {{-- Download Backup Button (shown after backup:database command) --}}
                        @if($executedCommand === 'backup:database' && $this->latestBackupFile)
                            <div style="margin-top: 12px;">
                                <a
                                    href="{{ url('/admin/backup/download/' . $this->latestBackupFile) }}"
                                    target="_blank"
                                    style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #16a34a; color: #ffffff; border-radius: 6px; font-size: 0.875rem; font-weight: 500; text-decoration: none; transition: background 0.15s;"
                                    onmouseover="this.style.background='#15803d'"
                                    onmouseout="this.style.background='#16a34a'"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    {{ __('Download Backup') }} ({{ $this->latestBackupFile }})
                                </a>
                            </div>
                        @endif
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
                            <span style="font-weight: 600;">{{ auth()->user()?->email ?? 'N/A' }}</span>
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
