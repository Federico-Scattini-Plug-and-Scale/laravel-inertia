<?php

use App\Http\Controllers\Company\Admin\CompanyController;
use App\Http\Controllers\Company\Admin\JobOfferController;
use App\Http\Controllers\Company\Auth\AuthDataController;
use App\Http\Controllers\Company\PaymentController;
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
    Route::prefix(trans('routes.company') . '/{user}')->middleware(['auth.company', 'role:company', 'verified:company'])->name('company.')->group(function () {
        Route::get('dashboard', [CompanyController::class, 'index'])
            ->name('dashboard');
        
        //Profile
        Route::prefix(trans('routes.profile'))->name('profile.')->group(function ()
        {
            Route::get('/', [CompanyController::class, 'show'])
                ->name('show');
            Route::post(trans('routes.edit'), [CompanyController::class, 'edit'])
                ->name('edit'); 
        });
        
        //Auth data
        Route::prefix(trans('routes.auth-data'))->name('authdata.')->group(function () 
        {
            Route::get('/', [AuthDataController::class, 'index'])
                ->name('index');
            Route::post(trans('routes.edit-password'), [AuthDataController::class, 'changePassword'])
                ->name('password.edit');
            Route::post(trans('routes.edit-email'), [AuthDataController::class, 'changeEmail'])
                ->name('email.edit');
        });

        //Invoice data
        Route::prefix(trans('routes.invoice-data'))->name('invoicedata.')->group(function () 
        {
            Route::get('/', [CompanyController::class, 'invoiceData'])
                ->name('index');
            Route::post(trans('routes.edit'), [CompanyController::class, 'editInvoiceData'])
                ->name('edit');
        });

        //Job offers managament
        Route::prefix(trans('routes.job-offers'))->name('joboffers.')->group(function () 
        {
            Route::get('/', [JobOfferController::class, 'index'])
                ->name('index');
            Route::get(trans('routes.create'), [JobOfferController::class, 'create'])
                ->name('create');
            Route::post(trans('routes.store'), [JobOfferController::class, 'store'])
                ->name('store');
            Route::get('/{jobOffer}/' . trans('routes.edit'), [JobOfferController::class, 'edit'])
                ->name('edit');
            Route::post('/{jobOffer}/' . trans('routes.update'), [JobOfferController::class, 'update'])
                ->name('update');
            Route::post('/{jobOffer}/' . trans('routes.delete'), [JobOfferController::class, 'destroy'])
                ->name('destroy');
        });

        //Payment
        Route::prefix('/{jobOffer}/' . trans('routes.payment'))->name('payment.')->group(function () 
        {
            Route::get(trans('routes.packages'), [PaymentController::class, 'packages'])
                ->name('packages');
            Route::post(trans('routes.packages'), [PaymentController::class, 'storePackage'])
                ->name('packages.store');
            Route::get(trans('routes.upgrade'), [PaymentController::class, 'upgrade'])
                ->name('upgrade');
            Route::post(trans('routes.upgrade'), [PaymentController::class, 'storeUpgrade'])
                ->name('upgrade.store');
            Route::get(trans('routes.preview'), [PaymentController::class, 'preview'])
                ->name('preview');
            Route::post(trans('routes.invoice-data'), [PaymentController::class, 'invoiceData'])
                ->name('invoicedata');
            Route::get('/', [PaymentController::class, 'payment'])
                ->name('index');
            Route::get(trans('routes.success'), [PaymentController::class, 'success'])
                ->name('success');
            Route::get(trans('routes.cancel'), [PaymentController::class, 'cancel'])
                ->name('cancel');
        });
    });
});

require __DIR__ . '/auth.php';
