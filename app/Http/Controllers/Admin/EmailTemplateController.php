<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();

        return view('admin.emails.index', compact('templates'));
    }

    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);

        return view('admin.emails.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->update($request->only(['subject', 'content']));

        return back()->with('status', 'Template updated successfully');
    }

    public function preview(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        // Mock data for preview
        $data = [
            'user' => (object) ['name' => 'John Doe', 'email' => 'john@example.com'],
            'otp' => '123456',
            'amount' => '$1,000.00',
            'method' => 'Bank Transfer',
            'trx' => 'TRX998877',
            'side' => 'BUY',
            'symbol' => 'BTC',
            'quantity' => '0.05',
            'price' => '$20,000.00',
            'total' => '$1,000.00',
            'fees' => '$5.00',
            'tier' => 'Platinum',
            'benefit1' => 'Low Fees',
            'benefit2' => 'Fast Support',
            'benefit3' => 'Market Insights',
            'benefit4' => 'Higher Limits',
        ];

        // This is a simple preview that renders the raw content.
        // For a true preview that extends the layout, we might need a temporary view.
        return view('email.layout', [
            'title' => $template->subject,
        ])->with(['content' => $template->content] + $data);
    }
}
