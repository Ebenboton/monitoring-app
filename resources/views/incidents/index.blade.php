@extends('layouts.app')

@section('title', 'Incidents — M-Monitoring')

@section('page-title', 'Incidents')
@section('page-subtitle', 'Dashboard / Incidents')

@section('page-actions')
<button onclick="window.location.reload()"
    class="flex items-center justify-center border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 w-9 h-9 rounded-lg transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
    </svg>
</button>
@endsection

@section('content')

{{-- 4 compteurs --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Total</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tous les incidents</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Actifs</p>
        <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['open'] }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">En cours</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Résolus</p>
        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['resolved'] }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Incidents clôturés</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Non acquittés</p>
        <p class="text-3xl font-bold text-orange-500 dark:text-orange-400">{{ $stats['unacknowledged'] }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">En attente</p>
    </div>
</div>

{{-- Filtres --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('incidents.index') }}" class="flex flex-wrap gap-3 items-center">
        <select name="status" onchange="this.form.submit()"
            class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-teal-400">
            <option value="">Tous les statuts</option>
            <option value="open" @selected(request('status')==='open' )>Actifs</option>
            <option value="resolved" @selected(request('status')==='resolved' )>Résolus</option>
        </select>
        <select name="application_id" onchange="this.form.submit()"
            class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-teal-400">
            <option value="">Toutes les applications</option>
            @foreach($applications as $app)
            <option value="{{ $app->id }}" @selected(request('application_id')===$app->id)>{{ $app->name }}</option>
            @endforeach
        </select>
        @if(request('status') || request('application_id'))
        <a href="{{ route('incidents.index') }}"
            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            Réinitialiser
        </a>
        @endif
    </form>
</div>

{{-- Tableau des incidents --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Liste des incidents</h3>
        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $incidents->total() }} incident(s)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Application</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Début</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Durée</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Cause</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Alertes</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Acquitté par</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents as $incident)
                @php
                // Durée
                if ($incident->is_resolved && $incident->duration_seconds) {
                $duration = sprintf('%02dh %02dm', floor($incident->duration_seconds/3600), floor(($incident->duration_seconds%3600)/60));
                $durationColor = 'text-gray-600 dark:text-gray-400';
                } else {
                $duration = $incident->started_at->diffForHumans(null, true);
                $durationColor = 'text-red-600 dark:text-red-400 font-semibold';
                }

                // Badge cause
                $causeBadge = [
                'http_error' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                'timeout' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                'keyword' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
                'auth' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
                'ssl' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                ];
                $cb = $causeBadge[$incident->root_cause] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-500';

                // Nombre d'alertes envoyées
                $alertCount = $incident->alerts->where('status', 'sent')->count();
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition {{ $incident->is_resolved ? 'opacity-70' : '' }}">

                    {{-- Application --}}
                    <td class="px-5 py-4">
                        <a href="{{ route('applications.show', $incident->application) }}"
                            class="font-semibold text-gray-800 dark:text-gray-100 hover:text-teal-600 dark:hover:text-teal-400">
                            {{ $incident->application->name }}
                        </a>
                        @if($incident->application->group_name)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $incident->application->group_name }}</p>
                        @endif
                    </td>

                    {{-- Statut --}}
                    <td class="px-5 py-4">
                        @if($incident->is_resolved)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                            RÉSOLU
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                            ACTIF
                        </span>
                        @endif
                    </td>

                    {{-- Début --}}
                    <td class="px-5 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">
                        <div>{{ $incident->started_at->format('d/m') }}</div>
                        <div class="text-xs">{{ $incident->started_at->format('H:i') }}</div>
                    </td>

                    {{-- Durée --}}
                    <td class="px-5 py-4 text-sm font-mono {{ $durationColor }}">
                        {{ $duration }}
                    </td>

                    {{-- Cause --}}
                    <td class="px-5 py-4">
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $cb }}">
                            {{ str_replace('_', ' ', $incident->root_cause ?? 'inconnue') }}
                        </span>
                    </td>

                    {{-- Alertes --}}
                    <td class="px-5 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">
                        {{ $alertCount }}
                    </td>

                    {{-- Acquitté par --}}
                    <td class="px-5 py-4 text-sm">
                        @if($incident->acknowledged_by)
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                            {{ $incident->acknowledgedBy->name }}
                        </span>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $incident->acknowledged_at->format('H:i') }}</p>
                        @else
                        <span class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            @if(!$incident->is_resolved && !$incident->acknowledged_by)
                            <form method="POST" action="{{ route('incidents.acknowledge', $incident) }}">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 bg-teal-500/10 dark:bg-teal-500/20 text-teal-600 dark:text-teal-400 border border-teal-300 dark:border-teal-700 hover:bg-teal-500/20 rounded text-xs font-medium transition">
                                    Acquitter
                                </button>
                            </form>
                            @elseif(!$incident->is_resolved && $incident->acknowledged_by)
                            <span class="px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded text-xs">
                                Pris en charge
                            </span>
                            @else
                            <a href="{{ route('applications.show', $incident->application) }}"
                                class="px-3 py-1.5 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded text-xs transition">
                                Détail
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-gray-400 dark:text-gray-500 py-12">
                        Aucun incident trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($incidents->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-400 dark:text-gray-500">
            {{ $incidents->firstItem() }}–{{ $incidents->lastItem() }} sur {{ $incidents->total() }}
        </p>
        {{ $incidents->links() }}
    </div>
    @endif
</div>

{{-- Timeline incident actif --}}
@php
$activeIncident = $incidents->where('is_resolved', false)->first();
@endphp

@if($activeIncident)
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-5">
        Chronologie — {{ $activeIncident->application->name }}
        <span class="ml-2 text-xs font-normal text-red-500 dark:text-red-400">(incident actif)</span>
    </h3>

    <div class="space-y-0">
        {{-- Panne détectée --}}
        <div class="flex gap-4 pb-6 relative">
            <div class="relative flex-shrink-0">
                <div class="w-5 h-5 rounded-full bg-red-100 dark:bg-red-900/30 border-2 border-red-500 flex items-center justify-center">
                    <svg class="w-2.5 h-2.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="absolute left-2.5 top-5 bottom-0 w-px bg-gray-200 dark:bg-gray-700"></div>
            </div>
            <div class="pt-0.5">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                    {{ $activeIncident->started_at->format('H:i:s') }} — Panne détectée
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    Cause : {{ str_replace('_', ' ', $activeIncident->root_cause ?? 'inconnue') }} —
                    Incident créé automatiquement
                </p>
            </div>
        </div>

        {{-- Alertes envoyées --}}
        @foreach($activeIncident->alerts->where('status', 'sent')->take(3) as $alert)
        <div class="flex gap-4 pb-6 relative">
            <div class="relative flex-shrink-0">
                <div class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-900/30 border-2 border-orange-400 flex items-center justify-center">
                    <svg class="w-2.5 h-2.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" />
                    </svg>
                </div>
                <div class="absolute left-2.5 top-5 bottom-0 w-px bg-gray-200 dark:bg-gray-700"></div>
            </div>
            <div class="pt-0.5">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                    {{ $alert->sent_at?->format('H:i:s') }} —
                    Email {{ $alert->alert_type === 'escalation' ? 'escalade' : 'd\'alerte' }} envoyé
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    Destinataire : {{ $alert->recipient_email }} —
                    Niveau {{ $alert->escalation_level }}
                </p>
            </div>
        </div>
        @endforeach

        {{-- En cours --}}
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <div class="w-5 h-5 rounded-full border-2 border-dashed border-red-500 flex items-center justify-center">
                    <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                </div>
            </div>
            <div class="pt-0.5">
                <p class="text-sm font-semibold text-red-600 dark:text-red-400">
                    En cours — En attente de résolution
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    Checks toutes les {{ $activeIncident->application->check_interval_seconds }}s —
                    Depuis {{ $activeIncident->started_at->diffForHumans() }}
                </p>
            </div>
        </div>
    </div>
</div>
@endif

@endsection