<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Maintenance;
use Illuminate\Http\Request;

/**
 * MaintenanceController
 *
 * Rôle : gère les fenêtres de maintenance planifiées.
 *
 * index()   → liste toutes les maintenances (passées + futures)
 * create()  → formulaire de création
 * store()   → enregistre une nouvelle maintenance
 * destroy() → supprime une maintenance
 *
 * Pendant une maintenance active :
 * - Les checks continuent normalement
 * - Aucun email d'alerte n'est envoyé
 * - Le statut de l'app passe à MAINTENANCE dans le dashboard
 * - Si l'app est DOWN à la fin de la maintenance → alerte immédiate
 */
class MaintenanceController extends Controller
{
    /**
     * Liste toutes les maintenances
     */
    public function index()
    {
        $maintenances = Maintenance::with(['application', 'creator'])
            ->orderByDesc('starts_at')
            ->paginate(20);

        $applications = Application::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Stats rapides
        $stats = [
            'active'   => Maintenance::where('starts_at', '<=', now())
                ->where('ends_at', '>=', now())->count(),
            'upcoming' => Maintenance::where('starts_at', '>', now())->count(),
            'past'     => Maintenance::where('ends_at', '<', now())->count(),
            'total'    => Maintenance::count(),
        ];

        return view('maintenances.index', compact('maintenances', 'applications', 'stats'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $applications = Application::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('maintenances.create', compact('applications'));
    }

    /**
     * Enregistre une nouvelle maintenance
     *
     * Validation :
     * - starts_at doit être dans le futur (ou maintenant)
     * - ends_at doit être après starts_at
     * - L'application doit exister et être active
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'starts_at'      => 'required|date',
            'ends_at'        => 'required|date|after:starts_at',
            'reason'         => 'required|string|max:1000',
        ], [
            'application_id.required' => 'Veuillez sélectionner une application.',
            'starts_at.required'      => 'La date de début est obligatoire.',
            'ends_at.required'        => 'La date de fin est obligatoire.',
            'ends_at.after'           => 'La date de fin doit être après la date de début.',
            'reason.required'         => 'Le motif est obligatoire.',
        ]);

        // Le titre = le motif ou une valeur par défaut
        $data['title']      = $data['reason'] ?? 'Maintenance planifiée';
        $data['created_by'] = auth()->user()->id;

        $maintenance = Maintenance::create($data);

        if ($maintenance->isActive()) {
            $maintenance->application->update(['current_status' => 'MAINTENANCE']);
        }

        AuditLog::log('maintenance.created', 'maintenance', $maintenance->id, [
            'application' => $maintenance->application->name,
        ]);

        return redirect()
            ->route('maintenances.index')
            ->with('success', "Maintenance planifiée pour \"{$maintenance->application->name}\".");
    }



    /**
     * Formulaire de modification 
     */

    public function update(Request $request, Maintenance $maintenance): \Illuminate\Http\RedirectResponse
    {
        // On ne peut modifier qu'une maintenance planifiée
        if (now()->gte($maintenance->starts_at)) {
            return back()->with('error', 'Impossible de modifier une maintenance en cours ou terminée.');
        }

        $data = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'starts_at'      => 'required|date',
            'ends_at'        => 'required|date|after:starts_at',
            'reason'         => 'required|string|max:1000',
        ]);

        $data['title'] = $data['reason'];
        $maintenance->update($data);

        return redirect()
            ->route('maintenances.index')
            ->with('success', 'Maintenance modifiée avec succès.');
    }


    /**
     * Supprime une maintenance
     */
    public function destroy(Maintenance $maintenance)
    {
        $appName = $maintenance->application->name;
        $maintenance->delete();

        AuditLog::log('maintenance.deleted', 'maintenance', $maintenance->id, [
            'application' => $appName,
        ]);

        return redirect()
            ->route('maintenances.index')
            ->with('success', "Maintenance supprimée.");
    }

    /**
     * Marque une maintenance en cours comme terminée immédiatement
     */
    public function finish(Maintenance $maintenance): \Illuminate\Http\RedirectResponse
    {
        // Force la fin maintenant
        $maintenance->update(['ends_at' => now()]);

        // Remet le statut de l'app à UNKNOWN pour forcer un nouveau check
        $maintenance->application->update(['current_status' => 'UNKNOWN']);

        AuditLog::log('maintenance.finished', 'maintenance', $maintenance->id, [
            'application' => $maintenance->application->name,
        ]);

        return redirect()
            ->route('maintenances.index')
            ->with('success', "Maintenance terminée. Le prochain check remettra le statut à jour.");
    }
}
