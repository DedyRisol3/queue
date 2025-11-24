<?php

use Illuminate\Support\Facades\Route;

// Halaman Display TV (Monitor)
Route::get('/', function () {
    return view('display');
});

// Halaman Kiosk (Tombol Ambil Antrian) -> INI WAJIB ADA
Route::get('/kiosk', function () {
    return view('take');
});