<?php

use App\Http\Controllers\Front\JobOffersController;
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

Route::localized(function () {
    Route::name('joboffers.')->group(function ()
    {
        Route::get('/{categorySlug}/{slug},{jobOffer}', [JobOffersController::class, 'show'])->name('show');
        Route::get('/{category?}/{tech?}/{location?}', [JobOffersController::class, 'index'])->name('listing');
    });
});
