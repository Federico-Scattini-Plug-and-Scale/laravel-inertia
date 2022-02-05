<?php

use App\Http\Controllers\Company\Admin\CompanyController;
use App\Http\Controllers\Company\Admin\InvoiceController;
use App\Http\Controllers\Company\Admin\JobOfferController;
use App\Http\Controllers\Company\Auth\AuthDataController;
use App\Http\Controllers\Company\PaymentController;
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
    Route::prefix(Lang::uri('company/{user}'))->middleware(['auth.company', 'role:company', 'verified:company'])->name('company.')->group(function () {
        Route::get('dashboard', [CompanyController::class, 'index'])
            ->name('dashboard');
        
        //Profile
        Route::prefix(Lang::uri('profile'))->name('profile.')->group(function ()
        {
            Route::get('/', [CompanyController::class, 'show'])
                ->name('show');
            Route::post(Lang::uri('edit'), [CompanyController::class, 'edit'])
                ->name('edit'); 
        });
        
        //Auth data
        Route::prefix(Lang::uri('auth-data'))->name('authdata.')->group(function () 
        {
            Route::get('/', [AuthDataController::class, 'index'])
                ->name('index');
            Route::post(Lang::uri('edit-password'), [AuthDataController::class, 'changePassword'])
                ->name('password.edit');
            Route::post(Lang::uri('edit-email'), [AuthDataController::class, 'changeEmail'])
                ->name('email.edit');
        });

        //Invoice data
        Route::prefix(Lang::uri('invoice-data'))->name('invoicedata.')->group(function () 
        {
            Route::get('/', [CompanyController::class, 'invoiceData'])
                ->name('index');
            Route::post(Lang::uri('edit'), [CompanyController::class, 'editInvoiceData'])
                ->name('edit');
        });

        //Job offers managament
        Route::prefix(Lang::uri('job-offers'))->name('joboffers.')->group(function () 
        {
            Route::get('/', [JobOfferController::class, 'index'])
                ->name('index');
            Route::get(Lang::uri('create'), [JobOfferController::class, 'create'])
                ->name('create');
            Route::post(Lang::uri('store'), [JobOfferController::class, 'store'])
                ->name('store');
            Route::get(Lang::uri('/{jobOffer}/edit'), [JobOfferController::class, 'edit'])
                ->name('edit');
            Route::post(Lang::uri('/{jobOffer}/update'), [JobOfferController::class, 'update'])
                ->name('update');
            Route::post(Lang::uri('/{jobOffer}/delete'), [JobOfferController::class, 'destroy'])
                ->name('destroy');
        });

        //Payment
        Route::prefix(Lang::uri('/{jobOffer}/payment'))->name('payment.')->group(function () 
        {
            Route::get(Lang::uri('packages'), [PaymentController::class, 'packages'])
                ->name('packages');
            Route::post(Lang::uri('packages'), [PaymentController::class, 'storePackage'])
                ->name('packages.store');
            Route::get(Lang::uri('upgrade'), [PaymentController::class, 'upgrade'])
                ->name('upgrade');
            Route::post(Lang::uri('upgrade'), [PaymentController::class, 'storeUpgrade'])
                ->name('upgrade.store');
            Route::get(Lang::uri('preview'), [PaymentController::class, 'preview'])
                ->name('preview');
            Route::post(Lang::uri('invoice-data'), [PaymentController::class, 'invoiceData'])
                ->name('invoicedata');
            Route::get('/', [PaymentController::class, 'payment'])
                ->name('index');
            Route::get(Lang::uri('success'), [PaymentController::class, 'success'])
                ->name('success');
            Route::get(Lang::uri('cancel'), [PaymentController::class, 'cancel'])
                ->name('cancel');
        });

        //Invoices
        Route::prefix(Lang::uri('invoices'))->name('invoices.')->group(function ()
        {
            Route::get('/', [InvoiceController::class, 'index'])
                ->name('index');
            Route::get(Lang::uri('/{invoice}/download'), [InvoiceController::class, 'download'])
                ->name('download');
            Route::get(Lang::uri('/{invoice}/preview'), [InvoiceController::class, 'preview'])
                ->name('preview');
        });
    });
});

require __DIR__ . '/auth.php';
