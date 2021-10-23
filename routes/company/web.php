<?php

use App\Http\Controllers\Company\Admin\CompanyController;
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

Route::prefix('company')->middleware(['auth.company', 'role:company'])->name('company.')->group(function () {
    Route::get('/dashboard', [CompanyController::class, 'index'])
        ->name('dashboard');
    
    Route::get('/profile/{user}', [CompanyController::class, 'show'])
        ->name('profile');
});

require __DIR__ . '/auth.php';
