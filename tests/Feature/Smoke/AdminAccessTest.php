<?php

namespace Tests\Feature\Smoke;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSee('Operational overview');
    }
}
