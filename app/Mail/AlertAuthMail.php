<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertAuthMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public Application $application,
        public Incident $incident,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔴 AUTH FAILED — {$this->application->name} : échec d'authentification",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alert-auth',
        );
    }
}
