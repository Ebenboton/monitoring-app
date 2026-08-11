<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertSslMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public Application $application,
        public int $daysRemaining,
    ) {}

    public function envelope(): Envelope
    {
        $urgency = $this->daysRemaining < 7 ? '🔴 URGENT' : '🟡 ATTENTION';
        return new Envelope(
            subject: "{$urgency} — Certificat SSL {$this->application->name} expire dans {$this->daysRemaining}j",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alert-ssl',
        );
    }
}