<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * SettingController
 *
 * Rôle : gère la page des paramètres système.
 *
 * index()     → affiche tous les paramètres
 * update()    → sauvegarde les paramètres modifiés
 * testEmail() → envoie un email de test avec la config SMTP actuelle
 */
class SettingController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) abort(403);

        $settings = Setting::allAsArray();

        return view('settings.index', compact('settings'));
    }


    public function update(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) abort(403);

        $data = $request->validate([
            'mail_host'              => 'required|string|max:255',
            'mail_port'              => 'required|integer',
            'mail_encryption'        => 'required|in:tls,ssl,none',
            'mail_username'          => 'required|string|max:255',
            'mail_password'          => 'nullable|string|max:255',
            'mail_from'              => 'required|string|max:255',
            'alert_email_level1'     => 'nullable|email|max:255',
            'alert_email_level2'     => 'nullable|email|max:255',
            'alert_delay_level2'     => 'nullable|integer|min:1',
            'alert_email_level3'     => 'nullable|email|max:255',
            'alert_delay_level3'     => 'nullable|integer|min:1',
            'default_check_interval' => 'required|integer|min:30',
            'default_retry_count'    => 'required|integer|min:1|max:10',
            'check_retention_days'   => 'required|integer|min:7|max:365',
            'send_recovery_alert'    => 'boolean',
        ]);


        // Ne met pas à jour le mot de passe si vide
        if (empty($data['mail_password'])) {
            unset($data['mail_password']);
        }


        $data['send_recovery_alert'] = $request->has('send_recovery_alert')
            ? '1'
            : '0';


        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }


        // Met à jour la configuration mail en runtime
        $this->applyMailConfig();


        // Met à jour les règles d'escalade
        $this->updateEscalationRules($data);


        AuditLog::log('settings.updated', 'settings', null, [
            'updated_by' => Auth::check()
                ? Auth::user()->name
                : 'system',
        ]);


        return back()->with(
            'success',
            'Paramètres sauvegardés avec succès.'
        );
    }



    /**
     * Envoie un email de test avec la configuration SMTP actuelle
     */
    public function testEmail(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) abort(403);

        $request->validate([
            'test_email' => 'required|email',
        ]);

        /*
         * Récupération de l'expéditeur configuré.
         *
         * Exemple accepté :
         * M-Monitoring <botonben7@gmail.com>
         *
         * Le système séparera automatiquement :
         * - Nom      : M-Monitoring
         * - Adresse  : botonben7@gmail.com
         */
        [$fromAddress, $fromName] = $this->parseMailFrom(
            Setting::get('mail_from')
        );

        config([
            'mail.mailers.smtp.host'       => Setting::get('mail_host', 'smtp.gmail.com'),
            'mail.mailers.smtp.port'       => (int) Setting::get('mail_port', 587),
            'mail.mailers.smtp.encryption' => Setting::get('mail_encryption', 'tls'),
            'mail.mailers.smtp.username'   => Setting::get('mail_username'),
            'mail.mailers.smtp.password'   => Setting::get('mail_password'),

            // Expéditeur correctement séparé
            'mail.from.address'            => $fromAddress,
            'mail.from.name'               => $fromName,
        ]);

        app()->forgetInstance('mailer');
        app()->forgetInstance('swift.mailer');
        app()->forgetInstance('swift.transport');

        try {
            Mail::raw(
                "Ceci est un email de test envoyé depuis M-Monitoring.\n\nSi vous recevez cet email, la configuration SMTP est correcte.",
                fn($m) => $m
                    ->to($request->test_email)
                    ->subject('M-Monitoring — Test SMTP')
            );

            return back()->with(
                'success',
                "Email de test envoyé à {$request->test_email}."
            );
        } catch (\Exception $e) {
            return back()->with(
                'error',
                "Échec de l'envoi : " . $e->getMessage()
            );
        }
    }



    /**
     * Applique la configuration SMTP en runtime
     */
    private function applyMailConfig(): void
    {
        /*
         * Récupération et séparation de l'expéditeur.
         *
         * Exemple :
         * M-Monitoring <botonben7@gmail.com>
         *
         * devient :
         * address = botonben7@gmail.com
         * name    = M-Monitoring
         */
        [$fromAddress, $fromName] = $this->parseMailFrom(
            Setting::get('mail_from')
        );

        config([
            'mail.mailers.smtp.host'       => Setting::get('mail_host'),
            'mail.mailers.smtp.port'       => Setting::get('mail_port'),
            'mail.mailers.smtp.encryption' => Setting::get('mail_encryption'),
            'mail.mailers.smtp.username'   => Setting::get('mail_username'),
            'mail.mailers.smtp.password'   => Setting::get('mail_password'),

            // Expéditeur correctement configuré
            'mail.from.address'            => $fromAddress,
            'mail.from.name'               => $fromName,
        ]);
    }



    /**
     * Sépare le nom et l'adresse email de l'expéditeur.
     *
     * Formats acceptés :
     *
     * M-Monitoring <botonben7@gmail.com>
     *
     * ou simplement :
     *
     * botonben7@gmail.com
     *
     * Retourne :
     *
     * [
     *     'botonben7@gmail.com',
     *     'M-Monitoring'
     * ]
     */
    private function parseMailFrom(?string $mailFrom): array
    {
        $mailFrom = trim((string) $mailFrom);

        /*
         * Format :
         * Nom <email@example.com>
         */
        if (preg_match('/^(.*?)\s*<\s*([^<>]+)\s*>$/', $mailFrom, $matches)) {

            $name = trim($matches[1]);
            $address = trim($matches[2]);

            /*
             * Si le nom est vide, on utilise M-Monitoring.
             */
            if ($name === '') {
                $name = 'M-Monitoring';
            }

            return [
                $address,
                $name,
            ];
        }

        /*
         * Si seule l'adresse email est fournie.
         */
        if (filter_var($mailFrom, FILTER_VALIDATE_EMAIL)) {
            return [
                $mailFrom,
                'M-Monitoring',
            ];
        }

        /*
         * Sécurité :
         * si la valeur enregistrée n'est pas valide,
         * on utilise l'identifiant SMTP comme adresse.
         */
        $username = trim((string) Setting::get('mail_username'));

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return [
                $username,
                'M-Monitoring',
            ];
        }

        /*
         * Dernier recours.
         * Cette valeur devra normalement être corrigée dans les paramètres.
         */
        return [
            $mailFrom,
            'M-Monitoring',
        ];
    }



    /**
     * Met à jour les règles d'escalade en base
     */
    private function updateEscalationRules(array $data): void
    {
        $levels = [

            1 => [
                'email' => $data['alert_email_level1'] ?? null,
                'delay' => 0,
            ],

            2 => [
                'email' => $data['alert_email_level2'] ?? null,
                'delay' => $data['alert_delay_level2'] ?? 15,
            ],

            3 => [
                'email' => $data['alert_email_level3'] ?? null,
                'delay' => $data['alert_delay_level3'] ?? 30,
            ],

        ];


        foreach ($levels as $level => $config) {

            if (!empty($config['email'])) {

                \App\Models\EscalationRule::updateOrCreate(

                    [
                        'level' => $level,
                        'application_id' => null,
                    ],

                    [
                        'recipient_email' => $config['email'],
                        'delay_minutes' => $config['delay'],
                    ]

                );
            }
        }
    }
}
