<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use App\Support\Demo\DemoGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $email = $request->string('email')->lower()->toString();

        try {
            DemoGuard::assertCanResetPassword($email);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors($exception->errors());
        }

        $user = User::query()->where('email', $email)->first();

        if ($user?->status === UserStatus::Active) {
            Password::sendResetLink(['email' => $email]);
        }

        return back()->with('status', __('passwords.sent'));
    }
}
