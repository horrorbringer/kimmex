<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Send Form --}}
        <form wire:submit="send" class="space-y-6">
            {{ $this->form }}

            {{-- Article Preview --}}
            @if($showPreview && $previewData)
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                            <x-heroicon-o-eye class="w-4 h-4" />
                            {{ __('Email Preview') }}
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            @if($previewData['coverImage'])
                                <div class="sm:w-32 flex-shrink-0">
                                    <img src="{{ url($previewData['coverImage']) }}"
                                         alt="{{ $previewData['title'] }}"
                                         class="w-full sm:w-32 h-24 object-cover rounded-lg" />
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-bold text-gray-900 dark:text-white truncate">
                                    {{ $previewData['title'] }}
                                </h4>
                                @if($previewData['excerpt'])
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                        {{ $previewData['excerpt'] }}
                                    </p>
                                @endif
                                <div class="mt-2 flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                                    @if($previewData['category'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
                                            {{ $previewData['category'] }}
                                        </span>
                                    @endif
                                    @if($previewData['publishedAt'])
                                        <span>{{ $previewData['publishedAt'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Duplicate Warning --}}
            @if($alreadySent)
                <div class="rounded-lg border border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/20 p-4">
                    <div class="flex gap-3">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                        <div>
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                {{ __('Already Sent') }}
                            </p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                {{ $lastSentInfo }}
                            </p>
                            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-2">
                                {{ __('You can still re-send by clicking "Send Anyway" below.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
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

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $this->activeSubscriberCount }}</span>
                    {{ __('active subscribers') }}
                </p>
            </div>
        </form>

        {{-- Recent Sends History --}}
        @if($this->recentSends->isNotEmpty())
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                        <x-heroicon-o-clock class="w-4 h-4" />
                        {{ __('Recent Sends') }}
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($this->recentSends as $send)
                        <div class="px-4 py-3 flex items-center justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $send->article?->getTranslation('title', 'en') ?? __('Deleted Article') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $send->sent_at?->format('M d, Y H:i') ?? $send->created_at->format('M d, Y H:i') }}
                                    @if($send->sender)
                                        · {{ __('by') }} {{ $send->sender->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
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
            </div>
        @endif
    </div>
</x-filament-panels::page>
