<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminQueueController;

// Halaman Dashboard Admin
Route::get('/dashboard', function () {
    return view('dashboard');
});

// Route lama untuk API (opsional jika sudah pakai axios langsung ke service-queue)
Route::post('/admin/call', [AdminQueueController::class, 'callNext']);