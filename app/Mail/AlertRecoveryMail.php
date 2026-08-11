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

class AlertRecoveryMail extends Mailable implements ShouldQueue
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
        $duration = $this->incident->duration_seconds
            ? gmdate('H\hi\ms\s', $this->incident->duration_seconds)
            : 'inconnue';

        return new Envelope(
            subject: "✅ RÉTABLI — {$this->application->name} est de nouveau UP (durée : {$duration})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alert-recovery',
        );
    }
}