<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Events\QueueUpdated;

class TicketController extends Controller
{
    // ... method takeQueue dan callNext tetap ada ...

    public function takeQueue(Request $request)
    {
        $request->validate(['session' => 'required|in:A,B']);
        
        $session = $request->session;
        $lastTicket = Ticket::where('session', $session)->whereDate('created_at', today())->latest()->first();
        $lastNumber = $lastTicket ? intval(substr($lastTicket->queue_number, 1)) : 0;
        $queueCode = $session . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        $ticket = Ticket::create([
            'queue_number' => $queueCode,
            'session'      => $session,
            'status'       => 'waiting',
            'user_phone'   => $request->phone ?? null
        ]);
        
        // Broadcast update statistik agar dashboard berubah realtime
        broadcast(new QueueUpdated($ticket));

        return response()->json($ticket);
    }

    public function callNext(Request $request)
    {
        // ... (Kode sama seperti sebelumnya) ...
        $request->validate(['session' => 'required|in:A,B']);
        
        // Tandai yang sedang dipanggil sebelumnya jadi 'finished' (opsional, tergantung flow)
        // Ticket::where('session', $request->session)->where('status', 'called')->update(['status' => 'finished']);

        $nextTicket = Ticket::where('session', $request->session)
            ->where('status', 'waiting')
            ->oldest()
            ->first();

        if (!$nextTicket) {
            return response()->json(['message' => 'Empty'], 404);
        }

        $nextTicket->update(['status' => 'called']);
        broadcast(new QueueUpdated($nextTicket));

        return response()->json(['data' => $nextTicket]);
    }

    // --- FITUR BARU UNTUK DASHBOARD ---

    // 1. API Data Dashboard (Summary)
    public function dashboardStats()
    {
        // Hitung antrian waiting
        $waitingA = Ticket::where('session', 'A')->where('status', 'waiting')->count();
        $waitingB = Ticket::where('session', 'B')->where('status', 'waiting')->count();

        // Ambil tiket yang sedang dipanggil (status 'called')
        $current = Ticket::where('status', 'called')->latest('updated_at')->first();

        // Ambil List Waiting
        $listA = Ticket::where('session', 'A')->where('status', 'waiting')->oldest()->limit(5)->get();
        $listB = Ticket::where('session', 'B')->where('status', 'waiting')->oldest()->limit(5)->get();

        return response()->json([
            'waitingA' => $waitingA,
            'waitingB' => $waitingB,
            'current'  => $current, // Tiket yang sedang aktif
            'listA'    => $listA,
            'listB'    => $listB,
        ]);
    }

    // 2. API History Pemanggilan
    public function history()
    {
        // Ambil 10 tiket terakhir yang statusnya sudah dipanggil atau selesai
        $history = Ticket::whereIn('status', ['called', 'finished'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return response()->json($history);
    }

    // 3. API Skip / Finish Manual
    public function updateStatus(Request $request)
    {
        $request->validate([
            'queue_number' => 'required',
            'status'       => 'required|in:finished,skipped'
        ]);

        $ticket = Ticket::where('queue_number', $request->queue_number)->first();

        if(!$ticket) return response()->json(['message' => 'Not found'], 404);

        $ticket->update(['status' => $request->status]);
        
        // Broadcast agar list antrian di dashboard berkurang secara realtime
        broadcast(new QueueUpdated($ticket));

        return response()->json(['message' => 'Updated', 'data' => $ticket]);
    }
}
