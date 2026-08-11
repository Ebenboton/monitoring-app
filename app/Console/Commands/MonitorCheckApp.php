<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Services\MonitorService;
use Illuminate\Console\Command;

class MonitorCheckApp extends Command
{
    protected $signature   = 'monitor:check {app : Nom ou ID de l\'application}';
    protected $description = 'Lance le check d\'une application spécifique';

    public function handle(MonitorService $monitor): void
    {
        $identifier = $this->argument('app');

        $app = Application::where('id', $identifier)
            ->orWhere('name', $identifier)
            ->first();

        if (!$app) {
            $this->error("Application \"{$identifier}\" introuvable.");
            return;
        }

        $this->info("Vérification de {$app->name}...");
        $check = $monitor->checkApplication($app);

        $this->table(
            ['Champ', 'Valeur'],
            [
                ['Statut',          $check->status],
                ['Code HTTP',       $check->http_code ?? '—'],
                ['Latence',         $check->response_time_ms ? $check->response_time_ms.'ms' : '—'],
                ['Keyword OK',      $check->keyword_ok === null ? '—' : ($check->keyword_ok ? 'Oui' : 'Non')],
                ['Auth OK',         $check->auth_ok === null ? '—' : ($check->auth_ok ? 'Oui' : 'Non')],
                ['SSL jours',       $check->ssl_days_remaining ?? '—'],
                ['Erreur',          $check->error_message ?? '—'],
            ]
        );
    }
}