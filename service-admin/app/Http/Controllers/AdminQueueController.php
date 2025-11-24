<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\AdminLog;

class AdminQueueController extends Controller
{
    public function callNext()
    {
        // Kirim request ke service-queue
        $response = Http::post('http://service-queue:8000/api/tickets/call-next');

        // Simpan aktivitas admin
        AdminLog::create([
            'action' => 'call-next',
        ]);

        return $response->json();
    }
}
