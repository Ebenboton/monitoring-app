<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * UserPasswordResetMail
 *
 * Rôle : email envoyé lors de la réinitialisation du mot de passe
 * par le Super Admin. Informe l'utilisateur que son mot de passe
 * a été réinitialisé et lui donne ses nouveaux identifiants.
 */
class UserPasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public User $user,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'M-Monitoring — Réinitialisation de votre mot de passe',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-password-reset',
        );
    }
}