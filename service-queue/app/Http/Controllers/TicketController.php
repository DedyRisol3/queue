<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Application\Ticket\TicketService;
use App\Events\QueueUpdated;

class TicketController extends Controller
{
    public function takeQueue(TicketService $service)
    {
        $ticket = $service->generateTicket();

        broadcast(new QueueUpdated($ticket));

        return response()->json($ticket);
    }

    public function callNext(TicketService $service)
    {
        $ticket = $service->callNextTicket();

        broadcast(new QueueUpdated($ticket));

        return response()->json($ticket);
    }
}
