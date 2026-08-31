<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Support\Demo\DemoGuard;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function create(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'email' => $request->string('email')->toString(),
            'token' => $token,
        ]);
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $email = $request->string('email')->lower()->toString();

        try {
            DemoGuard::assertCanResetPassword($email);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors($exception->errors());
        }

        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user || $user->status !== UserStatus::Active) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => [__('passwords.token')]]);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
