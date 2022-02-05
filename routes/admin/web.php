<?php

use App\Http\Controllers\Admin\Admin\AdminController;
use App\Http\Controllers\Admin\Admin\CategoryController;
use App\Http\Controllers\Admin\Admin\JobOffersController;
use App\Http\Controllers\Admin\Admin\JobOfferTypesController;
use App\Http\Controllers\Admin\Admin\TagController;
use Illuminate\Support\Facades\Lang;
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
    Route::prefix(Lang::uri('admin'))->middleware(['auth.admin', 'role:admin'])->name('admin.')->group(function () {
        Route::get(Lang::uri('dashboard'), [AdminController::class, 'index'])
            ->name('dashboard');
        Route::get(Lang::uri('profile/{user}'), [AdminController::class, 'show'])
            ->name('profile');
        Route::post(Lang::uri('profile/{user}/edit'), [AdminController::class, 'edit'])
            ->name('profile.edit');
    
        //Tags
        Route::prefix(Lang::uri('tags'))->name('tags.')->group(function () {
            Route::get('/', [TagController::class, 'index'])
                ->name('index');
            Route::post('/', [TagController::class, 'save'])
                ->name('save');
            Route::get(Lang::uri('{taggroup}/edit'), [TagController::class, 'edit'])
                ->name('edit');
            Route::post(Lang::uri('{taggroup}/save'), [TagController::class, 'update'])
                ->name('update');
            Route::post(Lang::uri('{taggroup}/delete'), [TagController::class, 'destroy'])
                ->name('destroy');
            Route::post(Lang::uri('{taggroup}/delete/{tag}'), [TagController::class, 'destroyTag'])
                ->name('destroy.tag');
        });
        
        //Job offer types
        Route::prefix(Lang::uri('job-offer-types'))->name('joboffertypes.')->group(function () {
            Route::get('/', [JobOfferTypesController::class, 'index'])
                ->name('index');
            Route::get('/' . trans('routes.create'), [JobOfferTypesController::class, 'create'])
                ->name('create');
            Route::post('/', [JobOfferTypesController::class, 'store'])
                ->name('store');
            Route::get(Lang::uri('{joboffertype}/edit'), [JobOfferTypesController::class, 'edit'])
                ->name('edit');
            Route::post(Lang::uri('{joboffertype}/edit'), [JobOfferTypesController::class, 'update'])
                ->name('update');
            Route::post(Lang::uri('{joboffertype}/delete'), [JobOfferTypesController::class, 'destroy'])
                ->name('destroy');
        });

        //Categories
        Route::prefix(Lang::uri('categories'))->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])
                ->name('index');
            Route::post('/', [CategoryController::class, 'save'])
                ->name('save');
            Route::get(Lang::uri('{category}/edit'), [CategoryController::class, 'edit'])
                ->name('edit');
            Route::post(Lang::uri('{category}/edit'), [CategoryController::class, 'update'])
                ->name('update');
            Route::post(Lang::uri('{category}/delete'), [CategoryController::class, 'destroy'])
                ->name('destroy');
        });

        //Job offers
        Route::prefix(Lang::uri('job-offers'))->name('joboffers.')->group(function () {
            Route::get('/', [JobOffersController::class, 'index'])
                ->name('index');
            Route::post(Lang::uri('{jobOffer}/approve'), [JobOffersController::class, 'approve'])
                ->name('approve');
            Route::post(Lang::uri('{jobOffer}/restore'), [JobOffersController::class, 'restore'])
                ->name('restore');
            Route::post(Lang::uri('{jobOffer}/archive'), [JobOffersController::class, 'archive'])
                ->name('archive');
            Route::get(Lang::uri('{jobOffer}/edit'), [JobOffersController::class, 'edit'])
                ->name('edit');
            Route::post(Lang::uri('{jobOffer}/edit'), [JobOffersController::class, 'update'])
                ->name('update');
            Route::post(Lang::uri('{jobOffer}/destroy'), [JobOffersController::class, 'destroy'])
                ->name('destroy');
        });
    });
});

require __DIR__ . '/auth.php';
