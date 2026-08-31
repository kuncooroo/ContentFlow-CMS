<?php

namespace App\Http\Middleware;

use App\Support\Demo\DemoGuard;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotDemoRestricted
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (DemoGuard::isEnabled() && $request->routeIs('password.request', 'password.email', 'password.reset', 'password.update')) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Password reset is disabled in demo mode.',
                ]);
        }

        return $next($request);
    }
}
