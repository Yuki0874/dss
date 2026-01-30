<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->route('user.login')->with('error', 'Please login to access this page.');
        }

        if (!Auth::guard('web')->user()->is_verified) {
            Auth::guard('web')->logout();
            return redirect()->route('user.login')->with('error', 'Please verify your email first.');
        }

        return $next($request);
    }
}