<?php

namespace App\Notifications;

use App\Models\BankWirePaymentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BankWirePaymentConfirmed extends Notification
{
    use Queueable;

    protected $wireRequest;

    public function __construct(BankWirePaymentRequest $wireRequest)
    {
        $this->wireRequest = $wireRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Bank Wire Transfer Approved')
            ->greeting("Hello {$notifiable->name},")
            ->line("Great news! Your bank wire transfer request for {$this->wireRequest->amount} {$this->wireRequest->currency} has been approved and confirmed.")
            ->line("Reference: {$this->wireRequest->payment_reference}")
            ->action('View Dashboard', route('dashboard.index'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'wire_request_id' => $this->wireRequest->id,
            'reference' => $this->wireRequest->payment_reference,
            'amount' => $this->wireRequest->amount,
            'currency' => $this->wireRequest->currency,
            'message' => 'Your bank wire request has been approved and confirmed by finance.'
        ];
    }
}
