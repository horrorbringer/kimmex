<?php

namespace Tests\Feature;

use App\Support\RichContent;
use Tests\TestCase;

class RichContentRenderingTest extends TestCase
{
    public function test_project_content_repairs_malformed_legacy_list_markup(): void
    {
        $content = '<ul><li>ជាន់៖ 17 ជាន់ <urli><li><b> ខែ</li></ul>';

        $rendered = RichContent::renderProject($content, 'list');

        $this->assertStringNotContainsString('<urli', $rendered);
        $this->assertStringContainsString('<ul><li>ជាន់៖ 17 ជាន់ </li><li><b> ខែ</li></ul>', $rendered);
    }
}
