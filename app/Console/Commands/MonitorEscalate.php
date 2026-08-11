<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\EscalationRule;
use App\Models\Incident;
use App\Services\AlertService;
use Illuminate\Console\Command;

/**
 * Commande : monitor:escalate
 *
 * Rôle : vérifie toutes les minutes les incidents ouverts et non acquittés.
 * Si un incident dépasse le délai d'escalade configuré sans être acquitté,
 * elle envoie un email au responsable de niveau supérieur (N+1, N+2...).
 *
 * Fonctionnement :
 * 1. Récupère tous les incidents ouverts et non acquittés
 * 2. Pour chaque incident, cherche les règles d'escalade applicables
 * 3. Vérifie si le délai est dépassé
 * 4. Si oui, envoie l'email d'escalade et enregistre en base
 */
class MonitorEscalate extends Command
{
    protected $signature   = 'monitor:escalate';
    protected $description = 'Vérifie et déclenche les escalades pour les incidents non acquittés';

    public function handle(AlertService $alertService): void
    {
        // Récupère tous les incidents ouverts et non acquittés
        $incidents = Incident::with(['application', 'alerts'])
            ->where('is_resolved', false)
            ->whereNull('acknowledged_by')
            ->get();

        if ($incidents->isEmpty()) {
            $this->info('Aucun incident non acquitté.');
            return;
        }

        $this->info("{$incidents->count()} incident(s) non acquitté(s) vérifiés...");

        foreach ($incidents as $incident) {
            $this->processEscalation($incident, $alertService);
        }

        $this->info('Vérification escalade terminée.');
    }

    private function processEscalation(Incident $incident, AlertService $alertService): void
    {
        $app = $incident->application;

        // Cherche les règles d'escalade pour cette application
        // D'abord les règles spécifiques à l'app, sinon les règles globales (application_id = null)
        $rules = EscalationRule::where(function ($q) use ($app) {
                $q->where('application_id', $app->id)
                  ->orWhereNull('application_id');
            })
            ->orderBy('level')
            ->get();

        if ($rules->isEmpty()) {
            return;
        }

        // Calcule depuis combien de minutes l'incident est ouvert
        $minutesSinceStart = $incident->started_at->diffInMinutes(now());

        foreach ($rules as $rule) {
            // Vérifie si le délai d'escalade est dépassé
            if ($minutesSinceStart < $rule->delay_minutes) {
                continue;
            }

            // Vérifie qu'on n'a pas déjà escaladé à ce niveau pour cet incident
            $alreadyEscalated = Alert::where('incident_id', $incident->id)
                ->where('alert_type', 'escalation')
                ->where('escalation_level', $rule->level)
                ->where('status', 'sent')
                ->exists();

            if ($alreadyEscalated) {
                continue;
            }

            // Envoie l'email d'escalade
            $this->sendEscalationEmail($incident, $rule, $alertService);

            $this->line("  ↗ Escalade niveau {$rule->level} pour {$app->name} → {$rule->recipient_email}");
        }
    }

    private function sendEscalationEmail(Incident $incident, EscalationRule $rule, AlertService $alertService): void
    {
        try {
            \Illuminate\Support\Facades\Mail::to($rule->recipient_email)
                ->send(new \App\Mail\AlertDownMail(
                    $incident->application,
                    $incident,
                    null,
                    null,
                    "⚠ ESCALADE NIVEAU {$rule->level} — Incident non acquitté depuis {$incident->started_at->diffForHumans()}"
                ));

            // Enregistre l'escalade en base
            Alert::create([
                'incident_id'      => $incident->id,
                'application_id'   => $incident->application_id,
                'recipient_email'  => $rule->recipient_email,
                'subject'          => "Escalade N{$rule->level} — {$incident->application->name}",
                'body'             => '',
                'alert_type'       => 'escalation',
                'escalation_level' => $rule->level,
                'status'           => 'sent',
                'sent_at'          => now(),
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error(
                "Escalade échouée pour incident {$incident->id} niveau {$rule->level} : {$e->getMessage()}"
            );
        }
    }
}