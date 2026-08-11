<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreUserRequest
 *
 * Rôle : valide les données de création d'un utilisateur.
 *
 * Règles :
 * - name     : obligatoire, max 150 caractères
 * - email    : obligatoire, unique en base, format email valide
 * - password : obligatoire, minimum 8 caractères
 * - role     : obligatoire, parmi les 4 rôles définis
 */
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:150',
            'email' => 'required|email|unique:users,email|max:255',
            'role'  => 'required|in:super_admin,admin,technician,observer',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom est obligatoire.',
            'email.required'     => 'L\'email est obligatoire.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'email.email'        => 'L\'email doit être valide.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'role.required'      => 'Le rôle est obligatoire.',
            'role.in'            => 'Le rôle sélectionné est invalide.',
        ];
    }
}
