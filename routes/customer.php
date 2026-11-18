<?php

use App\Http\Controllers\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
Route::get('/vendors', [CustomerController::class, 'vendors'])->name('vendors');
Route::get('/vendors/{id}', [CustomerController::class, 'vendorDetails'])->name('vendors.show');

Route::get('/cart', [CustomerController::class, 'cart'])->name('cart');
Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update', [CustomerController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove', [CustomerController::class, 'removeFromCart'])->name('cart.remove');

Route::get('/checkout', [CustomerController::class, 'checkout'])->name('checkout');
Route::post('/checkout/place-order', [CustomerController::class, 'placeOrder'])->name('checkout.place');

Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
Route::post('/orders/{orderNumber}/gcash', [CustomerController::class, 'submitGcash'])->name('orders.gcash');
Route::get('/tracking/{orderNumber?}', [CustomerController::class, 'tracking'])->name('tracking');

Route::get('/suki-points', [CustomerController::class, 'sukiPoints'])->name('suki-points');
Route::post('/suki-points/redeem', [CustomerController::class, 'redeemPoints'])->name('suki-points.redeem');

Route::get('/buy-for-me', [CustomerController::class, 'buyForMe'])->name('buy-for-me');
Route::post('/buy-for-me', [CustomerController::class, 'submitBuyForMe'])->name('buy-for-me.submit');

Route::get('/notifications', [CustomerController::class, 'notifications'])->name('notifications');
Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
Route::post('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');