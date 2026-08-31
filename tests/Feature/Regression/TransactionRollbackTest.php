<?php

namespace Tests\Feature\Regression;

use App\Actions\Navigation\UpdateMenuStructure;
use App\Actions\Users\CreateUser;
use App\Enums\MenuItemType;
use App\Enums\UserStatus;
use App\Models\Menu;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TransactionRollbackTest extends TestCase
{
    use DatabaseTransactions;

    public function test_menu_structure_update_rolls_back_when_audit_logging_fails(): void
    {
        $admin = User::factory()->administrator()->create();
        $menu = Menu::factory()->primary()->create();

        $menu->items()->create([
            'type' => MenuItemType::Custom,
            'label' => 'Old Item',
            'custom_url' => '/old',
            'position' => 0,
        ]);

        $this->mock(ActivityLogger::class, function ($mock): void {
            $mock->shouldReceive('record')->once()->andThrow(new \RuntimeException('Audit unavailable'));
        });

        try {
            app(UpdateMenuStructure::class)->handle(
                $menu,
                [
                    [
                        'label' => 'New Item',
                        'type' => MenuItemType::Custom->value,
                        'custom_url' => '/new',
                    ],
                ],
                $admin,
            );

            $this->fail('Expected audit failure to abort the transaction.');
        } catch (\RuntimeException) {
            //
        }

        $this->assertDatabaseHas('menu_items', ['label' => 'Old Item']);
        $this->assertDatabaseMissing('menu_items', ['label' => 'New Item']);
    }

    public function test_create_user_rolls_back_when_audit_logging_fails(): void
    {
        $actor = User::factory()->administrator()->create();

        $this->mock(ActivityLogger::class, function ($mock): void {
            $mock->shouldReceive('record')->once()->andThrow(new \RuntimeException('Audit unavailable'));
        });

        try {
            app(CreateUser::class)->handle(
                name: 'Rollback User',
                email: 'rollback@contentflow.test',
                password: 'password-1',
                status: UserStatus::Active,
                actor: $actor,
            );

            $this->fail('Expected audit failure to abort the transaction.');
        } catch (\RuntimeException) {
            //
        }

        $this->assertDatabaseMissing('users', ['email' => 'rollback@contentflow.test']);
    }
}
