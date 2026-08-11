<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Services\MonitorService;
use Illuminate\Console\Command;

class MonitorSslCheck extends Command
{
    protected $signature   = 'monitor:ssl-check';
    protected $description = 'Vérifie les certificats SSL de toutes les applications HTTPS';

    public function handle(MonitorService $monitor): void
    {
        $apps = Application::where('is_active', true)
            ->where('ssl_check', true)
            ->where('url', 'like', 'https://%')
            ->get();

        if ($apps->isEmpty()) {
            $this->info('Aucune application HTTPS avec SSL check activé.');
            return;
        }

        $this->info("Vérification SSL de {$apps->count()} application(s)...");

        foreach ($apps as $app) {
            $check = $app->checks()->latest('checked_at')->first();
            if (!$check) {
                $this->warn("  {$app->name} — aucun check disponible");
                continue;
            }

            $days = $check->ssl_days_remaining;
            if ($days === null) {
                $this->warn("  {$app->name} — SSL non vérifié");
                continue;
            }

            $icon = $days < 7 ? '🔴' : ($days < 30 ? '🟡' : '🟢');
            $this->line("  {$icon} {$app->name} — {$days} jours restants");
        }

        $this->info('Vérification SSL terminée.');
    }
}