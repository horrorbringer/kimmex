<?php

namespace Tests\Feature;

use Tests\TestCase;

class NewsArticleRichEditorMediaTest extends TestCase
{
    public function test_news_article_form_contains_external_media_action(): void
    {
        $formFile = file_get_contents(app_path('Filament/Resources/NewsArticles/Schemas/NewsArticleForm.php'));

        $this->assertStringContainsString('getInsertExternalMediaAction', $formFile);
        $this->assertStringContainsString('self::getInsertExternalMediaAction(\'content_en\')', $formFile);
        $this->assertStringContainsString('self::getInsertExternalMediaAction(\'content_km\')', $formFile);
        $this->assertStringContainsString('Insert External Media or Link', $formFile);
    }
}
