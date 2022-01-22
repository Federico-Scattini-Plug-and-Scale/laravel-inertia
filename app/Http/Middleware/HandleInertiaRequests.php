<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'locale' => app()->getLocale(),
            'locales' => config('app.available_locales'),
            'countries' => config('countries.availables'),
            'country' => getCountry(),
            'countryName' => getCountryName(),
            'lang' => fn () => translations(resource_path('lang/' . app()->getLocale() .'/' . app()->getLocale() . '.json')),
            'route' => fn () => Route::currentRouteName(),
            'flash' => [
                'message' => fn () => session()->get('message')
            ]
        ]);
    }
}
