<?php

namespace Tests\Feature;

use App\Rules\Turnstile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TurnstileValidationTest extends TestCase
{
    public function test_turnstile_passes_when_secret_is_not_configured(): void
    {
        config(['services.turnstile.secret' => null]);

        $validator = Validator::make(
            ['cf-turnstile-response' => null],
            ['cf-turnstile-response' => new Turnstile]
        );

        $this->assertFalse($validator->fails());
    }

    public function test_turnstile_fails_when_secret_is_configured_but_token_is_missing(): void
    {
        config(['services.turnstile.secret' => 'dummy_secret']);

        $validator = Validator::make(
            ['cf-turnstile-response' => ''],
            ['cf-turnstile-response' => new Turnstile]
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cf-turnstile-response', $validator->errors()->toArray());
    }

    public function test_turnstile_passes_when_cloudflare_verifies_successfully(): void
    {
        config(['services.turnstile.secret' => 'valid_secret']);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'challenge_ts' => now()->toIso8601String(),
                'hostname' => 'kimmex.com.kh',
            ], 200),
        ]);

        $validator = Validator::make(
            ['cf-turnstile-response' => 'valid_token_from_client'],
            ['cf-turnstile-response' => new Turnstile]
        );

        $this->assertFalse($validator->fails());
    }

    public function test_turnstile_fails_when_cloudflare_verification_fails(): void
    {
        config(['services.turnstile.secret' => 'valid_secret']);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $validator = Validator::make(
            ['cf-turnstile-response' => 'fake_or_expired_token'],
            ['cf-turnstile-response' => new Turnstile]
        );

        $this->assertTrue($validator->fails());
    }
}
