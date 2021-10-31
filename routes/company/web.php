<?php

use App\Http\Controllers\Company\Admin\CompanyController;
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

Route::prefix('company')->middleware(['auth.company', 'role:company'])->name('company.')->group(function () {
    Route::get('/dashboard', [CompanyController::class, 'index'])
        ->name('dashboard');
    
    Route::get('/profile/{user}', [CompanyController::class, 'show'])
        ->name('profile');
    Route::post('/profile/{user}/edit', [CompanyController::class, 'edit'])
        ->name('profile.edit');

    Route::get('/pricing', [CompanyController::class, 'pricing'])
        ->name('pricing');
    Route::get('/payment', [CompanyController::class, 'payment'])
        ->name('payment');
    Route::get('/success', function(Request $request){
        \Stripe\Stripe::setApiKey(env('STRIPE_PRIVATE_API_KEY'));

        $test=\Stripe\Checkout\Session::retrieve($request->get('session_id'));
        return dd($test);
    })
        ->name('success');
    Route::get('/cancel', function(){
        return 'cancel';
    })
        ->name('cancel');
});

require __DIR__ . '/auth.php';
