<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Services\MonitorService;
use Illuminate\Console\Command;

class MonitorCheckAll extends Command
{
    protected $signature   = 'monitor:check-all';
    protected $description = 'Lance les checks sur toutes les applications actives';

    public function handle(MonitorService $monitor): void
    {
        $apps = Application::where('is_active', true)->get();

        if ($apps->isEmpty()) {
            $this->info('Aucune application active à surveiller.');
            return;
        }

        $this->info("Vérification de {$apps->count()} application(s)...");
        $bar = $this->output->createProgressBar($apps->count());
        $bar->start();

        foreach ($apps as $app) {
            try {
                $check = $monitor->checkApplication($app);
                $this->line('');
                $this->line("  [{$check->status}] {$app->name} — {$check->response_time_ms}ms");
            } catch (\Exception $e) {
                $this->error("  Erreur sur {$app->name} : {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->info('Checks terminés.');
    }
}