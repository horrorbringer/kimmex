<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Quick Actions') }}
        </x-slot>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
            @php
                $actions = [
                    ['url' => '/admin/news-articles/create', 'icon' => '📝', 'label' => __('New Article')],
                    ['url' => '/admin/projects/create', 'icon' => '🏗️', 'label' => __('New Project')],
                    ['url' => '/admin/job-postings/create', 'icon' => '💼', 'label' => __('Post Job')],
                    ['url' => '/admin/send-newsletter', 'icon' => '📨', 'label' => __('Newsletter')],
                    ['url' => '/admin/inquiries', 'icon' => '📬', 'label' => __('Inquiries')],
                    ['url' => '/admin/analytics', 'icon' => '📊', 'label' => __('Analytics')],
                ];
            @endphp

            @foreach($actions as $action)
                <a href="{{ $action['url'] }}"
                   style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--gray-200); text-decoration: none; transition: all 0.15s ease; background: white;"
                   onmouseover="this.style.borderColor='#6366f1'; this.style.background='rgba(99,102,241,0.03)'; this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.borderColor='var(--gray-200)'; this.style.background='white'; this.style.transform='none'">
                    <span style="font-size: 1.25rem;">{{ $action['icon'] }}</span>
                    <span style="font-size: 0.8125rem; font-weight: 600; color: var(--gray-700);">{{ $action['label'] }}</span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
