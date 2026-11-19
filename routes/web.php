<?php

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/vendors', [PublicController::class, 'vendors'])->name('public.vendors');
Route::get('/vendors/{id}', [PublicController::class, 'vendorDetails'])->name('public.vendors.show');
// trigger language scan
