<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AdminLog; // Pastikan model ini ada atau hapus jika tidak perlu

class AdminQueueController extends Controller
{
    // Method ini dipanggil saat Admin menekan tombol "Panggil A" atau "Panggil B"
    public function callNext(Request $request)
    {
        $session = $request->input('session'); // 'A' atau 'B'

        // Kirim request ke Service Queue (Komunikasi antar Microservice)
        // Pastikan nama service di docker-compose adalah 'service-queue'
        $response = Http::post('http://service-queue:8000/api/tickets/call-next', [
            'session' => $session
        ]);

        if ($response->successful()) {
            // Simpan log aktivitas admin (Opsional)
            // AdminLog::create(['action' => "Called Session $session"]);
            
            return $response->json();
        }

        return response()->json(['message' => 'Gagal memanggil antrian'], $response->status());
    }
}
