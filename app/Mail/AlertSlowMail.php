<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertSlowMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public Application $application,
        public int $responseTime,
        public int $threshold,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🟡 DÉGRADÉ — {$this->application->name} est SLOW ({$this->responseTime}ms)",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alert-slow',
        );
    }
}