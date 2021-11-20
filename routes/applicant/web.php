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

Route::get('/applicant/dashboard', function () {
    return Inertia::render('Applicant/Dashboard');
})->middleware(['auth.applicant', 'role:applicant'])
    ->name('applicant.dashboard');

require __DIR__ . '/auth.php';
