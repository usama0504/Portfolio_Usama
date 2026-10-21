<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'show'])->name('home');
Route::post('/track', [TrackController::class, 'store'])->middleware('throttle:track')->name('track');
Route::get('/robots.txt', [SeoController::class, 'robots']);
Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.store');

// Dashboard: edit the portfolio content from the browser.
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])
            ->middleware('throttle:admin-login')
            ->name('login.store');

        // Forgot password: emailed link -> choose a new password
        Route::get('forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [PasswordResetController::class, 'store'])
            ->middleware('throttle:admin-forgot')
            ->name('password.email');
    });

    // The emailed link is protected by its own one-time token, so it works whether or not you are signed in.
    Route::get('reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])
        ->middleware('throttle:admin-forgot')
        ->name('password.update');

    Route::middleware('auth')->group(function () {
        Route::get('/', [ContentController::class, 'edit'])->name('dashboard');
        Route::put('content/{section}', [ContentController::class, 'update'])->name('content.update');
        Route::delete('content/{section}', [ContentController::class, 'destroy'])->name('content.destroy');
        Route::post('upload', [UploadController::class, 'store'])->name('upload');

        // Visitor statistics
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');

        // Contact-form inbox
        Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/export', [MessageController::class, 'export'])->name('messages.export');
        Route::post('messages/read-all', [MessageController::class, 'readAll'])->name('messages.read-all');
        Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
        Route::patch('messages/{message}/unread', [MessageController::class, 'markUnread'])->name('messages.unread');
        Route::patch('messages/{message}/archive', [MessageController::class, 'archive'])->name('messages.archive');
        Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        // Your account: details + password
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('password', [ProfileController::class, 'password'])->name('profile.password');

        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
    });
});
