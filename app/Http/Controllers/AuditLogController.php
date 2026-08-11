<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * AuditLogController
 *
 * Rôle : affiche l'historique de toutes les actions effectuées
 * sur la plateforme (création, modification, suppression, connexion...).
 *
 * Accessible uniquement aux Super Admins.
 *
 * Chaque entrée contient :
 * - L'utilisateur qui a effectué l'action
 * - Le type d'action (app.created, user.deleted, etc.)
 * - La ressource concernée (application, user, incident...)
 * - Les données associées (payload JSON)
 * - L'adresse IP
 * - La date/heure
 */
class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);

        $logs = AuditLog::with('user')
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->action, fn($q) => $q->where('action', 'like', '%' . $request->action . '%'))
            ->when($request->resource_type, fn($q) => $q->where('resource_type', $request->resource_type))
            ->orderByDesc('created_at')
            ->paginate(15);


        $users = User::orderBy('name')->get();

        $resourceTypes = AuditLog::distinct()->pluck('resource_type')->filter()->sort()->values();

        return view('audit-logs.index', compact('logs', 'users', 'resourceTypes'));
    }
}
