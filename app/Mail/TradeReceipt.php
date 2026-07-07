<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TradeReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Trade Execution Receipt',
        );
    }

    public function content()
    {
        $template = EmailTemplate::where('slug', 'trade')->first();
        if ($template) {
            return new Content(
                view: 'email.layout',
                with: array_merge([
                    'title' => $template->subject,
                    'content' => $template->content,
                ], $this->data),
            );
        }

        return new Content(
            view: 'email.trade_receipt',
            with: [
                'side' => $this->data['side'] ?? 'BUY',
                'symbol' => $this->data['symbol'] ?? 'AAPL',
                'quantity' => $this->data['quantity'] ?? '1.00',
                'price' => $this->data['price'] ?? '$150.00',
                'total' => $this->data['total'] ?? '$150.00',
                'fees' => $this->data['fees'] ?? '$0.00',
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}
