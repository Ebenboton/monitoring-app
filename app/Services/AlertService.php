<?php

namespace App\Services;

use App\Mail\AlertAuthMail;
use App\Mail\AlertDownMail;
use App\Mail\AlertRecoveryMail;
use App\Mail\AlertSlowMail;
use App\Mail\AlertSslMail;
use App\Models\Alert;
use App\Models\Application;
use App\Models\Check;
use App\Models\Incident;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AlertService
{
    /**
     * Détermine et envoie l'alerte appropriée selon le résultat du check
     */
    public function handleAlert(Application $app, Check $check, ?Incident $incident): void
    {
        // Pas d'alerte en maintenance
        if ($app->current_status === 'MAINTENANCE') {
            return;
        }

        // Récupère les destinataires
        $recipients = $this->getRecipients($app);
        if (empty($recipients)) {
            Log::warning("AlertService: aucun destinataire pour {$app->name}");
            return;
        }

        switch ($check->status) {
            case 'DOWN':
            case 'ERROR':
                if ($incident) {
                    $this->sendDownAlert($app, $check, $incident, $recipients);
                }
                break;

            case 'SLOW':
                $this->sendSlowAlert($app, $check, $recipients);
                break;
        }

        // Alerte SSL séparée si nécessaire
        if ($check->ssl_days_remaining !== null && $check->ssl_days_remaining <= $app->ssl_alert_days) {
            $this->sendSslAlert($app, $check->ssl_days_remaining, $recipients);
        }
    }

    /**
     * Alerte panne DOWN
     */
    public function sendDownAlert(
        Application $app,
        Check $check,
        Incident $incident,
        array $recipients
    ): void {
        // Vérifie qu'on n'a pas déjà envoyé une alerte pour cet incident
        $alreadySent = Alert::where('incident_id', $incident->id)
            ->whereIn('alert_type', ['down', 'auth'])
            ->where('status', 'sent')
            ->exists();

        if ($alreadySent) {
            return;
        }

        // Détermine le type d'alerte
        $alertType = $check->auth_ok === false ? 'auth' : 'down';

        foreach ($recipients as $email) {
            try {
                if ($alertType === 'auth') {
                    Mail::to($email)->send(new AlertAuthMail($app, $incident));
                } else {
                    Mail::to($email)->send(new AlertDownMail(
                        $app,
                        $incident,
                        $check->http_code,
                        $check->response_time_ms,
                        $check->error_message
                    ));
                }

                $this->saveAlert($incident->id, $app->id, $email, $alertType, 'sent');
            } catch (\Exception $e) {
                Log::error("AlertService: échec envoi email à {$email} pour {$app->name} : {$e->getMessage()}");
                $this->saveAlert($incident->id, $app->id, $email, $alertType, 'failed', $e->getMessage());
            }
        }
    }

    /**
     * Alerte dégradation SLOW
     */
    public function sendSlowAlert(Application $app, Check $check, array $recipients): void
    {
        // Anti-spam : max 1 alerte SLOW par heure par application
        $recentAlert = Alert::where('application_id', $app->id)
            ->where('alert_type', 'slow')
            ->where('status', 'sent')
            ->where('created_at', '>=', now()->subHour())
            ->exists();

        if ($recentAlert) {
            return;
        }

        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(new AlertSlowMail(
                    $app,
                    $check->response_time_ms,
                    $app->latency_warn_ms
                ));

                $this->saveAlert(null, $app->id, $email, 'slow', 'sent');
            } catch (\Exception $e) {
                Log::error("AlertService: échec envoi SLOW à {$email} : {$e->getMessage()}");
                $this->saveAlert(null, $app->id, $email, 'slow', 'failed', $e->getMessage());
            }
        }
    }

    /**
     * Alerte SSL
     */
    public function sendSslAlert(Application $app, int $daysRemaining, array $recipients): void
    {
        // Anti-spam : max 1 alerte SSL par jour par application
        $recentAlert = Alert::where('application_id', $app->id)
            ->where('alert_type', 'ssl')
            ->where('status', 'sent')
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($recentAlert) {
            return;
        }

        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(new AlertSslMail($app, $daysRemaining));
                $this->saveAlert(null, $app->id, $email, 'ssl', 'sent');
            } catch (\Exception $e) {
                Log::error("AlertService: échec envoi SSL à {$email} : {$e->getMessage()}");
                $this->saveAlert(null, $app->id, $email, 'ssl', 'failed', $e->getMessage());
            }
        }
    }

    /**
     * Alerte rétablissement
     */
    public function sendRecoveryAlert(Application $app, Incident $incident): void
    {
        $recipients = $this->getRecipients($app);
        if (empty($recipients)) {
            return;
        }

        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(new AlertRecoveryMail($app, $incident));
                $this->saveAlert($incident->id, $app->id, $email, 'recovery', 'sent');
            } catch (\Exception $e) {
                Log::error("AlertService: échec envoi recovery à {$email} : {$e->getMessage()}");
                $this->saveAlert($incident->id, $app->id, $email, 'recovery', 'failed', $e->getMessage());
            }
        }
    }

    /**
     * Récupère les destinataires pour une application
     * Pour l'instant : tous les admins et super_admins actifs
     * Plus tard : règles d'escalade par application
     */
    private function getRecipients(Application $app): array
    {
        return \App\Models\User::whereIn('role', ['super_admin', 'admin'])
            ->where('is_active', true)
            ->pluck('email')
            ->toArray();
    }

    /**
     * Enregistre l'alerte en base de données
     */
    private function saveAlert(
        ?string $incidentId,
        string $applicationId,
        string $email,
        string $type,
        string $status,
        ?string $errorMessage = null
    ): void {
        // Crée un incident factice si null (pour les alertes SLOW/SSL sans incident)
        if ($incidentId === null) {
            $incidentId = Incident::where('application_id', $applicationId)
                ->latest('started_at')
                ->value('id');
        }

        if (!$incidentId) {
            return;
        }

        Alert::create([
            'incident_id'      => $incidentId,
            'application_id'   => $applicationId,
            'recipient_email'  => $email,
            'subject'          => '',
            'body'             => '',
            'alert_type'       => $type,
            'escalation_level' => 1,
            'status'           => $status,
            'sent_at'          => $status === 'sent' ? now() : null,
            'error_message'    => $errorMessage,
        ]);
    }
}
