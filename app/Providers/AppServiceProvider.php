<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // helper for getting the current subdomain
        Request::macro('subdomain', function () {
            $domain = request()->server->get('HTTP_HOST');
            $split = explode('.', $domain, 3);
            
            // get the subdomain or return null
            return Arr::get($split, '0', '');
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
