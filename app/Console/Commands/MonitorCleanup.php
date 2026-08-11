<?php

namespace App\Console\Commands;

use App\Models\Check;
use App\Models\Alert;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Console\Command;

/**
 * MonitorCleanup
 *
 * Rôle : purge automatique des données anciennes.
 * Tourne tous les jours à 02h00 via le scheduler.
 *
 * Ce qui est purgé :
 * - Checks plus vieux que X jours (configurable dans Paramètres)
 * - Alertes envoyées plus vieilles que 90 jours
 * - Audit logs plus vieux que 180 jours
 *
 * Ce qui n'est PAS purgé :
 * - Les incidents (historique précieux)
 * - Les utilisateurs
 * - Les applications
 */
class MonitorCleanup extends Command
{
    protected $signature   = 'monitor:cleanup';
    protected $description = 'Purge les données anciennes (checks, alertes, audit logs)';

    public function handle(): void
    {
        $this->info('Démarrage de la purge...');

        // Rétention des checks (configurable dans Paramètres)
        $retentionDays = (int) Setting::get('check_retention_days', 90);

        // 1. Purge des checks
        $deletedChecks = Check::where('checked_at', '<', now()->subDays($retentionDays))->delete();
        $this->line("  ✓ Checks supprimés : {$deletedChecks} (> {$retentionDays} jours)");

        // 2. Purge des alertes envoyées (90 jours fixes)
        $deletedAlerts = Alert::where('status', 'sent')
            ->where('created_at', '<', now()->subDays(90))
            ->delete();
        $this->line("  ✓ Alertes supprimées : {$deletedAlerts} (> 90 jours)");

        // 3. Purge des audit logs (180 jours fixes)
        $deletedLogs = AuditLog::where('created_at', '<', now()->subDays(180))->delete();
        $this->line("  ✓ Audit logs supprimés : {$deletedLogs} (> 180 jours)");

        $this->info('Purge terminée.');
    }
}
