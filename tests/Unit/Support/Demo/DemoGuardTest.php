<?php

namespace Tests\Unit\Support\Demo;

use App\Support\Demo\DemoGuard;
use RuntimeException;
use Tests\LightweightTestCase;

class DemoGuardTest extends LightweightTestCase
{
    protected function tearDown(): void
    {
        config([
            'demo.enabled' => false,
            'demo.allow_in_production' => false,
        ]);

        parent::tearDown();
    }

    public function test_demo_mode_is_disabled_by_default(): void
    {
        $this->assertFalse(DemoGuard::isEnabled());
    }

    public function test_protected_user_emails_are_detected(): void
    {
        $this->assertTrue(DemoGuard::isProtectedEmail('demo-admin@contentflow.test'));
        $this->assertFalse(DemoGuard::isProtectedEmail('other@example.com'));
    }

    public function test_production_demo_without_override_throws(): void
    {
        config([
            'demo.enabled' => true,
            'demo.allow_in_production' => false,
        ]);

        app()->detectEnvironment(fn (): string => 'production');

        $this->expectException(RuntimeException::class);

        DemoGuard::ensureSafeConfiguration();
    }

    public function test_production_demo_with_override_is_allowed(): void
    {
        config([
            'demo.enabled' => true,
            'demo.allow_in_production' => true,
        ]);

        app()->detectEnvironment(fn (): string => 'production');

        DemoGuard::ensureSafeConfiguration();

        $this->assertTrue(DemoGuard::isEnabled());
    }
}
