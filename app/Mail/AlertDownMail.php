<?php

namespace App\Mail;

use App\Models\Incident;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertDownMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;


    public function __construct(
        public Application $application,
        public Incident $incident,
        public ?int $httpCode = null,
        public ?int $responseTime = null,
        public ?string $errorMessage = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔴 PANNE — {$this->application->name} est DOWN",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alert-down',
        );
    }
}
