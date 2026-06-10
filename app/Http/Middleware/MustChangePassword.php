<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MustChangePassword
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->must_change_password) {
            // If no token exists, generate one
            if (!$user->remember_token) {
                $user->remember_token = \Illuminate\Support\Str::random(60);
                $user->save();
            }
            return redirect()->route('password.setup.form', ['token' => $user->remember_token]);
        }

        return $next($request);
    }
}
