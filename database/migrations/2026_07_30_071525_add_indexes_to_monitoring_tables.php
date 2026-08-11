<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index ajoutés :
     *
     * checks :
     * - (application_id, checked_at DESC) → requêtes dashboard et historique
     * - (status, checked_at)              → calcul uptime et stats
     * - (checked_at)                      → purge et rapports par période
     *
     * incidents :
     * - (application_id, is_resolved)     → incidents ouverts par app
     * - (started_at)                      → rapports par période
     *
     * alerts :
     * - (application_id, alert_type)      → anti-spam AlertService
     * - (incident_id)                     → alerts par incident
     * - (created_at)                      → rapports par période
     *
     * audit_logs :
     * - (user_id, created_at)             → logs par utilisateur
     * - (resource_type, resource_id)      → logs par ressource
     */
    public function up(): void
    {
        // Index sur checks
        Schema::table('checks', function (Blueprint $table) {
            $table->index(['application_id', 'checked_at'], 'checks_app_date_idx');
            $table->index(['status', 'checked_at'], 'checks_status_date_idx');
            $table->index('checked_at', 'checks_date_idx');
        });

        // Index sur incidents
        Schema::table('incidents', function (Blueprint $table) {
            $table->index(['application_id', 'is_resolved'], 'incidents_app_resolved_idx');
            $table->index('started_at', 'incidents_started_idx');
        });

        // Index sur alerts
        Schema::table('alerts', function (Blueprint $table) {
            $table->index(['application_id', 'alert_type'], 'alerts_app_type_idx');
            $table->index('incident_id', 'alerts_incident_idx');
            $table->index('created_at', 'alerts_date_idx');
        });

        // Index sur audit_logs
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'audit_user_date_idx');
            $table->index(['resource_type', 'resource_id'], 'audit_resource_idx');
        });
    }

    public function down(): void
    {
        Schema::table('checks', function (Blueprint $table) {
            $table->dropIndex('checks_app_date_idx');
            $table->dropIndex('checks_status_date_idx');
            $table->dropIndex('checks_date_idx');
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropIndex('incidents_app_resolved_idx');
            $table->dropIndex('incidents_started_idx');
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->dropIndex('alerts_app_type_idx');
            $table->dropIndex('alerts_incident_idx');
            $table->dropIndex('alerts_date_idx');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('audit_user_date_idx');
            $table->dropIndex('audit_resource_idx');
        });
    }
};
