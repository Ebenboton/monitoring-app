@extends('layouts.app')

@section('title', 'Alertes — M-Monitoring')

@section('page-title', 'Alertes')
@section('page-subtitle', 'Dashboard / Historique des alertes email')

@section('content')

{{-- Compteurs --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Total</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Envoyées</p>
        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['sent'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Échouées</p>
        <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['failed'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Escalades</p>
        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['escalation'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">En attente</p>
        <p class="text-3xl font-bold text-orange-500 dark:text-orange-400">{{ $stats['pending'] }}</p>
    </div>
</div>


{{-- Filtres --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('alerts.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Type</label>
            <select name="type" onchange="this.form.submit()"
                class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                <option value="">Tous les types</option>
                <option value="down" @selected(request('type')==='down' )>Panne</option>
                <option value="slow" @selected(request('type')==='slow' )>Dégradation</option>
                <option value="ssl" @selected(request('type')==='ssl' )>SSL</option>
                <option value="auth" @selected(request('type')==='auth' )>Auth</option>
                <option value="recovery" @selected(request('type')==='recovery' )>Rétablissement</option>
                <option value="escalation" @selected(request('type')==='escalation' )>Escalade</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Statut</label>
            <select name="status" onchange="this.form.submit()"
                class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                <option value="">Tous les statuts</option>
                <option value="sent" @selected(request('status')==='sent' )>Envoyée</option>
                <option value="failed" @selected(request('status')==='failed' )>Échouée</option>
                <option value="pending" @selected(request('status')==='pending' )>En attente</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Application</label>
            <select name="application_id" onchange="this.form.submit()"
                class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                <option value="">Toutes</option>
                @foreach($applications as $app)
                <option value="{{ $app->id }}" @selected(request('application_id')===$app->id)>{{ $app->name }}</option>
                @endforeach
            </select>
        </div>
        @if(request('type') || request('status') || request('application_id'))
        <a href="{{ route('alerts.index') }}"
            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 py-2">
            Réinitialiser
        </a>
        @endif
    </form>
</div>

{{-- Tableau --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Historique des alertes</h3>
        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $alerts->total() }} alerte(s)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Type</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Application</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Destinataire</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Niveau</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                @php
                $typeBadge = [
                'down' => ['bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', 'Panne'],
                'slow' => ['bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400', 'Dégradation'],
                'ssl' => ['bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400', 'SSL'],
                'auth' => ['bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400', 'Auth'],
                'recovery' => ['bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', 'Rétabli'],
                'escalation' => ['bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400', 'Escalade'],
                ];
                [$typeClass, $typeLabel] = $typeBadge[$alert->alert_type] ?? ['bg-gray-100 dark:bg-gray-800 text-gray-500', ucfirst($alert->alert_type)];

                $statusBadge = [
                'sent' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                'failed' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                'pending' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
                ];
                $statusLabel = [
                'sent' => 'Envoyée',
                'failed' => 'Échouée',
                'pending' => 'En attente',
                ];
                $sb = $statusBadge[$alert->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-500';
                $sl = $statusLabel[$alert->status] ?? ucfirst($alert->status);
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                    {{-- Date --}}
                    <td class="px-5 py-3 text-xs font-mono text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $alert->created_at->format('d/m/Y H:i:s') }}
                    </td>

                    {{-- Type --}}
                    <td class="px-5 py-3">
                        <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold {{ $typeClass }}">
                            {{ $typeLabel }}
                        </span>
                    </td>

                    {{-- Application --}}
                    <td class="px-5 py-3">
                        @if($alert->application)
                        <a href="{{ route('applications.show', $alert->application) }}"
                            class="font-medium text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                            {{ $alert->application->name }}
                        </a>
                        @else
                        <span class="text-gray-400 dark:text-gray-500">—</span>
                        @endif
                    </td>

                    {{-- Destinataire --}}
                    <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">
                        {{ $alert->recipient_email }}
                    </td>

                    {{-- Niveau --}}
                    <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">
                        Niveau {{ $alert->escalation_level }}
                    </td>

                    {{-- Statut --}}
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $sb }}">
                            @if($alert->status === 'sent')
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            @elseif($alert->status === 'failed')
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            @else
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                            @endif
                            {{ $sl }}
                        </span>
                        @if($alert->status === 'failed' && $alert->error_message)
                        <p class="text-xs text-red-400 mt-1 truncate max-w-xs">{{ $alert->error_message }}</p>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-400 dark:text-gray-500 py-12">
                        Aucune alerte enregistrée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($alerts->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-400 dark:text-gray-500">
            {{ $alerts->firstItem() }}–{{ $alerts->lastItem() }} sur {{ $alerts->total() }}
        </p>
        {{ $alerts->links() }}
    </div>
    @endif
</div>

@endsection