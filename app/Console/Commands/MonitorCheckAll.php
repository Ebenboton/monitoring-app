<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * MonitorCheckAll
 *
 * Rôle : vérifie toutes les applications actives EN PARALLÈLE.
 * Appelée chaque minute par le scheduler et par le bouton « Actualiser ».
 *
 * Les applications sont traitées par lots de BATCH_SIZE pour ne pas lancer
 * trop de processus PHP simultanés (chacun consomme de la mémoire).
 * Ajuste BATCH_SIZE selon la puissance du serveur.
 *
 * S'appuie sur la commande monitor:check {nom}, qui retrouve l'application
 * par son nom.
 */
class MonitorCheckAll extends Command
{
    protected $signature   = 'monitor:check-all';
    protected $description = 'Vérifie toutes les applications actives en parallèle';

    private const MAX_PARALLEL = 10;

    public function handle(): int
    {
        $apps = Application::where('is_active', true)->orderBy('name')->get();

        if ($apps->isEmpty()) {
            $this->info('Aucune application active.');
            return self::SUCCESS;
        }

        $this->info("Vérification de {$apps->count()} application(s) en parallèle...");

        $php     = (new PhpExecutableFinder())->find(false) ?: 'php';
        $start   = microtime(true);
        $queue   = $apps->all();
        $running = [];

        while ($queue || $running) {
            // 1. Remplit les places libres du pool
            while ($queue && count($running) < self::MAX_PARALLEL) {
                $app     = array_shift($queue);
                $process = new Process([$php, 'artisan', 'monitor:check', $app->name], base_path(), null, null, 120);
                $process->start();
                $running[$app->id] = [$app, $process];
            }

            // 2. Récupère ceux qui viennent de finir
            foreach ($running as $id => [$app, $process]) {
                try {
                    $process->checkTimeout();
                } catch (\Symfony\Component\Process\Exception\ProcessTimedOutException $e) {
                    $this->warn("  ⚠ {$app->name} : vérification interrompue (timeout 120s)");
                    unset($running[$id]);
                    continue;
                }

                if (!$process->isRunning()) {
                    unset($running[$id]);
                    $app->refresh();
                    $ms = $app->checks()->latest('checked_at')->value('response_time_ms');
                    $this->line("  [{$app->current_status}] {$app->name} — " . ($ms !== null ? "{$ms}ms" : '—'));

                    if (!$process->isSuccessful()) {
                        $this->warn('    ⚠ ' . trim($process->getErrorOutput()));
                    }
                }
            }

            usleep(100000); // pause de 0,1 s pour ne pas saturer le CPU
        }

        $this->info('Checks terminés en ' . round(microtime(true) - $start, 1) . 's.');
        return self::SUCCESS;
    }
}
