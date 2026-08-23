<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Application;
use Illuminate\Http\Request;

/**
 * AlertController
 *
 * Rôle : affiche l'historique de toutes les alertes email
 * envoyées par le système de monitoring.
 *
 * Chaque alerte correspond à un email envoyé (ou tenté)
 * lors d'un incident, d'une escalade ou d'un rétablissement.
 */
class AlertController extends Controller
{
    public function index(Request $request)
    {
        $alerts = Alert::with(['application', 'incident'])
            ->when($request->type, fn($q) => $q->where('alert_type', $request->type))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->application_id, fn($q) => $q->where('application_id', $request->application_id))
            ->orderByDesc('created_at')
            ->paginate(15);

        $applications = Application::orderBy('name')->get();

        $stats = [
            'total'      => Alert::count(),
            'sent'       => Alert::where('status', 'sent')->count(),
            'failed'     => Alert::where('status', 'failed')->count(),
            'escalation' => Alert::where('alert_type', 'escalation')->count(),
            'pending'    => Alert::where('status', 'pending')->count(),
        ];

        return view('alerts.index', compact('alerts', 'applications', 'stats'));
    }
}
