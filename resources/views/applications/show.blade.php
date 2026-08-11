@extends('layouts.app')

@section('title', $application->name . ' — M-Monitoring')

@section('page-title', $application->name)
@section('page-subtitle', 'Dashboard / Applications / ' . $application->name)

@section('page-actions')
    <a href="#"
       class="flex items-center gap-2 border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Maintenance
    </a>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('applications.edit', $application) }}"
       class="flex items-center gap-2 border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Modifier
    </a>
    <button onclick="confirmDelete('{{ route('applications.destroy', $application) }}', '{{ $application->name }}')"
        class="flex items-center gap-2 border border-red-300 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 px-4 py-2 rounded-lg text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Supprimer
    </button>
    @endif
@endsection

@section('content')

@php
    // Calculs métriques
    $checks24h = $application->checks()->where('checked_at', '>=', now()->subHours(24))->get();
    $checks30d = $application->checks()->where('checked_at', '>=', now()->subDays(30))->get();

    $total24h  = $checks24h->count();
    $up24h     = $checks24h->where('status', 'UP')->count();
    $uptime24h = $total24h > 0 ? round(($up24h / $total24h) * 100, 1) : 0;

    $total30d  = $checks30d->count();
    $up30d     = $checks30d->where('status', 'UP')->count();
    $uptime30d = $total30d > 0 ? round(($up30d / $total30d) * 100, 1) : 0;

    $latencies    = $checks24h->whereNotNull('response_time_ms')->pluck('response_time_ms');
    $latencyAvg   = $latencies->count() > 0 ? round($latencies->avg()) : null;
    $latencyMin   = $latencies->count() > 0 ? $latencies->min() : null;
    $latencyMax   = $latencies->count() > 0 ? $latencies->max() : null;

    $latestCheck  = $application->checks()->latest('checked_at')->first();
    $sslDays      = $latestCheck?->ssl_days_remaining;

    $incidents24h = $application->incidents()->where('started_at', '>=', now()->subHours(24))->count();

    $statusColor = [
        'UP'          => 'bg-green-500',
        'SLOW'        => 'bg-orange-400',
        'DOWN'        => 'bg-red-500',
        'MAINTENANCE' => 'bg-yellow-400',
        'UNKNOWN'     => 'bg-gray-400',
    ];
    $statusDot = $statusColor[$application->current_status] ?? 'bg-gray-400';
@endphp

{{-- Badge statut + retour --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('applications.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour
    </a>
    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold
        {{ $application->current_status === 'UP' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
           ($application->current_status === 'DOWN' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' :
           ($application->current_status === 'SLOW' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400' :
           'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400')) }}">
        <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }} {{ $application->current_status === 'UP' ? 'animate-pulse' : '' }}"></span>
        {{ $application->current_status }}
        @if($latencyAvg) — {{ $latencyAvg }} ms @endif
    </span>
</div>

{{-- Incident en cours --}}
@if($openIncident)
<div class="flex items-center justify-between bg-red-600/10 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg px-4 py-3 mb-6">
    <div class="flex items-center gap-2 text-sm text-red-700 dark:text-red-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Incident en cours depuis <strong class="ml-1">{{ $openIncident->started_at->diffForHumans() }}</strong>
        — Cause : {{ $openIncident->root_cause ?? 'inconnue' }}
    </div>
    <span class="text-xs bg-red-500 text-white px-2.5 py-1 rounded-full font-semibold">ACTIF</span>
</div>
@endif

{{-- 4 cartes métriques --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Uptime 24H</p>
        <p class="text-3xl font-bold {{ $uptime24h >= 99 ? 'text-teal-500 dark:text-teal-400' : ($uptime24h >= 95 ? 'text-orange-500' : 'text-red-500') }}">
            {{ $uptime24h }}%
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $incidents24h }} incident(s)</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Uptime 30J</p>
        <p class="text-3xl font-bold {{ $uptime30d >= 99 ? 'text-teal-500 dark:text-teal-400' : ($uptime30d >= 95 ? 'text-orange-500' : 'text-red-500') }}">
            {{ $uptime30d }}%
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            {{ $uptime30d >= 99.9 ? 'Conforme SLA' : ($uptime30d >= 99 ? 'Bon niveau' : 'En dessous du SLA') }}
        </p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Latence moy.</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ $latencyAvg ? $latencyAvg.' ms' : '—' }}
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">7 derniers jours</p>
    </div>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">SSL expire dans</p>
        <p class="text-3xl font-bold {{ $sslDays === null ? 'text-gray-400' : ($sslDays < 7 ? 'text-red-500' : ($sslDays < 30 ? 'text-orange-500' : 'text-teal-500 dark:text-teal-400')) }}">
            {{ $sslDays !== null ? $sslDays.'j' : '—' }}
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            {{ $sslDays === null ? 'Non vérifié' : ($sslDays < 7 ? 'Critique !' : ($sslDays < 30 ? 'Renouvellement urgent' : 'Renouvellement OK')) }}
        </p>
    </div>
