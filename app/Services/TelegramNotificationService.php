<?php

namespace App\Services;

use App\Models\TelegramConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    /**
     * Send a notification for a given event.
     *
     * @param  string  $event  Event slug (e.g., 'deposit_submitted')
     * @param  array  $data  Context data for the message
     */
    public static function send(string $event, array $data = []): void
    {
        $configs = TelegramConfig::where('is_active', true)->get();

        foreach ($configs as $config) {
            $types = $config->notification_types ?? [];

            if (! in_array($event, $types)) {
                continue;
            }

            $message = self::buildMessage($event, $data);

            try {
                Http::timeout(3)->post("https://api.telegram.org/bot{$config->bot_token}/sendMessage", [
                    'chat_id' => $config->chat_id,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);
            } catch (\Exception $e) {
                Log::error("Telegram notification failed for config [{$config->name}]: ".$e->getMessage());
            }
        }
    }

    /**
     * Send a test message to verify bot configuration.
     */
    public static function sendTest(TelegramConfig $config): bool
    {
        try {
            $response = Http::timeout(5)->post("https://api.telegram.org/bot{$config->bot_token}/sendMessage", [
                'chat_id' => $config->chat_id,
                'text' => "✅ <b>Test Notification</b>\n\nThis is a test message from your trading platform.\nBot: {$config->name}\nTimestamp: ".now()->toDateTimeString(),
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram test failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Build a formatted message for a given event.
     */
    protected static function buildMessage(string $event, array $data): string
    {
        $siteName = config('app.name', 'Trading Platform');

        switch ($event) {
            case 'deposit_submitted':
                $user = $data['user'] ?? 'Unknown';
                $amount = $data['amount'] ?? '0.00';
                $method = $data['method'] ?? 'N/A';

                return "💰 <b>New Deposit Submitted</b>\n\n👤 User: {$user}\n💵 Amount: \${$amount}\n📋 Method: {$method}\n🕐 Time: ".now()->toDateTimeString();

            case 'withdrawal_requested':
                $user = $data['user'] ?? 'Unknown';
                $amount = $data['amount'] ?? '0.00';
                $wallet = $data['wallet'] ?? 'N/A';

                return "🏦 <b>Withdrawal Request</b>\n\n👤 User: {$user}\n💵 Amount: \${$amount}\n📋 Wallet: {$wallet}\n🕐 Time: ".now()->toDateTimeString();

            case 'support_ticket_created':
                $user = $data['user'] ?? 'Unknown';
                $subject = $data['subject'] ?? 'N/A';

                return "🎫 <b>New Support Ticket</b>\n\n👤 User: {$user}\n📋 Subject: {$subject}\n🕐 Time: ".now()->toDateTimeString();

            case 'trade_executed':
                $user = $data['user'] ?? 'Unknown';
                $pair = $data['pair'] ?? 'N/A';
                $type = $data['type'] ?? 'N/A';
                $amount = $data['amount'] ?? '0.00';

                return "📊 <b>Trade Executed</b>\n\n👤 User: {$user}\n📈 Pair: {$pair}\n🔄 Type: {$type}\n💵 Amount: \${$amount}\n🕐 Time: ".now()->toDateTimeString();

            case 'kyc_submitted':
                $user = $data['user'] ?? 'Unknown';
                $docType = $data['doc_type'] ?? 'N/A';

                return "🪪 <b>KYC Document Submitted</b>\n\n👤 User: {$user}\n📄 Document: {$docType}\n🕐 Time: ".now()->toDateTimeString();

            default:
                return "📢 <b>Platform Alert</b>\n\nEvent: {$event}\n".json_encode($data)."\n🕐 ".now()->toDateTimeString();
        }
    }
}
