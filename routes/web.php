<?php

use App\Http\Controllers\Front\JobOffersController;
use App\Http\Controllers\LangController;
use Illuminate\Support\Facades\Route;

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

Route::get('/lang', LangController::class)->name('lang');

Route::localized(function () {
    Route::name('joboffers.')->group(function ()
    {
        Route::get('/{locations?}', [JobOffersController::class, 'index'])->name('listing');
    });
});
