<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
