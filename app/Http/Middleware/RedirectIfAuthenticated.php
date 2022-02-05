<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        if (Auth::guard($guard)->check()) {
            $role = Auth::user()->role; 
            
            switch ($role) {
                case User::ADMIN:
                    return redirect()->route('admin.dashboard');
                    break;
                case User::COMPANY:
                    return redirect()->route('company.dashboard', Auth::user());
                    break; 
                case User::APPLICANT:
                    return redirect()->route('applicant.dashboard');
                    break; 
            
                default:
                    return redirect('/'); 
                    break;
            }
        }

        return $next($request);
    }
}
