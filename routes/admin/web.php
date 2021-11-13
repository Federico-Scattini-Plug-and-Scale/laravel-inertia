<?php

use App\Http\Controllers\Admin\Admin\AdminController;
use App\Http\Controllers\Admin\Admin\TagController;
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

Route::prefix('admin')->middleware(['auth.admin', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])
        ->name('dashboard');
    Route::get('/profile/{user}', [AdminController::class, 'show'])
        ->name('profile');
    Route::post('/profile/{user}/edit', [AdminController::class, 'edit'])
        ->name('profile.edit');

    //tags
    Route::get('/tags', [TagController::class, 'index'])
        ->name('tags');
    Route::post('/tags', [TagController::class, 'save'])
        ->name('tags.save');
    Route::get('/tags/{taggroup}/edit', [TagController::class, 'edit'])
        ->name('tags.edit');
    Route::post('/tags/{taggroup}/destroy', [TagController::class, 'destroy'])
        ->name('tags.destroy');
});

require __DIR__ . '/auth.php';
