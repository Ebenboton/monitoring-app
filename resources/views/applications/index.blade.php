@extends('layouts.app')

@section('title', 'Dashboard — M-Monitoring')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'WatchTower / Vue d\'ensemble')

@section('page-actions')
<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-500/10 dark:bg-green-500/20 text-green-600 dark:text-green-400 text-xs font-semibold rounded-full border border-green-200 dark:border-green-800">
    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
    EN DIRECT
</span>

<div class="relative">
    <input type="text" placeholder="Rechercher une application..."
        class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2 text-sm w-56 focus:outline-none focus:ring-1 focus:ring-blue-400 placeholder-gray-400 dark:placeholder-gray-500">
</div>

{{-- Bouton rafraîchir --}}
<button onclick="window.location.reload()" title="Actualiser les checks"
    class="flex items-center justify-center border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 w-9 h-9 rounded-lg transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
    </svg>
</button>

{{-- Bouton notifications --}}
<button title="Alertes actives"
    class="relative flex items-center justify-center border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 w-9 h-9 rounded-lg transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
    </svg>
    @if($stats['down'] > 0)
    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
        {{ $stats['down'] }}
    </span>
    @endif
</button>

{{-- Bouton nouvelle application --}}
@if(auth()->user()->isAdmin())
<a href="{{ route('applications.create') }}"
    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
    Ajouter
</a>
@endif
@endsection


@section('content')

@php
$downApps = $applications->where('current_status', 'DOWN');
$openIncidents = \App\Models\Incident::with('application')
->where('is_resolved', false)
->latest('started_at')
->take(5)
->get();
@endphp

{{-- Bannière incident actif --}}
@if($downApps->count() > 0)
@php $firstDown = $downApps->first(); @endphp
<div class="flex items-center justify-between bg-red-600/10 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg px-4 py-3 mb-6">
    <div class="flex items-center gap-2 text-sm text-red-700 dark:text-red-400">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.072 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <strong>{{ $firstDown->name }}</strong> est DOWN
        @if($firstDown->last_checked_at)
        depuis {{ $firstDown->last_checked_at->diffForHumans() }}
        @endif
        — <a href="{{ route('applications.show', $firstDown) }}" class="underline hover:no-underline">Voir l'incident →</a>
    </div>
    <button class="text-xs text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700 px-3 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/40 transition">
        Acquitter
    </button>
</div>
@endif

