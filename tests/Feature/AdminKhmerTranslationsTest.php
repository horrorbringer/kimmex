<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminKhmerTranslationsTest extends TestCase
{
    public function test_core_admin_labels_have_khmer_translations(): void
    {
        $translations = json_decode(File::get(lang_path('km.json')), true, 512, JSON_THROW_ON_ERROR);

        foreach ([
            'Administration',
            'Projects',
            'Services',
            'News',
            'Job Applications',
            'Employees',
            'Users',
            'Page Analytics',
            'System-Wide Settings',
            'Telegram Bot Alerts',
        ] as $label) {
            $this->assertArrayHasKey($label, $translations);
            $this->assertNotSame($label, $translations[$label]);
        }
    }
}
