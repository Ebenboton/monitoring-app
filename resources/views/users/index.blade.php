@extends('layouts.app')

@section('title', 'Utilisateurs — M-Monitoring')

@section('page-title', 'Utilisateurs')
@section('page-subtitle', 'Dashboard / Utilisateurs')

@section('page-actions')
@if(auth()->user()->isSuperAdmin())
<a href="{{ route('users.create') }}"
    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
    Nouvel utilisateur
</a>
@endif
@endsection

@section('content')

{{-- Compteurs --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Total</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Actifs</p>
        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Inactifs</p>
        <p class="text-3xl font-bold text-gray-400 dark:text-gray-500">{{ $stats['inactive'] }}</p>
    </div>
</div>

{{-- Tableau --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Liste des utilisateurs</h3>
        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $stats['total'] }} utilisateur(s)</span>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Utilisateur</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Rôle</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Statut</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Dernière connexion</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            @php
            $roleBadge = [
            'super_admin' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
            'admin' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
            'technician' => 'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400',
            'observer' => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
            ];
            $roleLabel = [
            'super_admin' => 'Super Admin',
            'admin' => 'Administrateur',
            'technician' => 'Technicien',
            'observer' => 'Observateur',
            ];
            $rb = $roleBadge[$user->role] ?? 'bg-gray-100 text-gray-500';
            $rl = $roleLabel[$user->role] ?? $user->role;
            @endphp
            <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition {{ !$user->is_active ? 'opacity-60' : '' }}">

                {{-- Utilisateur --}}
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-xs font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">(vous)</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>

                {{-- Rôle --}}
                <td class="px-5 py-4">
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $rb }}">
                        {{ $rl }}
                    </span>
                </td>

                {{-- Statut --}}
                <td class="px-5 py-4">
                    @if($user->is_active)
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-600 dark:text-green-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Actif
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 dark:text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                        Inactif
                    </span>
                    @endif
                </td>

                {{-- Dernière connexion --}}
                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Jamais' }}
                </td>

                {{-- Actions --}}
                <td class="px-5 py-4">
                    <div class="flex items-center justify-end gap-2">
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('users.edit', $user) }}"
                            class="px-3 py-1.5 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded text-xs transition">
                            Modifier
                        </a>

                        @if($user->id !== auth()->id())
                        {{-- Réinitialiser mot de passe --}}
                        <form method="POST" action="{{ route('users.reset-password', $user) }}"
                            onsubmit="return confirm('Réinitialiser le mot de passe de {{ $user->name }} ?')">
                            @csrf
                            <button type="submit"
                                class="px-3 py-1.5 border border-orange-200 dark:border-orange-900/50 text-orange-500 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded text-xs font-medium transition">
                                Réinitialiser
                            </button>
                        </form>

                        {{-- Supprimer --}}
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                            onsubmit="return confirm('Supprimer définitivement {{ $user->name }} ?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-3 py-1.5 border border-red-200 dark:border-red-900/50 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded text-xs font-medium transition">
                                Supprimer
                            </button>
                        </form>
                        @endif
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-gray-400 dark:text-gray-500 py-12">
                    Aucun utilisateur trouvé.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection