<?php

use App\Http\Controllers\LangController;
use App\Models\JobOffer;
use Illuminate\Support\Facades\App;
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

Route::get('/', function () {
    $offers = JobOffer::take(1)->get()->load('company', 'tags:id');
    return Inertia::render('Front/JobOffers/Listing', [
        'offers' => $offers,
    ]);
})->name('home');

Route::get('/locale', function () {
    return App::getLocale();
});

Route::get('/lang', LangController::class)->name('lang');