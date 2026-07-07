<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        // Intercept generic system emails first so they don't get hijacked by 'deposit' DB templates
        if (isset($this->data['body']) || isset($this->data['greeting'])) {
            return (new MailMessage)
                ->subject($this->data['subject'] ?? 'Notification')
                ->view('email.generic', $this->data);
        }

        $template = EmailTemplate::where('slug', 'deposit')->first();
        if ($template) {
            return (new MailMessage)
                ->subject($template->subject)
                ->view('email.layout', array_merge([
                    'title' => $template->subject,
                    'content' => $template->content,
                ], $this->data));
        }

        return (new MailMessage)
            ->subject($this->data['subject'] ?? 'Deposit Confirmed')
            ->view('email.deposit', [
                'amount' => $this->data['amount'] ?? '$0.00',
                'method' => $this->data['method'] ?? 'Deposit',
                'trx' => $this->data['trx'] ?? 'N/A',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
