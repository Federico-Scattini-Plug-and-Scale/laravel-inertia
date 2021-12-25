<?php

use App\Http\Controllers\Company\Admin\CompanyController;
use App\Http\Controllers\Company\Admin\JobOfferController;
use App\Http\Controllers\Company\Auth\AuthDataController;
use App\Http\Controllers\Company\PaymentController;
use Illuminate\Http\Request;
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
    Route::prefix(trans('routes.company'))->middleware(['auth.company', 'role:company', 'verified:company'])->name('company.')->group(function () {
        Route::get('{user}/dashboard', [CompanyController::class, 'index'])
            ->name('dashboard');
        
        //Profile
        Route::get('/{user}/' . trans('routes.profile'), [CompanyController::class, 'show'])
            ->name('profile');
        Route::post('/{user}/' . trans('routes.profile') . '/' . trans('routes.edit'), [CompanyController::class, 'edit'])
            ->name('profile.edit');
        
        //Auth data
        Route::get('/{user}/' . trans('routes.auth-data'), [AuthDataController::class, 'index'])
            ->name('authdata');
        Route::post('/{user}/' . trans('routes.auth-data') . '/' . trans('routes.edit-password'), [AuthDataController::class, 'changePassword'])
            ->name('authdata.password.edit');
        Route::post('/{user}' . trans('routes.auth-data') . '/' . trans('routes.edit-email'), [AuthDataController::class, 'changeEmail'])
            ->name('authdata.email.edit');

        //Invoice data
        Route::get('/{user}/' . trans('routes.invoice-data'), [CompanyController::class, 'invoiceData'])
            ->name('invoicedata');
        Route::post('/{user}/' . trans('routes.invoice-data') . '/' . trans('routes.edit'), [CompanyController::class, 'editInvoiceData'])
            ->name('invoicedata.edit');

        //Job offers managament
        Route::get('/{user}/'. trans('routes.job-offers') . '/' . trans('routes.create'), [JobOfferController::class, 'create'])
            ->name('joboffers.create');
        Route::post('/{user}/'. trans('routes.job-offers') . '/' . trans('routes.store'), [JobOfferController::class, 'store'])
            ->name('joboffers.store');
        
        Route::get('/' . trans('routes.pricing'), [CompanyController::class, 'pricing'])
            ->name('pricing');
        Route::get('/payment', [PaymentController::class, 'payment'])
            ->name('payment');
        Route::get('/success', [PaymentController::class, 'success'])
            ->name('success');
        Route::get('/cancel', function(){
            return 'cancel';
        })->name('cancel');
    });
});

require __DIR__ . '/auth.php';
