<?php

namespace Tests\Feature\UiFeedback;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthValidationFeedbackTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_validation_errors_use_field_level_feedback(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => '',
            'password' => '',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);
    }
}
