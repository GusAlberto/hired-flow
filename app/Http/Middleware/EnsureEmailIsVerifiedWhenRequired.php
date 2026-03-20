<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedWhenRequired
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('auth.require_verified_email')) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user || ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
