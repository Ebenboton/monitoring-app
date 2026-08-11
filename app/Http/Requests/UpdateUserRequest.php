<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateUserRequest
 *
 * Rôle : valide les données de modification d'un utilisateur.
 *
 * Différence avec StoreUserRequest :
 * - email    : unique sauf pour l'utilisateur en cours de modification
 * - password : optionnel — si vide, le mot de passe n'est pas changé
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'     => 'required|string|max:150',
            'email'    => "required|email|unique:users,email,{$userId},id|max:255",
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|in:super_admin,admin,technician,observer',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom est obligatoire.',
            'email.required'     => 'L\'email est obligatoire.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'role.required'      => 'Le rôle est obligatoire.',
        ];
    }
}
