<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;

Route::localized(function () {
    Route::prefix(Lang::uri('admin'))->name('admin.')->group(function () {
        Route::get(Lang::uri('register'), [RegisteredUserController::class, 'create'])
            ->middleware('guest')
            ->name('register');

        Route::post(Lang::uri('register'), [RegisteredUserController::class, 'store'])
            ->middleware('guest')
            ->name('register.store');

        Route::get(Lang::uri('login'), [AuthenticatedSessionController::class, 'create'])
            ->middleware('guest')
            ->name('login');

        Route::post(Lang::uri('login'), [AuthenticatedSessionController::class, 'store'])
            ->middleware('guest');

        Route::get(Lang::uri('forgot-password'), [PasswordResetLinkController::class, 'create'])
            ->middleware('guest')
            ->name('password.request');

        Route::post(Lang::uri('forgot-password'), [PasswordResetLinkController::class, 'store'])
            ->middleware('guest')
            ->name('password.email');

        Route::get(Lang::uri('reset-password') . '/{token}', [NewPasswordController::class, 'create'])
            ->middleware('guest')
            ->name('password.reset');

        Route::post(Lang::uri('reset-password'), [NewPasswordController::class, 'store'])
            ->middleware('guest')
            ->name('password.update');

        Route::get(Lang::uri('verify-email'), [EmailVerificationPromptController::class, '__invoke'])
            ->middleware('auth')
            ->name('verification.notice');

        Route::get(Lang::uri('verify-email/{id}/{hash}'), [VerifyEmailController::class, '__invoke'])
            ->middleware(['auth', 'signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post(Lang::uri('email/verification-notification'), [EmailVerificationNotificationController::class, 'store'])
            ->middleware(['auth', 'throttle:6,1'])
            ->name('verification.send');

        Route::get(Lang::uri('confirm-password'), [ConfirmablePasswordController::class, 'show'])
            ->middleware('auth')
            ->name('password.confirm');

        Route::post(Lang::uri('confirm-password'), [ConfirmablePasswordController::class, 'store'])
            ->middleware('auth');

        Route::post(Lang::uri('logout'), [AuthenticatedSessionController::class, 'destroy'])
            ->middleware('auth')
            ->name('logout');
    });
});
