<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LanguageSwitchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_language_query_switches_the_locale_without_a_redirect(): void
    {
        Route::middleware('web')->get('/language-performance-test', fn () => response()->json([
            'locale' => app()->getLocale(),
        ]));

        $this->get('/language-performance-test?lang=en')
            ->assertOk()
            ->assertJsonPath('locale', 'en')
            ->assertSessionHas('locale', 'en');
    }

    public function test_the_admin_language_switcher_uses_the_standard_language_switch_route(): void
    {
        Route::middleware('web')->get('/admin-language-switcher-test', fn () => view('filament.components.language-switcher'));

        $this->withServerVariables([
            'HTTP_REFERER' => 'http://kimmex.test/admin/news?filters=active',
        ])->get('/admin-language-switcher-test')
            ->assertOk()
            ->assertSee('http://kimmex.test/lang/en', false)
            ->assertSee('http://kimmex.test/lang/km', false);
    }
}
