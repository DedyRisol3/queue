<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::post('/queue/take', function () {
    return Http::post('http://service-queue:8000/api/tickets/take')->json();
});

Route::post('/admin/call-next', function () {
    return Http::post('http://service-admin:8000/api/admin/call')->json();
});

Route::get('/queue/current', function () {
    return Http::get('http://service-queue:8000/api/tickets/current')->json();
});
