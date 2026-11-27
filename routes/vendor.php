<?php

use App\Http\Controllers\Vendor\AccountController;
use App\Http\Controllers\Vendor\ApplicationController;
use App\Http\Controllers\Vendor\Auth\LoginController as VendorLoginController;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\StoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vendor portal routes (loaded from bootstrap/app.php, inside the "web" group)
|--------------------------------------------------------------------------
*/

Route::prefix('vendor')->name('vendor.')->group(function () {

    // Guests only
    Route::middleware('guest')->group(function () {
        Route::get('login', [VendorLoginController::class, 'create'])->name('login');
        Route::post('login', [VendorLoginController::class, 'store'])->name('login.store');

        Route::get('apply', [ApplicationController::class, 'create'])->name('apply');
        Route::post('apply', [ApplicationController::class, 'store'])->middleware('throttle:5,1')->name('apply.store');
    });

    Route::post('logout', [VendorLoginController::class, 'destroy'])->middleware('auth')->name('logout');

    // Signed in + approved account + vendor role + a store profile (all checked server-side).
    // Nothing below takes a vendor id from the browser: every query uses the signed-in vendor.
    Route::middleware(['auth', 'approved', 'role:vendor', 'vendor.has-store'])->group(function () {
        Route::redirect('/', '/vendor/dashboard');

        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // My Store
        Route::get('store', [StoreController::class, 'edit'])->name('store.edit');
        Route::put('store', [StoreController::class, 'update'])->name('store.update');
        Route::post('store/image', [StoreController::class, 'updateImage'])->middleware('throttle:20,1')->name('store.image');
        Route::delete('store/image', [StoreController::class, 'destroyImage'])->name('store.image.destroy');
        Route::put('store/payment', [StoreController::class, 'updatePayment'])->name('store.payment');
        Route::patch('store/availability', [StoreController::class, 'updateAvailability'])->name('store.availability');

        // Account
        Route::get('account/profile', [AccountController::class, 'profile'])->name('account.profile');
        Route::put('account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
        Route::get('account/password', [AccountController::class, 'password'])->name('account.password');
        Route::put('account/password', [AccountController::class, 'updatePassword'])->middleware('throttle:6,1')->name('account.password.update');
        Route::get('account/settings', [AccountController::class, 'settings'])->name('account.settings');
        Route::post('account/settings/sign-out-others', [AccountController::class, 'signOutOthers'])
            ->middleware('throttle:6,1')->name('account.settings.signout');
    });
});