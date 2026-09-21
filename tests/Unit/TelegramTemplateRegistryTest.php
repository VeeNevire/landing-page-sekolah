<?php

namespace Tests\Unit;

use App\Services\TelegramTemplateRegistry;
use Tests\TestCase;

class TelegramTemplateRegistryTest extends TestCase
{
    public function test_it_exposes_event_specific_template_parameters(): void
    {
        $registry = app(TelegramTemplateRegistry::class);

        $this->assertContains('nilai', $registry->allKeys('grade'));
        $this->assertContains('kkm', $registry->allKeys('grade'));
        $this->assertNotContains('status_kehadiran', $registry->allKeys('grade'));
        $this->assertContains('status', $registry->allKeys('attendance'));
        $this->assertNotContains('nilai', $registry->allKeys('attendance'));
    }
}
