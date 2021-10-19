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

Route::get('/company/dashboard', function () {
    return Inertia::render('Company/Dashboard');
})->middleware(['auth.company', 'role:company'])
    ->name('company.dashboard');

require __DIR__ . '/auth.php';
