<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::localized(function ()
{
    Route::prefix(trans('routes.applicant') . '/{user}')->middleware(['auth.applicant', 'role:applicant', 'verified:applicant'])->name('applicant.')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Applicant/Dashboard');
        })->name('dashboard');
    });
});

require __DIR__ . '/auth.php';
