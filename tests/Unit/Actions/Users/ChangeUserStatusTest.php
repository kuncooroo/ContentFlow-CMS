<?php

namespace Tests\Unit\Actions\Users;

use App\Actions\Users\ChangeUserStatus;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ChangeUserStatusTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_be_deactivated_by_another_user(): void
    {
        $actor = User::factory()->create();
        $target = User::factory()->create();

        $updated = app(ChangeUserStatus::class)->handle(
            $target,
            UserStatus::Inactive,
            $actor,
        );

        $this->assertSame(UserStatus::Inactive, $updated->status);
    }

    public function test_user_cannot_deactivate_their_own_account(): void
    {
        $user = User::factory()->create();

        $this->expectException(ValidationException::class);

        app(ChangeUserStatus::class)->handle(
            $user,
            UserStatus::Inactive,
            $user,
        );
    }
}
