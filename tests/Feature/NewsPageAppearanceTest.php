<?php

namespace Tests\Feature;

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Models\NewsArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NewsPageAppearanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_news_archive_is_available(): void
    {
        $response = $this->get(route('news.index'));

        $response
            ->assertOk()
            ->assertSee('News');
    }

    public function test_the_news_article_show_page_is_available(): void
    {
        Queue::fake();

        $article = NewsArticle::create([
            'title' => ['en' => 'Test Article', 'km' => 'អត្ថបទសាកល្បង'],
            'slug' => 'test-article',
            'category' => 'Updates',
            'content' => ['en' => '<p>Article content</p>', 'km' => '<p>ខ្លឹមសារ</p>'],
            'isActive' => true,
            'publishedAt' => now()->subDay(),
        ]);

        $job = JobPosting::create([
            'title' => ['en' => 'Civil Engineer', 'km' => 'វិស្វករ'],
            'slug' => 'civil-engineer',
            'status' => JobPostingStatus::OPEN,
            'type' => 'FULL_TIME',
            'location' => ['en' => 'Phnom Penh', 'km' => 'ភ្នំពេញ'],
        ]);

        $response = $this->get(route('news.show', ['slug' => 'test-article']));

        $response
            ->assertOk()
            ->assertSee($article->getTranslation('title', app()->getLocale()));
    }
}
