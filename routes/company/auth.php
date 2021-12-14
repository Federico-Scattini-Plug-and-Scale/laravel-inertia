<?php

use App\Http\Controllers\Company\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Company\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Company\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Company\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Company\Auth\NewPasswordController;
use App\Http\Controllers\Company\Auth\PasswordResetLinkController;
use App\Http\Controllers\Company\Auth\RegisteredUserController;
use App\Http\Controllers\Company\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::localized(function () {
    Route::prefix(trans('routes.company'))->name('company.')->group(function () {
        Route::get('/' . trans('routes.register'), [RegisteredUserController::class, 'create'])
            ->middleware('guest')
            ->name('register');

        Route::post('/' . trans('routes.register'), [RegisteredUserController::class, 'store'])
            ->middleware('guest');

        Route::get('/login', [AuthenticatedSessionController::class, 'create'])
            ->middleware('guest')
            ->name('login');

        Route::post('/login', [AuthenticatedSessionController::class, 'store'])
            ->middleware('guest');

        Route::get('/' . trans('routes.forgot-password'), [PasswordResetLinkController::class, 'create'])
            ->middleware('guest')
            ->name('password.request');

        Route::post('/' . trans('routes.forgot-password'), [PasswordResetLinkController::class, 'store'])
            ->middleware('guest')
            ->name('password.email');

        Route::get('/' . trans('routes.reset-password') . '/{token}', [NewPasswordController::class, 'create'])
            ->middleware('guest')
            ->name('password.reset');

        Route::post('/' . trans('routes.reset-password'), [NewPasswordController::class, 'store'])
            ->middleware('guest')
            ->name('password.update');

        Route::get('/' . trans('routes.verify-email'), [EmailVerificationPromptController::class, '__invoke'])
            ->middleware('auth.company')
            ->name('verification.notice');

        Route::get('/' . trans('routes.verify-email') . '/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware(['auth.company', 'signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('/' . trans('routes.email') .'/' . trans('routes.verification-notification'), [EmailVerificationNotificationController::class, 'store'])
            ->middleware(['auth.company', 'throttle:6,1'])
            ->name('verification.send');

        Route::get('/' . trans('routes.confirm-password'), [ConfirmablePasswordController::class, 'show'])
            ->middleware('auth.company')
            ->name('password.confirm');

        Route::post('/' . trans('routes.confirm-password'), [ConfirmablePasswordController::class, 'store'])
            ->middleware('auth.company');

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->middleware('auth.company')
            ->name('logout');
        });
});