{{-- Compteurs globaux --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Total Apps</p>
                <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $groups->count() }} groupe(s)</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Opérationnelles</p>
                <p class="text-4xl font-bold text-green-600 dark:text-green-400">{{ $stats['up'] }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    {{ $stats['total'] > 0 ? round(($stats['up'] / $stats['total']) * 100) : 0 }}% du parc
                </p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Dégradées</p>
                <p class="text-4xl font-bold text-orange-500 dark:text-orange-400">{{ $stats['slow'] }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Latence élevée</p>
            </div>
            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">En panne</p>
                <p class="text-4xl font-bold text-red-600 dark:text-red-400">{{ $stats['down'] }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Alertes envoyées</p>
            </div>
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Titre section + filtres --}}
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Applications surveillées</h2>
    <div class="flex items-center gap-2">
        <form method="GET" action="{{ route('applications.index') }}" class="flex items-center gap-2">
            <select name="group" onchange="this.form.submit()"
                class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                <option value="">Tous les groupes</option>
                @foreach($groups as $group)
                <option value="{{ $group }}" @selected(request('group')===$group)>{{ $group }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()"
                class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                <option value="">Tous les statuts</option>
                @foreach(['UP','SLOW','DOWN','MAINTENANCE','PAUSED','UNKNOWN'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Grille de cards applications --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
    @forelse($applications as $app)
    @php
    $latestCheck = $app->checks()->latest('checked_at')->first();

    $statusBorder = [
    'UP' => 'border-green-400 dark:border-green-600',
    'SLOW' => 'border-orange-400 dark:border-orange-500',
    'DOWN' => 'border-red-500 dark:border-red-600',
    'MAINTENANCE' => 'border-yellow-400 dark:border-yellow-500',
    'UNKNOWN' => 'border-gray-300 dark:border-gray-700',
    'PAUSED' => 'border-gray-300 dark:border-gray-700',
    ];
    $statusBadge = [
    'UP' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
    'SLOW' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
    'DOWN' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
    'MAINTENANCE' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
    'UNKNOWN' => 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400',
    'PAUSED' => 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400',
    ];
    $dotColor = [
    'UP' => 'bg-green-500',
    'SLOW' => 'bg-orange-400',
    'DOWN' => 'bg-red-500',
    'MAINTENANCE' => 'bg-yellow-400',
    'UNKNOWN' => 'bg-gray-400',
    'PAUSED' => 'bg-gray-400',
    ];
    $barColors = [
    'UP' => 'bg-green-500',
    'SLOW' => 'bg-orange-400',
    'DOWN' => 'bg-red-500',
    'MAINTENANCE' => 'bg-yellow-400',
    'UNKNOWN' => 'bg-gray-400',
    'PAUSED' => 'bg-gray-400',
    ];

    $border = $statusBorder[$app->current_status] ?? 'border-gray-300 dark:border-gray-700';
    $badge = $statusBadge[$app->current_status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-500';
    $dot = $dotColor[$app->current_status] ?? 'bg-gray-400';
    $bar = $barColors[$app->current_status] ?? 'bg-gray-400';

    // Uptime 30j simulé — sera calculé dynamiquement plus tard
    $totalChecks = $app->checks()->where('checked_at', '>=', now()->subDays(30))->count();
    $upChecks = $app->checks()->where('checked_at', '>=', now()->subDays(30))->where('status', 'UP')->count();
    $uptime = $totalChecks > 0 ? round(($upChecks / $totalChecks) * 100, 1) : 0;

    $openIncident = $app->incidents()->where('is_resolved', false)->latest('started_at')->first();
    @endphp

    <div class="bg-white dark:bg-gray-900 border-l-4 {{ $border }} border border-gray-200 dark:border-gray-800 rounded-xl p-5 hover:shadow-md dark:hover:shadow-gray-900 transition flex flex-col gap-4">

        {{-- Header card --}}
        <div class="flex items-start justify-between">
            <div class="min-w-0 flex-1">
                <a href="{{ route('applications.show', $app) }}" class="font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate block">
                    {{ $app->name }}
                </a>
                <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">{{ $app->url }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ml-3 flex-shrink-0 {{ $badge }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                {{ $app->current_status }}
            </span>
        </div>

        {{-- Métriques --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Latence</p>
                <p class="text-sm font-semibold {{ $latestCheck && $latestCheck->response_time_ms ? ($latestCheck->response_time_ms >= $app->latency_down_ms ? 'text-red-600 dark:text-red-400' : ($latestCheck->response_time_ms >= $app->latency_warn_ms ? 'text-orange-500 dark:text-orange-400' : 'text-green-600 dark:text-green-400')) : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $latestCheck && $latestCheck->response_time_ms ? $latestCheck->response_time_ms.' ms' : 'Timeout' }}
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Uptime 30j</p>
                <p class="text-sm font-semibold {{ $uptime >= 99 ? 'text-green-600 dark:text-green-400' : ($uptime >= 95 ? 'text-orange-500 dark:text-orange-400' : 'text-red-600 dark:text-red-400') }}">
                    {{ $uptime }}%
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">SSL</p>
                <p class="text-sm font-semibold {{ $latestCheck && $latestCheck->ssl_days_remaining !== null ? ($latestCheck->ssl_days_remaining < 7 ? 'text-red-600 dark:text-red-400' : ($latestCheck->ssl_days_remaining < 30 ? 'text-orange-500 dark:text-orange-400' : 'text-gray-700 dark:text-gray-300')) : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $latestCheck && $latestCheck->ssl_days_remaining !== null ? $latestCheck->ssl_days_remaining.'j' : '—' }}
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Auth</p>
                <p class="text-sm font-semibold">
                    @if($latestCheck && $latestCheck->auth_ok !== null)
                    @if($latestCheck->auth_ok)
                    <span class="text-green-600 dark:text-green-400">✓ OK</span>
                    @else
                    <span class="text-red-600 dark:text-red-400">✗ Erreur</span>
                    @endif
                    @elseif(!$app->auth_enabled)
                    <span class="text-gray-400 dark:text-gray-500">— Suspendu</span>
                    @else
                    <span class="text-gray-400 dark:text-gray-500">—</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Footer card --}}
        <div class="flex items-center justify-between pt-1">
            <span class="text-xs text-gray-400 dark:text-gray-500">
                @if($openIncident)
                <span class="text-red-500 dark:text-red-400 font-medium">
                    DOWN depuis {{ $openIncident->started_at->diffForHumans(null, true) }}
                </span>
                @elseif($app->last_checked_at)
                Il y a {{ $app->last_checked_at->diffForHumans(null, true) }}
                @else
                Jamais vérifié
                @endif
            </span>
            {{-- Mini barre de statut --}}
            <div class="flex items-center gap-0.5">
                @for($i = 0; $i < 12; $i++)
                    <div class="w-1.5 h-4 rounded-sm {{ $i < 10 ? $bar : 'bg-gray-200 dark:bg-gray-700' }} opacity-{{ $i < 10 ? '100' : '40' }}">
            </div>
            @endfor
        </div>
    </div>

    {{-- Actions --}}
    @if(auth()->user()->isAdmin())
    <div class="flex items-center gap-2 pt-1 border-t border-gray-100 dark:border-gray-800">
        <a href="{{ route('applications.show', $app) }}"
            class="flex-1 text-center py-1.5 text-xs text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
            Détails
        </a>
        <a href="{{ route('applications.edit', $app) }}"
            class="flex-1 text-center py-1.5 text-xs text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
            Modifier
        </a>
        <button onclick="confirmDelete('{{ route('applications.destroy', $app) }}', '{{ $app->name }}')"
            class="flex-1 text-center py-1.5 text-xs text-red-400 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 transition">
            Supprimer
        </button>
    </div>
    @endif
</div>
@empty
<div class="col-span-3 text-center text-gray-400 dark:text-gray-500 py-16">
    Aucune application surveillée pour l'instant.
    <a href="{{ route('applications.create') }}" class="text-blue-500 dark:text-blue-400 hover:underline ml-1">
        Ajouter la première.
    </a>
</div>
@endforelse
</div>

{{-- Section bas : Incidents + Disponibilité --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-24">

    {{-- Incidents actifs --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 dark:text-gray-100">Incidents actifs et récents</h3>
            <a href="#" class="text-xs text-blue-500 dark:text-blue-400 hover:underline">Voir tout</a>
        </div>
        @forelse($openIncidents as $incident)
        @php
        $incidentBadge = [
        'http_error' => ['bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', 'ACTIF'],
        'timeout' => ['bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', 'ACTIF'],
        'keyword' => ['bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400', 'SLOW'],
        'auth' => ['bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', 'ACTIF'],
        'ssl' => ['bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400', 'SLOW'],
        ];
        [$ib, $il] = $incidentBadge[$incident->root_cause] ?? ['bg-gray-100 dark:bg-gray-800 text-gray-500', 'ACTIF'];
        @endphp
        <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800 last:border-0">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-10 rounded-full {{ $incident->root_cause === 'keyword' || $incident->root_cause === 'ssl' ? 'bg-orange-400' : 'bg-red-500' }} flex-shrink-0"></div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                            {{ $incident->application->name }} —
                            {{ ucfirst(str_replace('_', ' ', $incident->root_cause ?? 'Erreur')) }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            Depuis {{ $incident->started_at->format('H:i') }} ·
                            {{ $incident->started_at->diffForHumans(null, true) }} ·
                            {{ $incident->alerts->count() }} alerte(s) envoyée(s)
                            @if($incident->acknowledged_by)
                            · Acquitté
                            @else
                            · <span class="text-orange-500 dark:text-orange-400">Non acquitté</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <span class="ml-3 px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0 {{ $ib }}">
                {{ $il }}
            </span>
        </div>
        @empty
        <div class="text-center text-gray-400 dark:text-gray-500 py-8 text-sm">
            Aucun incident actif — tout est opérationnel.
        </div>
        @endforelse
    </div>

    {{-- Disponibilité globale --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 dark:text-gray-100">Disponibilité globale (30j)</h3>
        </div>

        @php
        $globalUptime = 0;
        $count = 0;
        @endphp

        @foreach($applications->take(6) as $app)
        @php
        $total = $app->checks()->where('checked_at', '>=', now()->subDays(30))->count();
        $up = $app->checks()->where('checked_at', '>=', now()->subDays(30))->where('status', 'UP')->count();
        $pct = $total > 0 ? round(($up / $total) * 100, 1) : 0;
        $globalUptime += $pct;
        $count++;
        $barWidth = $pct;
        $barColor = $pct >= 99 ? 'bg-green-500' : ($pct >= 95 ? 'bg-orange-400' : 'bg-red-500');
        $textColor = $pct >= 99 ? 'text-green-600 dark:text-green-400' : ($pct >= 95 ? 'text-orange-500 dark:text-orange-400' : 'text-red-600 dark:text-red-400');
        @endphp
        <div class="mb-3">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $app->name }}</span>
                <span class="text-sm font-semibold {{ $textColor }}">{{ $pct }}%</span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                <div class="h-2 rounded-full {{ $barColor }} transition-all" style="width: {{ $barWidth }}%"></div>
            </div>
        </div>
        @endforeach

        @if($count > 0)
        @php $avg = round($globalUptime / $count, 1); @endphp
        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 text-center">
            <p class="text-4xl font-bold {{ $avg >= 99 ? 'text-green-500 dark:text-green-400' : ($avg >= 95 ? 'text-orange-500 dark:text-orange-400' : 'text-red-500 dark:text-red-400') }}">
                {{ $avg }}%
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Moyenne parc complet</p>
        </div>
        @endif
    </div>
</div>

<!-- {{-- Barre de navigation bottom --}}
<nav class="fixed bottom-0 left-64 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 z-20">
    <div class="flex items-center px-6 py-0 overflow-x-auto">
        <a href="{{ route('applications.index') }}"
           class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 whitespace-nowrap transition
                  {{ request()->routeIs('applications.index') ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('applications.create') }}"
           class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 whitespace-nowrap transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter app
        </a>
        @endif
        <a href="#"
           class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 whitespace-nowrap transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Incidents
            @if($openIncidents->count() > 0)
                <span class="bg-red-500 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center">
                    {{ $openIncidents->count() }}
                </span>
            @endif
        </a>
        <a href="#"
           class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 whitespace-nowrap transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            Alertes
        </a>
        <a href="#"
           class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 whitespace-nowrap transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Rapports
        </a>
        <a href="#"
           class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 whitespace-nowrap transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Maintenances
        </a>
        @if(auth()->user()->isSuperAdmin())
        <a href="#"
           class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 whitespace-nowrap transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Utilisateurs
        </a>
        <a href="#"
           class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 whitespace-nowrap transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Paramètres
        </a>
        @endif
    </div>
</nav> -->



{{-- Modal suppression --}}
<div id="delete-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9998;align-items:center;justify-content:center;">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800" style="border-radius:12px;padding:32px;max-width:420px;width:90%;">
        <div style="font-size:48px;text-align:center;margin-bottom:16px;">🗑️</div>
        <h3 class="text-gray-900 dark:text-white" style="font-size:18px;font-weight:600;text-align:center;margin-bottom:8px;">
            Confirmer la suppression
        </h3>
        <p class="text-gray-500 dark:text-gray-400" style="font-size:14px;text-align:center;margin-bottom:24px;">
            Vous êtes sur le point de supprimer <strong id="delete-app-name" class="text-gray-700 dark:text-gray-200"></strong>.
            Cette action est irréversible.
        </p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <button onclick="closeDeleteModal()"
                class="text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800"
                style="padding:10px 24px;border-radius:8px;cursor:pointer;font-size:14px;">
                Annuler
            </button>
            <form id="delete-form" method="POST">
                @csrf @method('DELETE')
                <button type="submit"
                    style="padding:10px 24px;border:none;border-radius:8px;background:#ef4444;color:#fff;cursor:pointer;font-size:14px;font-weight:600;">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(url, name) {
        document.getElementById('delete-app-name').textContent = '"' + name + '"';
        document.getElementById('delete-form').action = url;
        document.getElementById('delete-modal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').style.display = 'none';
    }
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>

@if(session('refreshing'))
<script>
    // Recharge la page une fois les checks terminés
    setTimeout(() => window.location.href = "{{ route('applications.index') }}", 15000);
</script>
@endif

@endsection