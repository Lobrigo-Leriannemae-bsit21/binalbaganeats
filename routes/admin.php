<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

Route::get('/accounts', [AdminController::class, 'accounts'])->name('accounts');
Route::get('/approvals', [AdminController::class, 'pendingApprovals'])->name('approvals');
Route::post('/approvals/{user}/approve', [AdminController::class, 'approveAccount'])->name('approvals.approve');
Route::post('/approvals/{user}/reject', [AdminController::class, 'rejectAccount'])->name('approvals.reject');

Route::get('/vendors', [AdminController::class, 'vendors'])->name('vendors');
Route::get('/riders', [AdminController::class, 'riders'])->name('riders');

Route::get('/zones', [AdminController::class, 'zones'])->name('zones');
Route::post('/zones/save', [AdminController::class, 'saveZone'])->name('zones.save');

Route::get('/fees', [AdminController::class, 'fees'])->name('fees');
Route::post('/fees/update', [AdminController::class, 'updateFee'])->name('fees.update');

Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring');
Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');