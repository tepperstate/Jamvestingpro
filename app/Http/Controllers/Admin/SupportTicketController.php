<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with('user')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $openCount = SupportTicket::where('status', 'open')->count();
        $inProgressCount = SupportTicket::where('status', 'in-progress')->count();
        $closedCount = SupportTicket::where('status', 'closed')->count();

        return view('admin.support_tickets', compact('tickets', 'openCount', 'inProgressCount', 'closedCount'));
    }

    public function reply(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:support_tickets,id',
            'admin_reply' => 'required|string',
        ]);

        SupportTicket::where('id', $request->id)->update([
            'admin_reply' => $request->admin_reply,
            'status' => 'in-progress',
            'replied_at' => Carbon::now(),
        ]);

        return back()->with('status', 'Reply sent successfully.');
    }

    public function updateStatus(Request $request)
    {
        SupportTicket::where('id', $request->id)->update([
            'status' => $request->status,
        ]);

        return response()->json(['status' => true, 'message' => 'Ticket status updated.']);
    }

    public function destroy($id)
    {
        SupportTicket::findOrFail($id)->delete();

        return back()->with('status', 'Ticket deleted.');
    }
}
