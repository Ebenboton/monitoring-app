@extends('layouts.app')

@section('title', 'Nouvel utilisateur — M-Monitoring')

@section('page-title', 'Nouvel utilisateur')
@section('page-subtitle', 'Dashboard / Utilisateurs / Nouveau')

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
    Créer et inviter
</button>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form id="user-form" method="POST" action="{{ route('users.store') }}" class="space-y-6">
        @csrf

        {{-- Informations du compte --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Informations du compte</h2>
            <div class="space-y-4">

                {{-- Nom --}}
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Nom complet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="Ex: Jean Dupont"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Adresse email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="jean.dupont@exemple.com"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Rôle --}}
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Rôle <span class="text-red-500">*</span>
                    </label>
                    <select name="role"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('role') border-red-500 @enderror">
                        <option value="">-- Sélectionner un rôle --</option>
                        <option value="super_admin" @selected(old('role')==='super_admin' )>Super Admin</option>
                        <option value="admin" @selected(old('role')==='admin' )>Administrateur</option>
                        <option value="technician" @selected(old('role')==='technician' )>Technicien</option>
                        <option value="observer" @selected(old('role')==='observer' )>Observateur</option>
                    </select>
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                    {{-- Description des rôles --}}
                    <div class="mt-3 space-y-1.5 text-xs">
                        <p class="text-gray-500 dark:text-gray-400">
                            <span class="font-medium text-purple-600 dark:text-purple-400">Super Admin</span>
                            — Accès total : apps, users, alertes, paramètres, audit logs
                        </p>
                        <p class="text-gray-500 dark:text-gray-400">
                            <span class="font-medium text-blue-600 dark:text-blue-400">Administrateur</span>
                            — Gestion apps, alertes, maintenances — pas de gestion users
                        </p>
                        <p class="text-gray-500 dark:text-gray-400">
                            <span class="font-medium text-teal-600 dark:text-teal-400">Technicien</span>
                            — Consultation + acquittement incidents — pas de modification config
                        </p>
                        <p class="text-gray-500 dark:text-gray-400">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Observateur</span>
                            — Lecture seule du dashboard et des rapports
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info mot de passe automatique --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Mot de passe</h2>

            {{-- Critères du nouveau mot de passe --}}
            <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    Critères du nouveau mot de passe (imposés à l'utilisateur)
                </p>
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                        8 caractères minimum
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                        Une lettre majuscule (A-Z)
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                        Une lettre minuscule (a-z)
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                        Un chiffre (0-9)
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                        Un caractère spécial (@, #, $, !, etc.)
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                        Différent du mot de passe temporaire
                    </span>
                </div>
            </div>
        </div>


    </form>
</div>
@endsection