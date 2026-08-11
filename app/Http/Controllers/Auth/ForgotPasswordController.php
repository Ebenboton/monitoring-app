<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\UserPasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * ForgotPasswordController
 *
 * Rôle : permet à un utilisateur de réinitialiser son mot de passe
 * de façon autonome depuis la page de connexion.
 *
 * Processus :
 * 1. L'utilisateur saisit son email
 * 2. Si l'email existe en base → mot de passe remis à la valeur par défaut
 * 3. must_change_password = true → obligé de rechanger à la prochaine connexion
 * 4. Email envoyé avec les identifiants temporaires
 *
 * Sécurité : on ne révèle pas si l'email existe ou non en base
 * (même message de confirmation dans les deux cas).
 */
class ForgotPasswordController extends Controller
{
    const DEFAULT_PASSWORD = 'Azerty1234@';

    /**
     * Affiche le formulaire de réinitialisation
     */
    public function show()
    {
        return view('auth.forgot-password-custom');
    }

    /**
     * Traite la demande de réinitialisation
     */
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'L\'adresse email doit être valide.',
        ]);

        $user = User::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        // Si l'utilisateur existe, on réinitialise
        if ($user) {
            $user->update([
                'password'             => Hash::make(self::DEFAULT_PASSWORD),
                'must_change_password' => true,
            ]);

            try {
                Mail::to($user->email)->send(new UserPasswordResetMail($user, self::DEFAULT_PASSWORD));
            } catch (\Exception $e) {
                Log::error("Reset email failed for {$user->email}: {$e->getMessage()}");
            }
        }

        // Même message qu'il existe ou non (sécurité anti-énumération)
        return back()->with('success',
            'Si un compte actif correspond à cet email, vous recevrez un email avec vos identifiants temporaires.'
        );
    }
}