</div>

{{-- Graphique latence + Configuration --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Graphique latence 24h --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 dark:text-gray-100">Latence (24 dernières heures)</h3>
            <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-2 py-1 rounded">24h</span>
        </div>

        @php
            // Grouper les checks par heure pour le graphique
            $chartData = [];
            for ($h = 23; $h >= 0; $h--) {
                $start = now()->subHours($h + 1);
                $end   = now()->subHours($h);
                $hourChecks = $checks24h->filter(fn($c) => $c->checked_at >= $start && $c->checked_at < $end);
                $avgMs = $hourChecks->whereNotNull('response_time_ms')->avg('response_time_ms');
                $hasDown = $hourChecks->whereIn('status', ['DOWN', 'ERROR'])->count() > 0;
                $hasSlow = $hourChecks->where('status', 'SLOW')->count() > 0;
                $chartData[] = [
                    'hour'   => $end->format('H:i'),
                    'avg'    => $avgMs ? round($avgMs) : 0,
                    'status' => $hasDown ? 'down' : ($hasSlow ? 'slow' : 'up'),
                ];
            }
            $maxMs = max(array_column($chartData, 'avg') ?: [1]);
        @endphp

        <div class="flex items-end gap-0.5 h-32 mb-2">
            @foreach($chartData as $bar)
                @php
                    $height = $maxMs > 0 && $bar['avg'] > 0 ? max(4, round(($bar['avg'] / $maxMs) * 100)) : 4;
                    $color  = $bar['status'] === 'down' ? 'bg-red-500' : ($bar['status'] === 'slow' ? 'bg-orange-400' : 'bg-teal-500');
                @endphp
                <div class="flex-1 flex flex-col items-center justify-end h-full group relative">
                    <div class="{{ $color }} rounded-sm w-full transition-all hover:opacity-80"
                         style="height: {{ $height }}%"
                         title="{{ $bar['hour'] }} — {{ $bar['avg'] }}ms">
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Labels heures --}}
        <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 mb-3">
            <span>00:00</span>
            <span>06:00</span>
            <span>12:00</span>
            <span>18:00</span>
            <span>Maint.</span>
        </div>

        @if($latencyMin !== null)
        <div class="flex items-center gap-4 text-xs">
            <span>Min: <span class="text-teal-500 dark:text-teal-400 font-semibold">{{ $latencyMin }}ms</span></span>
            <span>Moy: <span class="text-gray-700 dark:text-gray-300 font-semibold">{{ $latencyAvg }}ms</span></span>
            <span>Max: <span class="text-red-500 dark:text-red-400 font-semibold">{{ $latencyMax }}ms</span></span>
        </div>
        @endif
    </div>

    {{-- Configuration --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Configuration de surveillance</h3>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">URL</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $application->url }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">Méthode</p>
                <span class="inline-block text-xs font-bold px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded">
                    {{ $application->method }}
                </span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">Fréquence</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Toutes les {{ $application->check_interval_seconds }}s</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">Retries</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $application->retry_count }} tentative(s)</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">Seuil WARN</p>
                <p class="text-sm font-semibold text-orange-500 dark:text-orange-400">{{ $application->latency_warn_ms }} ms</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">Seuil DOWN</p>
                <p class="text-sm font-semibold text-red-500 dark:text-red-400">{{ $application->latency_down_ms }} ms</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">Vérif. SSL</p>
                <p class="text-sm font-semibold {{ $application->ssl_check ? 'text-teal-500 dark:text-teal-400' : 'text-gray-400' }}">
                    {{ $application->ssl_check ? '✓ Activé ('.$application->ssl_alert_days.'j)' : '✗ Désactivé' }}
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">Vérif. Auth</p>
                <p class="text-sm font-semibold {{ $application->auth_enabled ? 'text-teal-500 dark:text-teal-400' : 'text-gray-400' }}">
                    {{ $application->auth_enabled ? '✓ '.strtoupper($application->auth_type) : '✗ Désactivée' }}
                </p>
            </div>
            @if($application->keyword_expected)
            <div class="col-span-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">Keyword attendu</p>
                <p class="text-sm font-mono text-teal-600 dark:text-teal-400">"{{ $application->keyword_expected }}"</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Historique des checks --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 mb-24">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
            Historique des checks (dernières 24h)
        </h3>
        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $total24h }} vérification(s)</span>
    </div>

    {{-- En-têtes --}}
    <div class="grid grid-cols-8 gap-2 py-2 border-b border-gray-100 dark:border-gray-800 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
        <span>Heure</span>
        <span>Statut</span>
        <span>Code HTTP</span>
        <span>Latence</span>
        <span>Keyword</span>
        <span>Auth</span>
        <span>SSL</span>
        <span>Erreur</span>
    </div>

    @forelse($recentChecks as $check)
    @php
        $statusBadge = [
            'UP'    => 'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400',
            'SLOW'  => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
            'DOWN'  => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
            'ERROR' => 'bg-red-200 dark:bg-red-900/50 text-red-800 dark:text-red-300',
        ];
        $rowBg = [
            'UP'    => '',
            'SLOW'  => 'bg-orange-50/50 dark:bg-orange-900/5',
            'DOWN'  => 'bg-red-50/50 dark:bg-red-900/5',
            'ERROR' => 'bg-red-100/50 dark:bg-red-900/10',
        ];
        $sb = $statusBadge[$check->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-500';
        $rb = $rowBg[$check->status] ?? '';
    @endphp
    <div class="grid grid-cols-8 gap-2 py-3 border-b border-gray-100 dark:border-gray-800 last:border-0 text-sm items-center {{ $rb }} rounded px-1">
        <span class="text-gray-500 dark:text-gray-400 font-mono text-xs">
            {{ $check->checked_at->format('H:i:s') }}
        </span>
        <span>
            <span class="inline-block px-2 py-0.5 rounded text-xs font-bold {{ $sb }}">
                {{ $check->status }}
            </span>
        </span>
        <span class="text-gray-600 dark:text-gray-400">{{ $check->http_code ?? '—' }}</span>
        <span class="font-mono {{ $check->response_time_ms >= $application->latency_down_ms ? 'text-red-500 dark:text-red-400' : ($check->response_time_ms >= $application->latency_warn_ms ? 'text-orange-500 dark:text-orange-400' : 'text-gray-600 dark:text-gray-400') }}">
            {{ $check->response_time_ms ? $check->response_time_ms.' ms' : '—' }}
        </span>
        <span class="text-center">
            @if($check->keyword_ok === null)<span class="text-gray-300 dark:text-gray-600">—</span>
            @elseif($check->keyword_ok)<span class="text-teal-500 dark:text-teal-400">✓</span>
            @else<span class="text-red-500 dark:text-red-400">✗</span>
            @endif
        </span>
        <span class="text-center">
            @if($check->auth_ok === null)<span class="text-gray-300 dark:text-gray-600">—</span>
            @elseif($check->auth_ok)<span class="text-teal-500 dark:text-teal-400">✓</span>
            @else<span class="text-red-500 dark:text-red-400">✗</span>
            @endif
        </span>
        <span class="{{ $check->ssl_days_remaining !== null && $check->ssl_days_remaining < 7 ? 'text-red-500 dark:text-red-400 font-semibold' : ($check->ssl_days_remaining !== null && $check->ssl_days_remaining < 30 ? 'text-orange-500 dark:text-orange-400' : 'text-gray-600 dark:text-gray-400') }}">
            {{ $check->ssl_days_remaining !== null ? $check->ssl_days_remaining.'j' : '—' }}
        </span>
        <span class="text-red-400 dark:text-red-400/80 text-xs truncate">
            {{ $check->error_message ?? '—' }}
        </span>
    </div>
    @empty
    <div class="text-center text-gray-400 dark:text-gray-500 py-12 text-sm">
        Aucun check effectué pour l'instant.
    </div>
    @endforelse
</div>

{{-- Modal suppression --}}
<div id="delete-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9998;align-items:center;justify-content:center;">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800" style="border-radius:12px;padding:32px;max-width:420px;width:90%;">
        <div style="font-size:48px;text-align:center;margin-bottom:16px;">🗑️</div>
        <h3 class="text-gray-900 dark:text-white" style="font-size:18px;font-weight:600;text-align:center;margin-bottom:8px;">Confirmer la suppression</h3>
        <p class="text-gray-500 dark:text-gray-400" style="font-size:14px;text-align:center;margin-bottom:24px;">
            Supprimer <strong id="delete-app-name" class="text-gray-700 dark:text-gray-200"></strong> ? Cette action est irréversible.
        </p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <button onclick="closeDeleteModal()" class="text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800" style="padding:10px 24px;border-radius:8px;cursor:pointer;font-size:14px;">
                Annuler
            </button>
            <form id="delete-form" method="POST">
                @csrf @method('DELETE')
                <button type="submit" style="padding:10px 24px;border:none;border-radius:8px;background:#ef4444;color:#fff;cursor:pointer;font-size:14px;font-weight:600;">
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

@endsection