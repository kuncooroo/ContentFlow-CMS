<?php

namespace Tests\Feature\Regression;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModuleAuthorizationRegressionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return array<string, array{0: string, 1: callable(): User|null, 2: int}>
     */
    public static function protectedAdminRouteProvider(): array
    {
        return [
            'guest redirected from dashboard' => ['admin.dashboard', fn (): null => null, 302],
            'guest redirected from users' => ['admin.users.index', fn (): null => null, 302],
            'author forbidden from users' => ['admin.users.index', fn (): User => User::factory()->author()->create(), 403],
            'author forbidden from roles' => ['admin.roles.index', fn (): User => User::factory()->author()->create(), 403],
            'author forbidden from pages' => ['admin.pages.index', fn (): User => User::factory()->author()->create(), 403],
            'author forbidden from settings' => ['admin.settings.general', fn (): User => User::factory()->author()->create(), 403],
            'author forbidden from audit' => ['admin.audit.index', fn (): User => User::factory()->author()->create(), 403],
            'editor forbidden from users' => ['admin.users.index', fn (): User => User::factory()->editor()->create(), 403],
            'editor forbidden from settings' => ['admin.settings.general', fn (): User => User::factory()->editor()->create(), 403],
            'editor forbidden from audit' => ['admin.audit.index', fn (): User => User::factory()->editor()->create(), 403],
            'editor forbidden from menus' => ['admin.menus.index', fn (): User => User::factory()->editor()->create(), 403],
            'author can access posts' => ['admin.posts.index', fn (): User => User::factory()->author()->create(), 200],
            'author can access media' => ['admin.media.index', fn (): User => User::factory()->author()->create(), 200],
            'editor can access comments' => ['admin.comments.index', fn (): User => User::factory()->editor()->create(), 200],
            'administrator can access settings' => ['admin.settings.general', fn (): User => User::factory()->administrator()->create(), 200],
        ];
    }

    #[DataProvider('protectedAdminRouteProvider')]
    public function test_protected_admin_route_authorization(string $routeName, callable $userFactory, int $expectedStatus): void
    {
        $user = $userFactory();

        $request = $this->get(route($routeName));

        if ($user !== null) {
            $request = $this->actingAs($user)->get(route($routeName));
        }

        $request->assertStatus($expectedStatus);
    }
}
