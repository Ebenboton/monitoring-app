<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Alert;
use App\Models\Check;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * ReportController
 *
 * Rôle : génère les rapports SLA et statistiques de surveillance.
 *
 * index()  → page principale des rapports avec KPIs et tableau SLA
 * export() → export CSV des données brutes de checks
 *
 * Métriques calculées :
 * - Uptime % = (checks UP / total checks) × 100
 * - MTTR     = durée moyenne de résolution des incidents
 * - Alertes  = nombre d'emails envoyés sur la période
 */
class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Trouve le premier check enregistré pour déterminer depuis quand on surveille
        $firstCheck = \App\Models\Check::oldest('checked_at')->first();
        $firstMonth = $firstCheck
            ? Carbon::parse($firstCheck->checked_at)->startOfMonth()
            : now()->startOfMonth();

        // Génère la liste des mois disponibles depuis le premier check
        $availableMonths = [];
        $current = $firstMonth->copy();
        while ($current->lte(now())) {
            $availableMonths[] = $current->format('Y-m');
            $current->addMonth();
        }
        $availableMonths = array_reverse($availableMonths); // Plus récent en premier

        // Mois sélectionné (défaut : mois en cours)
        $selectedMonth = $request->get('month', now()->format('Y-m'));

        // Si le mois demandé n'existe pas, prendre le plus récent
        if (!in_array($selectedMonth, $availableMonths)) {
            $selectedMonth = $availableMonths[0] ?? now()->format('Y-m');
        }

        $startDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $endDate   = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

        // Est-ce le mois en cours ? Si oui, la fin = maintenant
        if ($endDate->gt(now())) {
            $endDate = now();
        }

        $applications = Application::where('is_active', true)->orderBy('name')->get();

        // Calcul des stats par application
        $appStats = $applications->map(function ($app) use ($startDate, $endDate) {
            $checks = Check::where('application_id', $app->id)
                ->whereBetween('checked_at', [$startDate, $endDate])
                ->get();

            $total   = $checks->count();
            $upCount = $checks->where('status', 'UP')->count();
            $uptime  = $total > 0 ? round(($upCount / $total) * 100, 2) : 0;

            $incidents = Incident::where('application_id', $app->id)
                ->whereBetween('started_at', [$startDate, $endDate])
                ->get();

            $resolvedIncidents = $incidents->where('is_resolved', true);

            $mttr = $resolvedIncidents->count() > 0
                ? round($resolvedIncidents->avg('duration_seconds') / 60, 1)
                : null;

            $totalDowntime = $resolvedIncidents->sum('duration_seconds');

            return [
                'app'          => $app,
                'uptime'       => $uptime,
                'total_checks' => $total,
                'incidents'    => $incidents->count(),
                'mttr'         => $mttr,
                'downtime_min' => round($totalDowntime / 60),
                'sla_target'   => 99.0,
                'sla_ok'       => $uptime >= 99.0,
            ];
        });

        // KPIs globaux
        $globalUptime = $appStats->count() > 0
            ? round($appStats->avg('uptime'), 1)
            : 0;

        $totalIncidents = Incident::whereBetween('started_at', [$startDate, $endDate])->count();

        $globalMttr = Incident::whereBetween('started_at', [$startDate, $endDate])
            ->where('is_resolved', true)
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');
        $globalMttr = $globalMttr ? round($globalMttr / 60) : 0;

        $totalAlerts = Alert::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'sent')->count();

        $failedAlerts = Alert::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'failed')->count();

        return view('reports.index', compact(
            'appStats',
            'globalUptime',
            'totalIncidents',
            'globalMttr',
            'totalAlerts',
            'failedAlerts',
            'selectedMonth',
            'availableMonths',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export CSV des checks sur la période
     */
    public function export(Request $request)
    {
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $endDate   = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

        if ($endDate->gt(now())) {
            $endDate = now();
        }

        $checks = Check::with('application')
            ->whereBetween('checked_at', [$startDate, $endDate])
            ->orderByDesc('checked_at')
            ->get();

        $filename = 'rapport_sla_' . $selectedMonth . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($checks, $startDate, $endDate, $selectedMonth) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            // Titre du rapport
            fputcsv($handle, ['RAPPORT SLA M-MONITORING'], ';');
            fputcsv($handle, ['Période', Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y')], ';');
            fputcsv($handle, ['Généré le', now()->format('d/m/Y à H:i:s')], ';');
            fputcsv($handle, [], ';');

            // En-têtes
            fputcsv($handle, [
                'Application',
                'URL',
                'Groupe',
                'Statut',
                'Code HTTP',
                'Latence (ms)',
                'Keyword OK',
                'Auth OK',
                'SSL (jours)',
                'Erreur',
                'Date/Heure'
            ], ';');

            foreach ($checks as $check) {
                fputcsv($handle, [
                    $check->application->name,
                    $check->application->url,
                    $check->application->group_name ?? '',
                    $check->status,
                    $check->http_code ?? '',
                    $check->response_time_ms ?? '',
                    $check->keyword_ok === null ? '' : ($check->keyword_ok ? 'Oui' : 'Non'),
                    $check->auth_ok === null ? '' : ($check->auth_ok ? 'Oui' : 'Non'),
                    $check->ssl_days_remaining ?? '',
                    $check->error_message ?? '',
                    $check->checked_at->format('d/m/Y H:i:s'),
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
