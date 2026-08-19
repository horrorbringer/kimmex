<?php

return [
    'title' => 'ចូលប្រើប្រាស់',
    'heading' => 'ចូលប្រើប្រាស់គណនីរបស់អ្នក',
    'form' => [
        'email' => [
            'label' => 'អាសយដ្ឋានអ៊ីមែល',
        ],
        'password' => [
            'label' => 'ពាក្យសម្ងាត់',
        ],
        'remember' => [
            'label' => 'ចងចាំគណនីនេះ',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'ចូលប្រើប្រាស់',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => 'ឬ',
            'label' => 'ចុះឈ្មោះគណនីថ្មី',
        ],
        'request_password_reset' => [
            'label' => 'ភ្លេចពាក្យសម្ងាត់?',
        ],
    ],
    'messages' => [
        'failed' => 'ព័ត៌មានសម្គាល់មិនត្រឹមត្រូវទេ។',
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'ការប៉ុនប៉ងច្រើនដងពេក',
            'body' => 'សូមព្យាយាមម្តងទៀតក្នុងរយៈពេល :seconds វិនាទី។',
        ],
    ],
];
