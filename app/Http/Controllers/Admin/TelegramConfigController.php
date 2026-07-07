<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramConfig;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;

class TelegramConfigController extends Controller
{
    public function index()
    {
        $configs = TelegramConfig::orderByDesc('id')->get();
        $eventLabels = TelegramConfig::getEventLabels();

        return view('admin.telegram', compact('configs', 'eventLabels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bot_token' => 'required|string|max:500',
            'chat_id' => 'required|string|max:255',
        ]);

        TelegramConfig::create([
            'name' => $request->name,
            'bot_token' => $request->bot_token,
            'chat_id' => $request->chat_id,
            'notification_types' => $request->notification_types ?? [],
            'is_active' => true,
        ]);

        return back()->with('success', 'Telegram bot added successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:telegram_configs,id',
            'name' => 'required|string|max:255',
            'bot_token' => 'required|string|max:500',
            'chat_id' => 'required|string|max:255',
        ]);

        $config = TelegramConfig::findOrFail($request->id);
        $config->update([
            'name' => $request->name,
            'bot_token' => $request->bot_token,
            'chat_id' => $request->chat_id,
            'notification_types' => $request->notification_types ?? [],
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Telegram bot updated.');
    }

    public function destroy($id)
    {
        TelegramConfig::findOrFail($id)->delete();

        return back()->with('success', 'Telegram bot deleted.');
    }

    public function test($id)
    {
        $config = TelegramConfig::findOrFail($id);
        $success = TelegramNotificationService::sendTest($config);

        if ($success) {
            return back()->with('success', 'Test message sent successfully!');
        }

        return back()->with('error', 'Failed to send test message. Check your bot token and chat ID.');
    }
}
