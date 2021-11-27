<?php

use App\Http\Controllers\Admin\Admin\AdminController;
use App\Http\Controllers\Admin\Admin\JobOfferTypesController;
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
    Route::get('/profilo/{user}', [AdminController::class, 'show'])
        ->name('profile');
    Route::post('/profilo/{user}/modifica', [AdminController::class, 'edit'])
        ->name('profile.edit');

    //tags
    Route::get('/tags', [TagController::class, 'index'])
        ->name('tags');
    Route::post('/tags', [TagController::class, 'save'])
        ->name('tags.save');
    Route::get('/tags/{taggroup}/modifica', [TagController::class, 'edit'])
        ->name('tags.edit');
    Route::post('/tags/{taggroup}/salva', [TagController::class, 'update'])
        ->name('tags.update');
    Route::post('/tags/{taggroup}/elimina', [TagController::class, 'destroy'])
        ->name('tags.destroy');
    Route::post('/tags/{taggroup}/elimina/{tag}', [TagController::class, 'destroyTag'])
        ->name('tags.destroy.tag');

    //Job offer types
    Route::get('/tipi-di-offerte', [JobOfferTypesController::class, 'index'])
        ->name('joboffertypes');
    Route::get('/tipi-di-offerte/crea', [JobOfferTypesController::class, 'create'])
        ->name('joboffertypes.create');
    Route::post('/tipi-di-offerte', [JobOfferTypesController::class, 'store'])
        ->name('joboffertypes.store');
});

require __DIR__ . '/auth.php';
