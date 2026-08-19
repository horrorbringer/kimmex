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

    public function test_project_content_repairs_bullet_paragraphs_and_ignores_malformed_links(): void
    {
        $content = '<p>• First responsibility<br>• Second responsibility</p><p><a href="https://example.com>Broken link text</a></p>';

        $rendered = RichContent::renderProject($content);

        $this->assertStringContainsString('<ul><li>First responsibility</li><li>Second responsibility</li></ul>', $rendered);
        $this->assertStringContainsString('Broken link text</a>', $rendered);
        $this->assertStringNotContainsString('<a href="https://example.com>', $rendered);
    }

    public function test_news_content_preserves_code_blocks_and_pre_tags(): void
    {
        $content = '<pre><code>function helloWorld() { return true; }</code></pre>';

        $rendered = RichContent::render($content);

        $this->assertStringContainsString('<pre translate="no" class="notranslate"><code translate="no" class="notranslate">function helloWorld() { return true; }</code></pre>', $rendered);
    }
}
