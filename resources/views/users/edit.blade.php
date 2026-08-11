@extends('layouts.app')

@section('title', 'Modifier — ' . $user->name)

@section('page-title', 'Modifier l\'utilisateur')
@section('page-subtitle', 'Dashboard / Utilisateurs / ' . $user->name)

@section('page-actions')
<a href="{{ route('users.index') }}"
    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition">
    Annuler
</a>
<button type="submit" form="user-form"
    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
    Enregistrer
</button>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form id="user-form" method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Informations du compte</h2>
            <div class="space-y-4">

                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Nom complet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Adresse email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Rôle <span class="text-red-500">*</span>
                    </label>
                    <select name="role"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="super_admin" @selected(old('role', $user->role)==='super_admin')>Super Admin</option>
                        <option value="admin" @selected(old('role', $user->role)==='admin')>Administrateur</option>
                        <option value="technician" @selected(old('role', $user->role)==='technician')>Technicien</option>
                        <option value="observer" @selected(old('role', $user->role)==='observer')>Observateur</option>
                    </select>
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                            Nouveau mot de passe
                            <span class="text-gray-400 dark:text-gray-500 font-normal">(optionnel)</span>
                        </label>
                        <input type="password" name="password"
                            placeholder="Laisser vide pour ne pas modifier"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('password') border-red-500 @enderror">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                            Confirmer le mot de passe
                        </label>
                        <input type="password" name="password_confirmation"
                            placeholder="Répétez le nouveau mot de passe"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>
            </div>
        </div>

        {{-- Info compte --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-400 dark:text-gray-500">Compte créé le</span>
                    <p class="font-medium text-gray-800 dark:text-gray-100 mt-0.5">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div>
                    <span class="text-gray-400 dark:text-gray-500">Dernière connexion</span>
                    <p class="font-medium text-gray-800 dark:text-gray-100 mt-0.5">
                        {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y à H:i') : 'Jamais' }}
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection