<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Role
{

    public function handle($request, Closure $next, String $role)
    {
        $user = Auth::user();
        if($user->role == $role)
            return $next($request);

        return redirect()->route('home');
    }
}
