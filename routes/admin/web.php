<?php

use App\Http\Controllers\Admin\Admin\AdminController;
use App\Http\Controllers\Admin\Admin\CategoryController;
use App\Http\Controllers\Admin\Admin\JobOffersController;
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

Route::localized(function () {
    Route::prefix('admin')->middleware(['auth.admin', 'role:admin'])->name('admin.')->group(function () {
        Route::get('dashboard', [AdminController::class, 'index'])
            ->name('dashboard');
        Route::get('/' . trans('routes.profile') . '/{user}', [AdminController::class, 'show'])
            ->name('profile');
        Route::post('/' . trans('routes.profile') . '/{user}/' . trans('routes.edit'), [AdminController::class, 'edit'])
            ->name('profile.edit');
    
        //Tags
        Route::prefix('/tags')->name('tags.')->group(function () {
            Route::get('/', [TagController::class, 'index'])
                ->name('index');
            Route::post('/', [TagController::class, 'save'])
                ->name('save');
            Route::get('{taggroup}/' . trans('routes.edit'), [TagController::class, 'edit'])
                ->name('edit');
            Route::post('{taggroup}/' . trans('routes.save'), [TagController::class, 'update'])
                ->name('update');
            Route::post('{taggroup}/' . trans('routes.delete'), [TagController::class, 'destroy'])
                ->name('destroy');
            Route::post('{taggroup}/'. trans('routes.delete') .'/{tag}', [TagController::class, 'destroyTag'])
                ->name('destroy.tag');
        });
        
        //Job offer types
        Route::prefix('/' . trans('routes.job-offer-types'))->name('joboffertypes.')->group(function () {
            Route::get('/', [JobOfferTypesController::class, 'index'])
                ->name('index');
            Route::get('/' . trans('routes.create'), [JobOfferTypesController::class, 'create'])
                ->name('create');
            Route::post('/', [JobOfferTypesController::class, 'store'])
                ->name('store');
            Route::get('{joboffertype}/' . trans('routes.edit'), [JobOfferTypesController::class, 'edit'])
                ->name('edit');
            Route::post('{joboffertype}/' . trans('routes.edit'), [JobOfferTypesController::class, 'update'])
                ->name('update');
            Route::post('{joboffertype}/' . trans('routes.delete'), [JobOfferTypesController::class, 'destroy'])
                ->name('destroy');
        });

        //Categories
        Route::prefix('/' . trans('routes.categories'))->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])
                ->name('index');
            Route::post('/', [CategoryController::class, 'save'])
                ->name('save');
            Route::get('{category}/' . trans('routes.edit'), [CategoryController::class, 'edit'])
                ->name('edit');
            Route::post('{category}/' . trans('routes.edit'), [CategoryController::class, 'update'])
                ->name('update');
            Route::post('{category}/' . trans('routes.delete'), [CategoryController::class, 'destroy'])
                ->name('destroy');
        });

        //Job offers
        Route::prefix('/' . trans('routes.job-offers'))->name('joboffers.')->group(function () {
            Route::get('/', [JobOffersController::class, 'index'])
                ->name('index');
            Route::post('{jobOffer}/' . trans('routes.approve'), [JobOffersController::class, 'approve'])
                ->name('approve');
            Route::post('{jobOffer}/' . trans('routes.restore'), [JobOffersController::class, 'restore'])
                ->name('restore');
            Route::post('{jobOffer}/' . trans('routes.archive'), [JobOffersController::class, 'archive'])
                ->name('archive');
            Route::post('{jobOffer}/' . trans('routes.destroy'), [JobOffersController::class, 'destroy'])
                ->name('destroy');
        });
    });
});

require __DIR__ . '/auth.php';
