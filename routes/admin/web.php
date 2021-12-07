<?php

use App\Http\Controllers\Admin\Admin\AdminController;
use App\Http\Controllers\Admin\Admin\CategoryController;
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
        Route::get('/dashboard', [AdminController::class, 'index'])
            ->name('dashboard');
        Route::get('/' . trans('routes.profile') . '/{user}', [AdminController::class, 'show'])
            ->name('profile');
        Route::post('/' . trans('routes.profile') . '/{user}/' . trans('routes.modify'), [AdminController::class, 'edit'])
            ->name('profile.edit');
    
        //tags
        Route::prefix('/tags')->group(function () {
            Route::get('/', [TagController::class, 'index'])
                ->name('tags');
            Route::post('/', [TagController::class, 'save'])
                ->name('tags.save');
            Route::get('/{taggroup}/' . trans('routes.modify'), [TagController::class, 'edit'])
                ->name('tags.edit');
            Route::post('/{taggroup}/' . trans('routes.save'), [TagController::class, 'update'])
                ->name('tags.update');
            Route::post('/{taggroup}/' . trans('routes.delete'), [TagController::class, 'destroy'])
                ->name('tags.destroy');
            Route::post('/{taggroup}/'. trans('routes.delete') .'/{tag}', [TagController::class, 'destroyTag'])
                ->name('tags.destroy.tag');
        });
        
        //Job offer types
        Route::prefix('/' . trans('routes.job-offer-types'))->group(function () {
            Route::get('/', [JobOfferTypesController::class, 'index'])
                ->name('joboffertypes');
            Route::get('/' . trans('routes.create'), [JobOfferTypesController::class, 'create'])
                ->name('joboffertypes.create');
            Route::post('/', [JobOfferTypesController::class, 'store'])
                ->name('joboffertypes.store');
            Route::get('/{joboffertype}/' . trans('routes.modify'), [JobOfferTypesController::class, 'edit'])
                ->name('joboffertypes.edit');
            Route::post('/{joboffertype}/' . trans('routes.modify'), [JobOfferTypesController::class, 'update'])
                ->name('joboffertypes.update');
            Route::post('/{joboffertype}/' . trans('routes.delete'), [JobOfferTypesController::class, 'destroy'])
                ->name('joboffertypes.destroy');
        });

        //Categories
        Route::prefix('/' . trans('routes.categories'))->group(function () {
            Route::get('/', [CategoryController::class, 'index'])
                ->name('categories');
            Route::post('/', [CategoryController::class, 'save'])
                ->name('categories.save');
            Route::get('/{category}/' . trans('routes.modify'), [CategoryController::class, 'edit'])
                ->name('categories.edit');
            Route::post('/{category}/' . trans('routes.modify'), [CategoryController::class, 'update'])
                ->name('categories.update');
            Route::post('/{category}/' . trans('routes.delete'), [CategoryController::class, 'destroy'])
                ->name('categories.destroy');
        });
    });
});

require __DIR__ . '/auth.php';
