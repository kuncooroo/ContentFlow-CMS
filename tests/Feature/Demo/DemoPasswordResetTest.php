<?php

namespace Tests\Feature\Demo;

use Illuminate\Support\Facades\Config;
use Tests\LightweightTestCase;

class DemoPasswordResetTest extends LightweightTestCase
{
    protected function tearDown(): void
    {
        Config::set('demo.enabled', false);

        parent::tearDown();
    }

    public function test_password_reset_routes_redirect_when_demo_mode_enabled(): void
    {
        Config::set('demo.enabled', true);

        $this->get(route('password.request'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }
}
