<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * PasswordChangeController
 *
 * Rôle : gère le changement obligatoire de mot de passe.
 *
 * show()   → affiche le formulaire de changement
 * update() → valide et enregistre le nouveau mot de passe
 *
 * Critères du nouveau mot de passe :
 * - Minimum 8 caractères
 * - Au moins 1 lettre majuscule
 * - Au moins 1 lettre minuscule
 * - Au moins 1 chiffre
 * - Au moins 1 caractère spécial
 * - Différent du mot de passe temporaire
 */
class PasswordChangeController extends Controller
{
    public function show()
    {
        return view('auth.password-change');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        // Vérifie que le nouveau mot de passe est différent du temporaire
        if (Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors([
                'password' => 'Le nouveau mot de passe doit être différent du mot de passe temporaire.',
            ]);
        }

        auth()->user()->update([
            'password'             => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()
            ->route('applications.index')
            ->with('success', 'Mot de passe mis à jour avec succès. Bienvenue sur M-Monitoring !');
    }
}