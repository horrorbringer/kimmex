<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SitemapPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    /**
     * Test that human-readable HTML sitemap loads successfully.
     */
    public function test_html_sitemap_page_loads_successfully(): void
    {
        $response = $this->get('/sitemap');

        $response->assertOk();
        $response->assertSee('Site');
        $response->assertSee('Map');
        $response->assertSee('/sitemap.xml');
        $response->assertSee('/about');
        $response->assertSee('/services');
        $response->assertSee('/projects');
        $response->assertSee('/careers');
        $response->assertSee('/contact');
    }

    /**
     * Test that XML sitemap loads as valid XML.
     */
    public function test_xml_sitemap_returns_xml_response(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<urlset', false);
        $response->assertSee(url('/'), false);
        $response->assertSee(url('/sitemap'), false);
    }
}
