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

        if ($user && $user->must_change_password && $user->role_id != 1) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Please change your password before continuing.');
        }

        return $next($request);
    }
}
