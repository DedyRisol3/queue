<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Events\QueueUpdated;

class TicketController extends Controller
{
    

    public function takeQueue(Request $request)
    {
        // Validasi input
        $request->validate(['session' => 'required|in:A,B']);
        
        $session = $request->session;

        // Cek nomor terakhir hari ini
        $lastTicket = Ticket::where('session', $session)
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        // Buat nomor urut baru
        $lastNumber = $lastTicket ? intval(substr($lastTicket->queue_number, 1)) : 0;
        $queueCode = $session . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        // Simpan ke database
        $ticket = Ticket::create([
            'queue_number' => $queueCode,
            'session'      => $session,
            'status'       => 'waiting',
            'user_phone'   => $request->phone ?? null
        ]);

        // Coba broadcast (abaikan error jika gagal)
        try {
            broadcast(new QueueUpdated($ticket));
        } catch (\Exception $e) {
            // Silent fail biar antrian tetap jalan meski realtime mati
        }

        return response()->json($ticket);
    }

    // ... (Method lain opsional, yang penting takeQueue dulu)
}