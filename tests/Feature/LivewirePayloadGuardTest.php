<?php

namespace Tests\Feature;

use Tests\TestCase;

class LivewirePayloadGuardTest extends TestCase
{
    public function test_livewire_allows_legitimate_rich_editor_nesting_while_keeping_a_payload_guard(): void
    {
        $this->assertSame(20, config('livewire.payload.max_nesting_depth'));
        $this->assertNotNull(config('livewire.payload.max_nesting_depth'));
    }
}
