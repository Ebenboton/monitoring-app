<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\AuditLog;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use Illuminate\Support\Facades\Crypt;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::query()
            ->when(request('status'), fn($q) => $q->where('current_status', request('status')))
            ->when(request('group'), fn($q) => $q->where('group_name', request('group')))
            ->when(request('search'), fn($q) => $q->where('name', 'like', '%' . request('search') . '%'))
            ->orderBy('name')
            ->get();

        $stats = [
            'total'       => $applications->count(),
            'up'          => $applications->where('current_status', 'UP')->count(),
            'down'        => $applications->where('current_status', 'DOWN')->count(),
            'slow'        => $applications->where('current_status', 'SLOW')->count(),
            'maintenance' => $applications->where('current_status', 'MAINTENANCE')->count(),
        ];

        $groups = Application::select('group_name')
            ->whereNotNull('group_name')
            ->distinct()
            ->pluck('group_name');

        return view('applications.index', compact('applications', 'stats', 'groups'));
    }

    public function create()
    {
        return view('applications.create');
    }

    public function store(StoreApplicationRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->user()?->id;

        // Cast des types corrects
        $data['check_interval_seconds'] = (int) $data['check_interval_seconds'];
        $data['timeout_ms']             = (int) $data['timeout_ms'];
        $data['latency_warn_ms']        = (int) $data['latency_warn_ms'];
        $data['latency_down_ms']        = (int) $data['latency_down_ms'];
        $data['retry_count']            = (int) $data['retry_count'];
        $data['ssl_alert_days']         = isset($data['ssl_alert_days']) ? (int) $data['ssl_alert_days'] : 30;
        $data['ssl_check']              = (bool) ($data['ssl_check'] ?? false);
        $data['auth_enabled']           = (bool) ($data['auth_enabled'] ?? false);
        $data['is_active']              = (bool) ($data['is_active'] ?? false);

        // Chiffrement des credentials
        if (!empty($data['auth_credential'])) {
            $data['auth_credential'] = Crypt::encryptString($data['auth_credential']);
        }
        if (!empty($data['auth_password'])) {
            $data['auth_password'] = Crypt::encryptString($data['auth_password']);
        }

        $application = Application::create($data);

        AuditLog::log('app.created', 'application', $application->id, [
            'name' => $application->name,
            'url'  => $application->url,
        ]);

        return redirect()
            ->route('applications.index')
            ->with('success', "Application \"{$application->name}\" créée avec succès.");
    }



    public function show(Application $application)
    {
        $recentChecks = $application->checks()
            ->orderByDesc('checked_at')
            ->limit(50)
            ->get();

        $openIncident = $application->incidents()
            ->where('is_resolved', false)
            ->latest('started_at')
            ->first();

        return view('applications.show', compact('application', 'recentChecks', 'openIncident'));
    }

    public function edit(Application $application)
    {
        // Déchiffrement pour l'affichage dans le formulaire
        if ($application->auth_credential) {
            $application->auth_credential = Crypt::decryptString($application->auth_credential);
        }
        if ($application->auth_password) {
            $application->auth_password = Crypt::decryptString($application->auth_password);
        }

        return view('applications.edit', compact('application'));
    }

    public function update(UpdateApplicationRequest $request, Application $application)
    {
        $data = $request->validated();

        if (!empty($data['auth_credential'])) {
            $data['auth_credential'] = Crypt::encryptString($data['auth_credential']);
        }
        if (!empty($data['auth_password'])) {
            $data['auth_password'] = Crypt::encryptString($data['auth_password']);
        }

        $application->update($data);

        AuditLog::log('app.updated', 'application', $application->id, [
            'name' => $application->name,
        ]);

        return redirect()
            ->route('applications.index')
            ->with('success', "Application \"{$application->name}\" mise à jour.");
    }

    public function destroy(Application $application)
    {
        $name = $application->name;
        $application->delete();

        AuditLog::log('app.deleted', 'application', $application->id, [
            'name' => $name,
        ]);

        return redirect()
            ->route('applications.index')
            ->with('success', "Application \"{$name}\" supprimée.");
    }
}
