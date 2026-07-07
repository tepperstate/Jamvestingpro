<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('exchange.support_tickets', compact('tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        SupportTicket::create([
            'user_id' => auth()->user()->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        try {
            TelegramNotificationService::send('support_ticket_created', [
                'user' => auth()->user()->first_name.' '.auth()->user()->last_name,
                'subject' => $request->subject,
            ]);
        } catch (\Exception $e) {
        }

        return back()->with('status', 'Your support ticket has been submitted. Our team will respond shortly.');
    }

    public function show($id)
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();

        return response()->json(['data' => $ticket]);
    }
}
