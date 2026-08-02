<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Quick Actions') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Start the tasks you use most often.') }}
        </x-slot>

        @php
            $actions = [
                ['url' => '/admin/news-articles/create', 'icon' => 'heroicon-o-newspaper', 'label' => __('New Article'), 'description' => __('Publish a news update')],
                ['url' => '/admin/projects/create', 'icon' => 'heroicon-o-building-office-2', 'label' => __('New Project'), 'description' => __('Add a portfolio project')],
                ['url' => '/admin/job-postings/create', 'icon' => 'heroicon-o-briefcase', 'label' => __('Post Job'), 'description' => __('Create a new vacancy')],
                ['url' => '/admin/send-newsletter', 'icon' => 'heroicon-o-paper-airplane', 'label' => __('Newsletter'), 'description' => __('Prepare an email campaign')],
                ['url' => '/admin/inquiries', 'icon' => 'heroicon-o-inbox', 'label' => __('Inquiries'), 'description' => __('Review messages from visitors')],
                ['url' => '/admin/analytics', 'icon' => 'heroicon-o-chart-bar-square', 'label' => __('Analytics'), 'description' => __('Explore site performance')],
            ];
        @endphp

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr)); gap: 0.5rem;">
            @foreach($actions as $action)
                <x-filament::button
                    tag="a"
                    :href="$action['url']"
                    :icon="$action['icon']"
                    :tooltip="$action['description']"
                    color="gray"
                    size="sm"
                    outlined
                    style="justify-content: flex-start; width: 100%;"
                >
                    {{ $action['label'] }}
                </x-filament::button>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
