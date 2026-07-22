<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LanguageSwitchingTest extends TestCase
{
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
}
