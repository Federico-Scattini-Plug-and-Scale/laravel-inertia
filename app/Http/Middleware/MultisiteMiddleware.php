<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MultisiteMiddleware
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
        $sites = collect(config('sites.sites'));

        $defaultSite = $sites->firstWhere('default', true);

        $currentSite = $sites->get($request->subdomain(), $defaultSite);

        App::setLocale($currentSite['locale']);

        return $next($request);
    }
}
