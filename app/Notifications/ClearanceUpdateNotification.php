<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClearanceUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $mailData;

    /**
     * Pass an array containing 'subject', 'greeting', 'lines', and optional 'action'
     */
    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $email = (new MailMessage)
            ->subject($this->mailData['subject'])
            ->greeting($this->mailData['greeting'] ?? 'Hello,');

        foreach ($this->mailData['lines'] as $line) {
            $email->line($line);
        }

        if (isset($this->mailData['action'])) {
            $email->action($this->mailData['action']['text'], $notifiable['action']['url']);
        }

        return $email;
    }
}