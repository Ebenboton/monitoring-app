<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * SettingsServiceProvider
 *
 * Rôle : charge les paramètres système depuis la base de données
 * et les applique à la configuration Laravel à chaque requête.
 *
 * Cela permet de modifier le SMTP et autres paramètres
 * depuis l'interface web sans toucher au fichier .env.
 *
 * Précaution : on vérifie que la table settings existe avant
 * de tenter de lire (évite les erreurs lors des migrations).
 */
class SettingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            // Vérifie que la table existe (évite erreur lors de migrate:fresh)
            if (!Schema::hasTable('settings')) {
                return;
            }

            $settings = Setting::allAsArray();

            if (empty($settings)) {
                return;
            }

            // Application de la config SMTP
            if (!empty($settings['mail_host'])) {
                config([
                    'mail.mailers.smtp.host'       => $settings['mail_host'],
                    'mail.mailers.smtp.port'       => $settings['mail_port'] ?? 587,
                    'mail.mailers.smtp.encryption' => $settings['mail_encryption'] ?? 'tls',
                    'mail.mailers.smtp.username'   => $settings['mail_username'] ?? '',
                    'mail.mailers.smtp.password'   => $settings['mail_password'] ?? '',
                    'mail.from.address'            => $settings['mail_username'] ?? '',
                    'mail.from.name'               => 'M-Monitoring',
                ]);
            }
        } catch (\Exception $e) {
            // Silencieux — ne pas bloquer l'app si la BDD est indisponible
            \Illuminate\Support\Facades\Log::warning('SettingsServiceProvider: ' . $e->getMessage());
        }
    }
}
