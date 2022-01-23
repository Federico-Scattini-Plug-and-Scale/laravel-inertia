<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('changeLocale'))
        {
            session()->put('country', $request->country);
            
            return redirect(route($request->route()->getName(), $request->route()->parameters(), true, $request->locale));
        }

        return $next($request);
    }
}
