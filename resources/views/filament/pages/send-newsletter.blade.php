<x-filament-panels::page>
    <div class="fi-page-content-ctn">
        {{-- Send Form --}}
        <form wire:submit="send">
            {{ $this->form }}

            {{-- Article Preview --}}
            @if($showPreview && $previewData)
                <x-filament::section class="mt-6">
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-eye class="fi-icon fi-size-md" />
                            {{ __('Email Preview') }}
                        </div>
                    </x-slot>

                    <div class="flex flex-col gap-4 sm:flex-row">
                        @if($previewData['coverImage'])
                            <div class="flex-shrink-0">
                                <img src="{{ url($previewData['coverImage']) }}"
                                     alt="{{ $previewData['title'] }}"
                                     style="width: 128px; height: 96px; object-fit: cover; border-radius: 8px;" />
                            </div>
                        @endif
                        <div style="min-width: 0; flex: 1;">
                            <p class="fi-section-header-heading" style="font-size: 1rem;">
                                {{ $previewData['title'] }}
                            </p>
                            @if($previewData['excerpt'])
                                <p style="margin-top: 4px; font-size: 0.875rem; color: var(--gray-500); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $previewData['excerpt'] }}
                                </p>
                            @endif
                            <div style="margin-top: 8px; display: flex; align-items: center; gap: 12px; font-size: 0.75rem; color: var(--gray-400);">
                                @if($previewData['category'])
                                    <x-filament::badge color="primary" size="sm">
                                        {{ $previewData['category'] }}
                                    </x-filament::badge>
                                @endif
                                @if($previewData['publishedAt'])
                                    <span>{{ $previewData['publishedAt'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endif

            {{-- Duplicate Warning --}}
            @if($alreadySent)
                <div class="mt-6">
                    <x-filament::section>
                        <div class="flex gap-3">
                            <x-heroicon-o-exclamation-triangle style="width: 20px; height: 20px; color: #d97706; flex-shrink: 0;" />
                            <div>
                                <p style="font-size: 0.875rem; font-weight: 600; color: #92400e;">
                                    {{ __('Already Sent') }}
                                </p>
                                <p style="font-size: 0.875rem; color: #a16207; margin-top: 4px;">
                                    {{ $lastSentInfo }}
                                </p>
                                <p style="font-size: 0.75rem; color: #b45309; margin-top: 8px;">
                                    {{ __('You can still re-send by clicking "Send Anyway" below.') }}
                                </p>
                            </div>
                        </div>
                    </x-filament::section>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="mt-6 flex flex-wrap items-center gap-4" style="padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                @if($alreadySent)
                    <x-filament::button
                        type="button"
                        wire:click="forceSend"
                        color="warning"
                        icon="heroicon-o-arrow-path"
                    >
                        {{ __('Send Anyway') }}
                    </x-filament::button>
                @else
                    <x-filament::button
                        type="submit"
                        color="primary"
                        icon="heroicon-o-paper-airplane"
                    >
                        {{ __('Send to All Subscribers') }}
                    </x-filament::button>
                @endif

                <p style="font-size: 0.875rem; color: var(--gray-500);">
                    <span style="font-weight: 600; color: var(--gray-700);">{{ $this->activeSubscriberCount }}</span>
                    {{ __('active subscribers') }}
                </p>
            </div>
        </form>

        {{-- Recent Sends History --}}
        @if($this->recentSends->isNotEmpty())
            <div class="mt-8">
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-clock class="fi-icon fi-size-md" />
                            {{ __('Recent Sends') }}
                        </div>
                    </x-slot>

                    <div style="display: flex; flex-direction: column;">
                        @foreach($this->recentSends as $send)
                            <div style="padding: 12px 0; display: flex; align-items: center; justify-content: space-between; gap: 16px; {{ !$loop->last ? 'border-bottom: 1px solid var(--gray-100);' : '' }}">
                                <div style="min-width: 0; flex: 1;">
                                    <p style="font-size: 0.875rem; font-weight: 500; color: var(--gray-900); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $send->article?->getTranslation('title', 'en') ?? __('Deleted Article') }}
                                    </p>
                                    <p style="font-size: 0.75rem; color: var(--gray-500); margin-top: 2px;">
                                        {{ $send->sent_at?->format('M d, Y H:i') ?? $send->created_at->format('M d, Y H:i') }}
                                        @if($send->sender)
                                            · {{ __('by') }} {{ $send->sender->name }}
                                        @endif
                                    </p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
                                    <span style="font-size: 0.75rem; color: var(--gray-500);">
                                        {{ $send->sent_count }}/{{ $send->subscriber_count }}
                                    </span>
                                    @php
                                        $statusColor = match($send->status) {
                                            'completed' => 'success',
                                            'sending' => 'warning',
                                            'failed' => 'danger',
                                            default => 'gray',
                                        };
                                    @endphp
                                    <x-filament::badge :color="$statusColor" size="sm">
                                        {{ ucfirst($send->status) }}
                                    </x-filament::badge>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            </div>
        @endif
    </div>
</x-filament-panels::page>
