<?php

namespace App\Http\Controllers;

use App\Mail\UserInvitationMail;
use App\Mail\UserPasswordResetMail;
use App\Models\AuditLog;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * UserController
 *
 * Rôle : gère les comptes utilisateurs de la plateforme.
 * Accessible uniquement aux Super Admins.
 *
 * index()         → liste tous les utilisateurs + matrice permissions
 * create()        → formulaire de création
 * store()         → crée le compte + envoie email d'invitation
 * edit()          → formulaire de modification
 * update()        → met à jour un utilisateur
 * destroy()       → supprime définitivement un utilisateur
 * resetPassword() → réinitialise le mot de passe + envoie email
 */
class UserController extends Controller
{
    // Mot de passe par défaut pour les nouveaux comptes
    const DEFAULT_PASSWORD = 'Azerty1234@';

    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        $users = User::orderBy('name')->get();

        $stats = [
            'total'    => $users->count(),
            'active'   => $users->where('is_active', true)->count(),
            'inactive' => $users->where('is_active', false)->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        return view('users.create');
    }

    /**
     * Crée un nouvel utilisateur et envoie l'email d'invitation.
     *
     * Processus :
     * 1. Valide les données
     * 2. Crée le compte avec must_change_password = true
     * 3. Envoie l'email avec les identifiants
     * 4. Log l'action
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password']             = Hash::make(self::DEFAULT_PASSWORD);
        $data['is_active']            = true;
        $data['must_change_password'] = true;

        $user = User::create($data);

        // Envoie l'email d'invitation avec le mot de passe par défaut
        try {
            Mail::to($user->email)->send(new UserInvitationMail($user, self::DEFAULT_PASSWORD));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Invitation email failed for {$user->email}: {$e->getMessage()}");
        }

        AuditLog::log('user.created', 'user', $user->id, [
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', "Compte de \"{$user->name}\" créé. Un email d'invitation a été envoyé à {$user->email}.");
    }

    public function edit(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        AuditLog::log('user.updated', 'user', $user->id, [
            'name' => $user->name,
            'role' => $user->role,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', "Utilisateur \"{$user->name}\" mis à jour.");
    }

    /**
     * Supprime définitivement un utilisateur.
     *
     * On ne peut pas se supprimer soi-même.
     * Les audit logs orphelins sont conservés (user_id nullable).
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $name = $user->name;
        $user->delete();

        AuditLog::log('user.deleted', 'user', $user->id, ['name' => $name]);

        return back()->with('success', "Compte de \"{$name}\" supprimé.");
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur.
     *
     * Processus :
     * 1. Remet le mot de passe par défaut
     * 2. Active must_change_password = true
     * 3. Envoie l'email de réinitialisation
     */
    public function resetPassword(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Utilisez la page profil pour changer votre propre mot de passe.');
        }

        $user->update([
            'password'             => Hash::make(self::DEFAULT_PASSWORD),
            'must_change_password' => true,
        ]);

        try {
            Mail::to($user->email)->send(new UserPasswordResetMail($user, self::DEFAULT_PASSWORD));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Reset email failed for {$user->email}: {$e->getMessage()}");
        }

        AuditLog::log('user.password_reset', 'user', $user->id, [
            'reset_by' => auth()->user()->name,
        ]);

        return back()->with('success', "Mot de passe de \"{$user->name}\" réinitialisé. Un email a été envoyé.");
    }
}