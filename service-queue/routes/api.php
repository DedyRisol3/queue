<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

// Route untuk Kiosk (Ambil Antrian)
Route::post('/tickets/take', [TicketController::class, 'takeQueue']);

// Route untuk Admin (Panggil, Statistik, dll)
Route::post('/tickets/call-next', [TicketController::class, 'callNext']);


Route::get('/tickets/stats', [TicketController::class, 'dashboardStats']);
Route::get('/tickets/history', [TicketController::class, 'history']);
Route::post('/tickets/update-status', [TicketController::class, 'updateStatus']);