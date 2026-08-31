<?php

namespace App\Http\Middleware;

use App\Support\Install\InstallLock;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstallerUnlocked
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (InstallLock::isLocked()) {
            return response('', Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
