<?php

use App\Http\Controllers\Applicant\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Applicant\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Applicant\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Applicant\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Applicant\Auth\NewPasswordController;
use App\Http\Controllers\Applicant\Auth\PasswordResetLinkController;
use App\Http\Controllers\Applicant\Auth\RegisteredUserController;
use App\Http\Controllers\Applicant\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::localized(function () {
    Route::prefix(trans('routes.applicant'))->name('applicant.')->group(function () {
        Route::get(trans('routes.register'), [RegisteredUserController::class, 'create'])
            ->middleware('guest')
            ->name('register');

        Route::post(trans('routes.register'), [RegisteredUserController::class, 'store'])
            ->middleware('guest');

        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->middleware('guest')
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store'])
            ->middleware('guest');

        Route::get(trans('routes.forgot-password'), [PasswordResetLinkController::class, 'create'])
            ->middleware('guest')
            ->name('password.request');

        Route::post(trans('routes.forgot-password'), [PasswordResetLinkController::class, 'store'])
            ->middleware('guest')
            ->name('password.email');

        Route::get(trans('routes.reset-password') . '/{token}', [NewPasswordController::class, 'create'])
            ->middleware('guest')
            ->name('password.reset');

        Route::post(trans('routes.reset-password'), [NewPasswordController::class, 'store'])
            ->middleware('guest')
            ->name('password.update');

        Route::get(trans('routes.verify-email'), [EmailVerificationPromptController::class, '__invoke'])
            ->middleware('auth.applicant')
            ->name('verification.notice');

        Route::get(trans('routes.verify-email') . '/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware(['auth.applicant', 'signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post(trans('routes.email') .'/' . trans('routes.verification-notification'), [EmailVerificationNotificationController::class, 'store'])
            ->middleware(['auth.applicant', 'throttle:6,1'])
            ->name('verification.send');

        Route::get(trans('routes.confirm-password'), [ConfirmablePasswordController::class, 'show'])
            ->middleware('auth.applicant')
            ->name('password.confirm');

        Route::post(trans('routes.confirm-password'), [ConfirmablePasswordController::class, 'store'])
            ->middleware('auth.applicant');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->middleware('auth.applicant')
            ->name('logout');
        });
});