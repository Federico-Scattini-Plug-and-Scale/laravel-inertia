<?php

use App\Http\Controllers\Company\Admin\CompanyController;
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
        Route::get('/dashboard', [CompanyController::class, 'index'])
            ->name('dashboard');
        
        //Profile
        Route::get('/' . trans('routes.profile') . '/{user}', [CompanyController::class, 'show'])
            ->name('profile');
        Route::post('/' . trans('routes.profile') . '/{user}/' . trans('routes.edit'), [CompanyController::class, 'edit'])
            ->name('profile.edit');
        
        //Auth data
        Route::get('/' . trans('routes.auth-data') . '/{user}', [AuthDataController::class, 'index'])
            ->name('authdata');
        Route::post('/' . trans('routes.auth-data') . '/{user}/' . trans('routes.edit-password'), [AuthDataController::class, 'changePassword'])
            ->name('authdata.password.edit');
        Route::post('/' . trans('routes.auth-data') . '/{user}/' . trans('routes.edit-email'), [AuthDataController::class, 'changeEmail'])
            ->name('authdata.email.edit');

        //Invoice data
        Route::get('/' . trans('routes.invoice-data') . '/{user}', [CompanyController::class, 'invoiceData'])
            ->name('invoicedata');
        Route::post('/' . trans('routes.invoice-data') . '/{user}/' . trans('routes.edit'), [CompanyController::class, 'editInvoiceData'])
            ->name('invoicedata.edit');
        
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
