@extends('layouts.app')

@section('title', 'Rapports SLA — M-Monitoring')

@section('page-title', 'Rapports SLA')
@section('page-subtitle', 'Dashboard / Rapports')

@section('page-actions')
{{-- Sélecteur de mois --}}
<form method="GET" action="{{ route('reports.index') }}" id="month-form">
    <select name="month" onchange="document.getElementById('month-form').submit()"
        class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
        @foreach($availableMonths as $month)
        @php
        $label = \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y');
        @endphp
        <option value="{{ $month }}" @selected($selectedMonth===$month)>{{ ucfirst($label) }}</option>
        @endforeach
    </select>
</form>

{{-- Export CSV --}}
<a href="{{ route('reports.export', ['month' => $selectedMonth]) }}"
    class="flex items-center gap-2 border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
    </svg>
    Export CSV
</a>
@endsection


@section('content')

{{-- 4 KPIs --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    {{-- Disponibilité globale --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
            Disponibilité globale
        </p>
        <p class="text-4xl font-bold {{ $globalUptime >= 99 ? 'text-green-500 dark:text-green-400' : ($globalUptime >= 95 ? 'text-orange-500 dark:text-orange-400' : 'text-red-500 dark:text-red-400') }}">
            {{ $globalUptime }}%
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            {{ ucfirst(\Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y')) }}
        </p>
    </div>

    {{-- Total incidents --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
            Total incidents
        </p>
        <p class="text-4xl font-bold text-gray-900 dark:text-white">
            {{ $totalIncidents }}
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            {{ ucfirst(\Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y')) }}
        </p>
    </div>

    {{-- MTTR moyen --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
            MTTR moyen
        </p>
        <p class="text-4xl font-bold text-gray-900 dark:text-white">
            {{ $globalMttr > 0 ? $globalMttr.' min' : '—' }}
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            Mean Time to Resolve
        </p>
    </div>

    {{-- Alertes envoyées --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
            Alertes envoyées
        </p>
        <p class="text-4xl font-bold text-gray-900 dark:text-white">
            {{ $totalAlerts }}
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            @if($failedAlerts > 0)
            <span class="text-red-500 dark:text-red-400">{{ $failedAlerts }} échec(s) d'envoi</span>
            @else
            0 échec d'envoi
            @endif
        </p>
    </div>
</div>

{{-- ═══════════ Graphiques ═══════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Courbe de latence --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-semibold text-gray-800 dark:text-gray-100">Latence moyenne par jour</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    En millisecondes — une courbe par application
                </p>
            </div>
        </div>

        @if(count($latencyDatasets) > 0)
            <div class="relative" style="height: 300px;">
                <canvas id="latencyChart"></canvas>
            </div>
        @else
            <div class="flex items-center justify-center text-sm text-gray-400 dark:text-gray-500" style="height: 300px;">
                Aucune donnée de latence sur cette période.
            </div>
        @endif
    </div>

    {{-- Répartition des statuts --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Répartition des checks</h3>
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-5">
            {{ array_sum($statusCounts) }} vérification(s)
        </p>

        @if(array_sum($statusCounts) > 0)
            <div class="relative" style="height: 220px;">
                <canvas id="statusChart"></canvas>
            </div>

            {{-- Légende détaillée sous le camembert --}}
            <div class="mt-5 space-y-2">
                @php
                    $statusMeta = [
                        'UP'    => ['Opérationnel', '#10b981'],
                        'SLOW'  => ['Dégradé',      '#f59e0b'],
                        'DOWN'  => ['En panne',     '#ef4444'],
                        'ERROR' => ['Erreur',       '#991b1b'],
                    ];
                    $totalChecks = array_sum($statusCounts);
                @endphp
                @foreach($statusMeta as $key => [$label, $color])
                    @if(!empty($statusCounts[$key]))
                        @php $pct = round(($statusCounts[$key] / $totalChecks) * 100, 1); @endphp
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $color }}"></span>
                                {{ $label }}
                            </span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">
                                {{ $pct }}%
                                <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">({{ $statusCounts[$key] }})</span>
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="flex items-center justify-center text-sm text-gray-400 dark:text-gray-500" style="height: 220px;">
                Aucun check sur cette période.
            </div>
        @endif
    </div>
</div>

{{-- Tableau SLA par application --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
            Disponibilité par application —
            {{ ucfirst(\Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y')) }}
        </h3>
        <span class="text-xs text-gray-400 dark:text-gray-500">
            Du {{ $startDate->format('d/m/Y') }} au {{ $endDate->format('d/m/Y') }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Application</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">SLA cible</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Uptime réel</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Incidents</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Durée totale panne</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">MTTR</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Conformité</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appStats as $stat)
                @php
                $uptime = $stat['uptime'];
                $uptimeColor = $uptime >= 99 ? 'text-green-600 dark:text-green-400'
                : ($uptime >= 95 ? 'text-orange-500 dark:text-orange-400'
                : 'text-red-600 dark:text-red-400');

                // Conformité : CONFORME si >= SLA cible, LIMITE si >= 98, NON CONFORME sinon
                if ($uptime >= $stat['sla_target']) {
                $conformityLabel = 'CONFORME';
                $conformityClass = 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400';
                } elseif ($uptime >= 98) {
                $conformityLabel = 'LIMITE';
                $conformityClass = 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400';
                } else {
                $conformityLabel = 'NON CONFORME';
                $conformityClass = 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400';
                }
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                    {{-- Application --}}
                    <td class="px-6 py-4">
                        <a href="{{ route('applications.show', $stat['app']) }}"
                            class="font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                            {{ $stat['app']->name }}
                        </a>
                        @if($stat['app']->group_name)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $stat['app']->group_name }}</p>
                        @endif
                    </td>

                    {{-- SLA cible --}}
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                        {{ $stat['sla_target'] }}%
                    </td>

                    {{-- Uptime réel --}}
                    <td class="px-6 py-4">
                        <span class="font-semibold {{ $uptimeColor }}">{{ $uptime }}%</span>
                        @if($stat['total_checks'] > 0)
                        <div class="w-24 bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 mt-1.5">
                            <div class="h-1.5 rounded-full {{ $uptime >= 99 ? 'bg-green-500' : ($uptime >= 95 ? 'bg-orange-400' : 'bg-red-500') }}"
                                style="width: {{ min(100, $uptime) }}%">
                            </div>
                        </div>
                        @endif
                    </td>

                    {{-- Incidents --}}
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                        {{ $stat['incidents'] }}
                    </td>

                    {{-- Durée totale panne --}}
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                        {{ $stat['downtime_min'] > 0 ? $stat['downtime_min'].' min' : '0 min' }}
                    </td>

                    {{-- MTTR --}}
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                        {{ $stat['mttr'] !== null ? $stat['mttr'].' min' : '—' }}
                    </td>

                    {{-- Conformité --}}
                    <td class="px-6 py-4">
                        <span class="inline-block px-2.5 py-1 rounded text-xs font-bold {{ $conformityClass }}">
                            {{ $conformityLabel }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-gray-400 dark:text-gray-500 py-12">
                        Aucune application active.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════ Chart.js ═══════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels       = @json($labels);
    const latencyData  = @json($latencyDatasets);
    const statusCounts = @json($statusCounts);

    let latencyChart = null;
    let statusChart  = null;

    // Couleurs adaptées au thème clair/sombre
    function theme() {
        const dark = document.documentElement.classList.contains('dark');
        return {
            text: dark ? '#9ca3af' : '#6b7280',
            grid: dark ? 'rgba(75,85,99,0.25)' : 'rgba(229,231,235,0.9)',
            tooltipBg: dark ? '#1f2937' : '#ffffff',
            tooltipText: dark ? '#f3f4f6' : '#111827',
            border: dark ? '#374151' : '#e5e7eb',
        };
    }

    function buildLatency() {
        const el = document.getElementById('latencyChart');
        if (!el || latencyData.length === 0) return;

        const t = theme();

        latencyChart = new Chart(el, {
            type: 'line',
            data: {
                labels: labels,
                datasets: latencyData.map(d => ({
                    label: d.label,
                    data: d.data,
                    borderColor: d.color,
                    backgroundColor: d.color + '20',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    tension: 0.35,
                    spanGaps: true,
                    fill: false,
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: t.text,
                            boxWidth: 10,
                            boxHeight: 10,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 16,
                            font: { size: 11 },
                        }
                    },
                    tooltip: {
                        backgroundColor: t.tooltipBg,
                        titleColor: t.tooltipText,
                        bodyColor: t.tooltipText,
                        borderColor: t.border,
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label} : ${ctx.parsed.y} ms`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: t.grid, drawBorder: false },
                        ticks: { color: t.text, font: { size: 11 }, maxRotation: 0, autoSkipPadding: 20 }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: t.grid, drawBorder: false },
                        ticks: {
                            color: t.text,
                            font: { size: 11 },
                            callback: v => v + ' ms'
                        }
                    }
                }
            }
        });
    }

    function buildStatus() {
        const el = document.getElementById('statusChart');
        if (!el) return;

        const colors = { UP: '#10b981', SLOW: '#f59e0b', DOWN: '#ef4444', ERROR: '#991b1b' };
        const keys   = Object.keys(statusCounts);
        if (keys.length === 0) return;

        const t = theme();

        statusChart = new Chart(el, {
            type: 'doughnut',
            data: {
                labels: keys,
                datasets: [{
                    data: keys.map(k => statusCounts[k]),
                    backgroundColor: keys.map(k => colors[k] || '#9ca3af'),
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: t.tooltipBg,
                        titleColor: t.tooltipText,
                        bodyColor: t.tooltipText,
                        borderColor: t.border,
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: ctx => ` ${ctx.label} : ${ctx.parsed} check(s)`
                        }
                    }
                }
            }
        });
    }

    function render() {
        if (latencyChart) { latencyChart.destroy(); latencyChart = null; }
        if (statusChart)  { statusChart.destroy();  statusChart  = null; }
        buildLatency();
        buildStatus();
    }

    render();

    // Redessine les graphiques quand on bascule clair/sombre
    new MutationObserver(render).observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
})();
</script>

@endsection