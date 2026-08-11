<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Incident;
use Illuminate\Http\Request;

/**
 * IncidentController
 *
 * Rôle : gère les incidents de surveillance.
 *
 * index()       → affiche la liste de tous les incidents (ouverts + résolus)
 * acknowledge() → acquitte un incident (marque comme pris en charge)
 *
 * L'acquittement arrête les escalades automatiques et enregistre
 * qui a pris en charge l'incident et à quelle heure.
 */
class IncidentController extends Controller
{
    /**
     * Liste tous les incidents avec filtres
     */
    public function index()
    {
        $incidents = Incident::with(['application', 'acknowledgedBy'])
            ->when(request('status'), function ($q) {
                if (request('status') === 'open') {
                    return $q->where('is_resolved', false);
                }
                if (request('status') === 'resolved') {
                    return $q->where('is_resolved', true);
                }
            })
            ->when(request('application_id'), fn($q) =>
                $q->where('application_id', request('application_id'))
            )
            ->orderByDesc('started_at')
            ->paginate(15);

        $applications = \App\Models\Application::orderBy('name')->get();

        // Statistiques rapides
        $stats = [
            'total'       => Incident::count(),
            'open'        => Incident::where('is_resolved', false)->count(),
            'resolved'    => Incident::where('is_resolved', true)->count(),
            'unacknowledged' => Incident::where('is_resolved', false)
                                ->whereNull('acknowledged_by')->count(),
        ];

        return view('incidents.index', compact('incidents', 'applications', 'stats'));
    }

    /**
     * Acquitte un incident
     *
     * Marque l'incident comme pris en charge par l'utilisateur connecté.
     * Après acquittement, les escalades automatiques s'arrêtent.
     */
    public function acknowledge(Incident $incident)
    {
        // Vérifie que l'incident n'est pas déjà acquitté
        if ($incident->acknowledged_by) {
            return back()->with('warning', "Cet incident a déjà été acquitté par {$incident->acknowledgedBy->name}.");
        }

        // Acquitte l'incident
        $incident->acknowledge(auth()->user());

        // Log de l'action
        AuditLog::log(
            'incident.acknowledged',
            'incident',
            $incident->id,
            [
                'application' => $incident->application->name,
                'started_at'  => $incident->started_at->toDateTimeString(),
            ]
        );

        return back()->with('success', "Incident acquitté avec succès.");
    }
}