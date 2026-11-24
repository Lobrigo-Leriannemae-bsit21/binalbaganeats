<?php

use App\Http\Controllers\Vendor\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');

Route::get('/storefront', [VendorController::class, 'storefront'])->name('storefront');
Route::post('/storefront', [VendorController::class, 'updateStorefront'])->name('storefront.update');

Route::get('/menu', [VendorController::class, 'menu'])->name('menu');
Route::post('/menu/add', [VendorController::class, 'addMenuItem'])->name('menu.add');
Route::post('/menu/{id}/update', [VendorController::class, 'updateMenuItem'])->name('menu.update');
Route::post('/menu/{id}/delete', [VendorController::class, 'deleteMenuItem'])->name('menu.delete');

Route::get('/incoming-orders', [VendorController::class, 'incomingOrders'])->name('incoming-orders');
Route::post('/orders/{orderNumber}/decision', [VendorController::class, 'orderDecision'])->name('orders.decision');
Route::post('/orders/{orderNumber}/verify-gcash', [VendorController::class, 'verifyGcash'])->name('orders.verify-gcash');

Route::get('/orders', [VendorController::class, 'orders'])->name('orders');
Route::post('/orders/{orderNumber}/status', [VendorController::class, 'updateOrderStatus'])->name('orders.status');

Route::get('/completed-orders', [VendorController::class, 'completedOrders'])->name('completed-orders');
Route::get('/sales', [VendorController::class, 'sales'])->name('sales');
Route::get('/earnings', [VendorController::class, 'earnings'])->name('earnings');
Route::get('/notifications', [VendorController::class, 'notifications'])->name('notifications');

Route::get('/profile', [VendorController::class, 'profile'])->name('profile');
Route::post('/profile', [VendorController::class, 'updateProfile'])->name('profile.update');