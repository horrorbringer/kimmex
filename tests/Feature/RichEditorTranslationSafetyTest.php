<?php

namespace Tests\Feature;

use App\Services\AutoTranslateService;
use Illuminate\Support\Facades\File;
use Mockery;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Tests\TestCase;

class RichEditorTranslationSafetyTest extends TestCase
{
    public function test_it_translates_rich_editor_text_without_sending_html_to_the_translator(): void
    {
        $googleTranslate = Mockery::mock(GoogleTranslate::class);
        $googleTranslate->shouldReceive('setSource')->twice()->with('en');
        $googleTranslate->shouldReceive('setTarget')->times(3)->with('km');
        $googleTranslate->shouldReceive('translate')
            ->once()
            ->with('Install and test the distribution board.')
            ->andReturn('ដំឡើង និងសាកល្បងផ្ទាំងចែកចាយអគ្គិសនី។');
        $googleTranslate->shouldReceive('translate')
            ->once()
            ->with('Follow quality standards.')
            ->andReturn('អនុវត្តតាមស្តង់ដារគុណភាព។');

        $service = new AutoTranslateService($googleTranslate);

        $translated = $service->translateFrom(
            '<ul><li><p>Install and test the distribution board.</p></li><li><p>Follow quality standards.</p></li></ul>',
            'km',
            'en',
        );

        $this->assertStringContainsString('<ul>', $translated);
        $this->assertStringContainsString('<li><p>ដំឡើង និងសាកល្បងផ្ទាំងចែកចាយអគ្គិសនី។</p></li>', $translated);
        $this->assertStringContainsString('<li><p>អនុវត្តតាមស្តង់ដារគុណភាព។</p></li>', $translated);
    }

    public function test_it_recognizes_rich_editor_html_for_background_translation_protection(): void
    {
        $googleTranslate = Mockery::mock(GoogleTranslate::class);
        $googleTranslate->shouldReceive('setSource')->once()->with('en');
        $googleTranslate->shouldReceive('setTarget')->once()->with('km');

        $service = new AutoTranslateService($googleTranslate);

        $this->assertTrue($service->containsHtml('<p>Editor content</p>'));
        $this->assertFalse($service->containsHtml('Short plain text'));
        $this->assertStringContainsString('containsHtml($currentEn)', file_get_contents(app_path('Jobs/AutoTranslateModel.php')));
    }

    public function test_translation_actions_open_the_translated_locale_for_editor_review(): void
    {
        $helper = File::get(app_path('Filament/Support/TranslationHelper.php'));

        $this->assertStringContainsString('$sourceText = $get($sourceField) ?? $state;', $helper);
        $this->assertStringContainsString('$translatedData = self::translateLocaleData($translator, $sourceData, $resolvedTargetLocale, $sourceLocale);', $helper);
        $this->assertStringContainsString('...self::recordLocaleData($record, $resolvedTargetLocale),', $helper);
        $this->assertStringContainsString('...$translatedData,', $helper);
        $this->assertStringContainsString('setActiveLocale($resolvedTargetLocale)', $helper);
        $this->assertStringContainsString("self::activeLocale(\$livewire) === 'km' ? '🇬🇧 '.__('To EN') : '🇰🇭 '.__('To KH')", $helper);
        $this->assertStringContainsString('$sourceLocale = self::activeLocale($livewire, (string) $sourceText);', $helper);
    }

    public function test_it_does_not_translate_code_blocks_and_pre_tags(): void
    {
        $googleTranslate = Mockery::mock(GoogleTranslate::class);
        $googleTranslate->shouldReceive('setSource')->twice()->with('en');
        $googleTranslate->shouldReceive('setTarget')->times(3)->with('km');
        $googleTranslate->shouldReceive('translate')
            ->once()
            ->with('Here is our API endpoint:')
            ->andReturn('នេះគឺជាចំណុចបញ្ចប់ API របស់យើង៖');

        $service = new AutoTranslateService($googleTranslate);

        $html = '<p>Here is our API endpoint:</p><pre><code>const url = "https://kimmex.com/api";</code></pre>';
        $translated = $service->translateFrom($html, 'km', 'en');

        $this->assertStringContainsString('នេះគឺជាចំណុចបញ្ចប់ API របស់យើង៖', $translated);
        $this->assertStringContainsString('<pre><code>const url = "https://kimmex.com/api";</code></pre>', $translated);
    }
}
