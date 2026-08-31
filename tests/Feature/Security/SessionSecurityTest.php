<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Support\Content\RichContentPolicy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SessionSecurityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_logout_invalidates_session(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $sessionId = session()->getId();

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertNotSame($sessionId, session()->getId());
    }

    public function test_rich_content_policy_disallows_raw_html(): void
    {
        $this->assertSame('plain_escaped', RichContentPolicy::mode());
        $this->assertFalse(RichContentPolicy::allowsHtml());
    }
}
