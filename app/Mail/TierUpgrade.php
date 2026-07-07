<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TierUpgrade extends Mailable
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
            subject: 'Account Tier Upgraded',
        );
    }

    public function content()
    {
        $template = EmailTemplate::where('slug', 'tier')->first();
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
            view: 'email.tier_upgrade',
            with: [
                'tier' => $this->data['tier'] ?? 'Gold',
                'benefit1' => $this->data['benefit1'] ?? 'Reduced trading commissions',
                'benefit2' => $this->data['benefit2'] ?? 'Priority customer support',
                'benefit3' => $this->data['benefit3'] ?? 'Exclusive market research',
                'benefit4' => $this->data['benefit4'] ?? 'Higher withdrawal limits',
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}
