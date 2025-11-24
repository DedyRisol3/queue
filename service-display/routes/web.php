<?php

use Illuminate\Support\Facades\Route;

Route::get('/ambil', function () {
    return view('take');
});
Route::get('/display', function () {
    return view('display');
});
