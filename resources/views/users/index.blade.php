@extends('layouts.app')

@section('title', 'Utilisateurs — M-Monitoring')

@section('page-title', 'Utilisateurs')
@section('page-subtitle', 'Dashboard / Gestion des accès')

@section('page-actions')
    @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('users.create') }}"
           class="flex items-center gap-2 bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Inviter
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

{{-- Tableau utilisateurs --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Membres de l'équipe</h3>
        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $stats['total'] }} utilisateur(s)</span>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Utilisateur</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Email</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Rôle</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Dernière connexion</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Statut</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            @php
                $roleBadge = [
                    'super_admin' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
                    'admin'       => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                    'technician'  => 'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400',
                    'observer'    => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
                ];
                $roleLabel = [
                    'super_admin' => 'Super Admin',
                    'admin'       => 'Administrateur',
                    'technician'  => 'Technicien',
                    'observer'    => 'Observateur',
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
                        <p class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">(vous)</span>
                            @endif
                        </p>
                    </div>
                </td>

                {{-- Email --}}
                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->email }}
                </td>

                {{-- Rôle --}}
                <td class="px-5 py-4">
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $rb }}">
                        {{ $rl }}
                    </span>
                </td>

                {{-- Dernière connexion --}}
                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Jamais' }}
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

                {{-- Actions --}}
                <td class="px-5 py-4">
                    <div class="flex items-center justify-end gap-2">
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('users.edit', $user) }}"
                               class="px-3 py-1.5 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded text-xs transition">
                                Modifier
                            </a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.reset-password', $user) }}"
                                      onsubmit="return confirm('Réinitialiser le mot de passe de {{ $user->name }} ?')">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 border border-orange-200 dark:border-orange-900/50 text-orange-500 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded text-xs font-medium transition">
                                        Réinitialiser
                                    </button>
                                </form>
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
                <td colspan="6" class="text-center text-gray-400 dark:text-gray-500 py-12">
                    Aucun utilisateur trouvé.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Matrice des permissions — Super Admin uniquement --}}
@if(auth()->user()->isSuperAdmin())
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Matrice des permissions</h3>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Droits d'accès par rôle dans M-Monitoring</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide w-2/5">Permission</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-purple-500 dark:text-purple-400 uppercase tracking-wide">Super Admin</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-blue-500 dark:text-blue-400 uppercase tracking-wide">Administrateur</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-teal-500 dark:text-teal-400 uppercase tracking-wide">Technicien</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Observateur</th>
                </tr>
            </thead>
            <tbody>
                @php
                $permissions = [
                    // Consultation
                    ['section' => true, 'label' => 'Consultation'],
                    ['label' => 'Voir le dashboard',                    'super' => true,  'admin' => true,  'tech' => true,  'obs' => true],
                    ['label' => 'Voir les détails d\'une application',  'super' => true,  'admin' => true,  'tech' => true,  'obs' => true],
                    ['label' => 'Voir l\'historique des checks',        'super' => true,  'admin' => true,  'tech' => true,  'obs' => true],
                    ['label' => 'Voir les incidents',                   'super' => true,  'admin' => true,  'tech' => true,  'obs' => true],
                    ['label' => 'Générer des rapports SLA',             'super' => true,  'admin' => true,  'tech' => true,  'obs' => true],

                    // Applications
                    ['section' => true, 'label' => 'Applications'],
                    ['label' => 'Ajouter / modifier une application',   'super' => true,  'admin' => true,  'tech' => false, 'obs' => false],
                    ['label' => 'Supprimer une application',            'super' => true,  'admin' => true,  'tech' => false, 'obs' => false],

                    // Incidents & Alertes
                    ['section' => true, 'label' => 'Incidents & Alertes'],
                    ['label' => 'Acquitter un incident',                'super' => true,  'admin' => true,  'tech' => true,  'obs' => false],
                    ['label' => 'Voir l\'historique des alertes',       'super' => true,  'admin' => true,  'tech' => true,  'obs' => false],

                    // Maintenances
                    ['section' => true, 'label' => 'Maintenances'],
                    ['label' => 'Planifier une maintenance',            'super' => true,  'admin' => true,  'tech' => false, 'obs' => false],
                    ['label' => 'Terminer / annuler une maintenance',   'super' => true,  'admin' => true,  'tech' => false, 'obs' => false],

                    // Administration
                    ['section' => true, 'label' => 'Administration'],
                    ['label' => 'Gérer les utilisateurs',               'super' => true,  'admin' => false, 'tech' => false, 'obs' => false],
                    ['label' => 'Réinitialiser un mot de passe',        'super' => true,  'admin' => false, 'tech' => false, 'obs' => false],
                    ['label' => 'Configurer SMTP',                      'super' => true,  'admin' => false, 'tech' => false, 'obs' => false],
                    ['label' => 'Configurer les escalades',             'super' => true,  'admin' => false, 'tech' => false, 'obs' => false],
                    ['label' => 'Voir les audit logs',                  'super' => true,  'admin' => false, 'tech' => false, 'obs' => false],
                    ['label' => 'Paramètres de surveillance globaux',   'super' => true,  'admin' => false, 'tech' => false, 'obs' => false],
                ];
                @endphp

                @foreach($permissions as $perm)
                    @if(isset($perm['section']) && $perm['section'])
                        {{-- Ligne de section --}}
                        <tr class="bg-gray-50 dark:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td colspan="5" class="px-5 py-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $perm['label'] }}
                            </td>
                        </tr>
                    @else
                        <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                            <td class="px-5 py-3 text-gray-700 dark:text-gray-300">{{ $perm['label'] }}</td>
                            <td class="px-5 py-3 text-center">
                                @if($perm['super'])
                                    <span class="text-green-500 dark:text-green-400 font-bold">✓</span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">✗</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($perm['admin'])
                                    <span class="text-green-500 dark:text-green-400 font-bold">✓</span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">✗</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($perm['tech'])
                                    <span class="text-green-500 dark:text-green-400 font-bold">✓</span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">✗</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($perm['obs'])
                                    <span class="text-green-500 dark:text-green-400 font-bold">✓</span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">✗</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection