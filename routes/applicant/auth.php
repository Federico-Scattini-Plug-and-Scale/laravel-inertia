<?php

use App\Http\Controllers\Applicant\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Applicant\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Applicant\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Applicant\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Applicant\Auth\NewPasswordController;
use App\Http\Controllers\Applicant\Auth\PasswordResetLinkController;
use App\Http\Controllers\Applicant\Auth\RegisteredUserController;
use App\Http\Controllers\Applicant\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;

Route::localized(function () {
    Route::prefix(Lang::uri('applicant'))->name('applicant.')->group(function () {
        Route::get(Lang::uri('register'), [RegisteredUserController::class, 'create'])
            ->middleware('guest')
            ->name('register');

        Route::post(Lang::uri('register'), [RegisteredUserController::class, 'store'])
            ->middleware('guest');

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

        Route::get(Lang::uri('reset-password/{token}'), [NewPasswordController::class, 'create'])
            ->middleware('guest')
            ->name('password.reset');

        Route::post(Lang::uri('reset-password'), [NewPasswordController::class, 'store'])
            ->middleware('guest')
            ->name('password.update');

        Route::get(Lang::uri('verify-email'), [EmailVerificationPromptController::class, '__invoke'])
            ->middleware('auth.applicant')
            ->name('verification.notice');

        Route::get(Lang::uri('verify-email/{id}/{hash}'), [VerifyEmailController::class, '__invoke'])
            ->middleware(['auth.applicant', 'signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post(Lang::uri('email/verification-notification'), [EmailVerificationNotificationController::class, 'store'])
            ->middleware(['auth.applicant', 'throttle:6,1'])
            ->name('verification.send');

        Route::get(Lang::uri('confirm-password'), [ConfirmablePasswordController::class, 'show'])
            ->middleware('auth.applicant')
            ->name('password.confirm');

        Route::post(Lang::uri('confirm-password'), [ConfirmablePasswordController::class, 'store'])
            ->middleware('auth.applicant');

        Route::post(Lang::uri('logout'), [AuthenticatedSessionController::class, 'destroy'])
            ->middleware('auth.applicant')
            ->name('logout');
        });
});