<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ContactInquiryNotificationTest extends TestCase
{
    public function test_contact_inquiries_store_and_send_the_visitor_ip_once(): void
    {
        $controller = File::get(app_path('Http/Controllers/FormController.php'));
        $observer = File::get(app_path('Observers/InquiryObserver.php'));
        $telegram = File::get(app_path('Services/TelegramService.php'));
        $migration = File::get(database_path('migrations/2026_07_27_152406_add_ip_address_to_inquiries_table.php'));

        $this->assertStringContainsString("'ip_address' => \$request->ip()", $controller);
        $this->assertStringNotContainsString('notifyInquiry([', $controller);
        $this->assertStringContainsString("'ip_address' => \$inquiry->ip_address", $observer);
        $this->assertStringContainsString("'🌐 *IP Address:* '", $telegram);
        $this->assertStringContainsString("string('ip_address', 45)", $migration);
    }
}
