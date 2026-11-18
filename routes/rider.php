<?php

use App\Http\Controllers\Rider\RiderController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [RiderController::class, 'dashboard'])->name('dashboard');
Route::get('/available-jobs', [RiderController::class, 'availableJobs'])->name('available-jobs');
Route::post('/jobs/{orderNumber}/accept', [RiderController::class, 'acceptJob'])->name('jobs.accept');

Route::get('/assigned', [RiderController::class, 'assignedDeliveries'])->name('assigned');
Route::get('/details/{orderNumber?}', [RiderController::class, 'deliveryDetails'])->name('details');
Route::post('/details/{orderNumber}/status', [RiderController::class, 'updateDeliveryStatus'])->name('status.update');

Route::get('/buy-for-me', [RiderController::class, 'buyForMeRequests'])->name('buy-for-me');
Route::post('/buy-for-me/{requestId}/accept', [RiderController::class, 'acceptBuyForMe'])->name('buy-for-me.accept');
Route::post('/buy-for-me/{requestId}/expense', [RiderController::class, 'recordExpense'])->name('buy-for-me.expense');
Route::post('/buy-for-me/{requestId}/settle', [RiderController::class, 'updateSettlement'])->name('buy-for-me.settle');

Route::get('/expenses', [RiderController::class, 'expenses'])->name('expenses');
Route::get('/history', [RiderController::class, 'history'])->name('history');
Route::get('/notifications', [RiderController::class, 'notifications'])->name('notifications');

Route::get('/profile', [RiderController::class, 'profile'])->name('profile');
Route::post('/profile', [RiderController::class, 'updateProfile'])->name('profile.update');