<?php

namespace App\Http\Middleware\Applicant;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateApplicant extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('applicant.login');
        }
    }
}
