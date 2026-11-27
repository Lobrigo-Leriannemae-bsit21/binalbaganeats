<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\AccountSettingsController;
use App\Http\Controllers\Customer\BuyForMeController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\MenuItemController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\PasswordController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\SukiPointController;
use App\Http\Controllers\Customer\VendorController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\FoundationController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

// Public help center (general information only)
Route::get('/help', HelpController::class)->name('help');

// Phase 0 diagnostics (only available when APP_ENV=local)
Route::get('/foundation-check', FoundationController::class)->name('foundation');

// Guests only
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Customer area: signed in + approved account + customer role (all checked server-side)
Route::middleware(['auth', 'approved', 'role:customer'])->group(function () {
    Route::get('/dashboard', CustomerDashboardController::class)->name('dashboard');

    // Discovery
    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->whereNumber('vendor')->name('vendors.show');
    Route::get('/menu/{item}', [MenuItemController::class, 'show'])->whereNumber('item')->name('menu.show');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/items', [CartController::class, 'store'])->middleware('throttle:60,1')->name('cart.store');
    Route::patch('/cart/items/{item}', [CartController::class, 'update'])->whereNumber('item')->name('cart.update');
    Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->whereNumber('item')->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');

    // Orders (ownership is enforced by OrderPolicy)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->whereNumber('order')->name('orders.show');
    Route::get('/orders/{order}/confirmation', [OrderController::class, 'confirmation'])->whereNumber('order')->name('orders.confirmation');
    Route::get('/orders/{order}/track', [OrderController::class, 'track'])->whereNumber('order')->name('orders.track');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->whereNumber('order')->name('orders.cancel');

    // GCash payment proof
    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])
        ->whereNumber('order')->middleware('throttle:10,1')->name('orders.payment.store');
    Route::get('/orders/{order}/payment-proof', [PaymentController::class, 'proof'])
        ->whereNumber('order')->name('orders.payment.proof');

    // Suki Points (every query is scoped to the signed-in customer)
    Route::get('/suki-points', [SukiPointController::class, 'index'])->name('suki.index');
    Route::get('/suki-points/history', [SukiPointController::class, 'history'])->name('suki.history');
    Route::get('/suki-points/redeem', [SukiPointController::class, 'redeem'])->name('suki.redeem');

    // Buy-for-Me (ownership is enforced by BuyForMeRequestPolicy)
    Route::get('/buy-for-me', [BuyForMeController::class, 'index'])->name('buyforme.index');
    Route::get('/buy-for-me/create', [BuyForMeController::class, 'create'])->name('buyforme.create');
    Route::post('/buy-for-me', [BuyForMeController::class, 'store'])->middleware('throttle:10,1')->name('buyforme.store');
    Route::get('/buy-for-me/{buyForMe}', [BuyForMeController::class, 'show'])->whereNumber('buyForMe')->name('buyforme.show');
    Route::post('/buy-for-me/{buyForMe}/cancel', [BuyForMeController::class, 'cancel'])->whereNumber('buyForMe')->name('buyforme.cancel');
    Route::post('/buy-for-me/{buyForMe}/advance', [BuyForMeController::class, 'advance'])
        ->whereNumber('buyForMe')->middleware('throttle:10,1')->name('buyforme.advance');
    Route::get('/buy-for-me/{buyForMe}/advance-proof', [BuyForMeController::class, 'advanceProof'])
        ->whereNumber('buyForMe')->name('buyforme.advance.proof');
    Route::get('/buy-for-me/{buyForMe}/receipt', [BuyForMeController::class, 'receipt'])
        ->whereNumber('buyForMe')->name('buyforme.receipt');

    // Account (everything acts on the signed-in customer only)
    Route::get('/account/profile', [ProfileController::class, 'edit'])->name('account.profile');
    Route::put('/account/profile', [ProfileController::class, 'update'])->name('account.profile.update');
    Route::get('/account/address', [ProfileController::class, 'address'])->name('account.address');
    Route::put('/account/address', [ProfileController::class, 'updateAddress'])->name('account.address.update');
    Route::get('/account/password', [PasswordController::class, 'edit'])->name('account.password');
    Route::put('/account/password', [PasswordController::class, 'update'])->middleware('throttle:6,1')->name('account.password.update');
    Route::get('/account/settings', [AccountSettingsController::class, 'index'])->name('account.settings');
    Route::post('/account/settings/sign-out-others', [AccountSettingsController::class, 'signOutOthers'])
        ->middleware('throttle:6,1')->name('account.settings.signout');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])
        ->whereUuid('notification')->name('notifications.read');
});