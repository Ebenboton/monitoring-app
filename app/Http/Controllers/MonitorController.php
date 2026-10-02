<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * MonitorController
 *
 * Rôle : déclenche manuellement une vérification de toutes les applications
 * depuis le bouton « Actualiser » du dashboard, SANS bloquer la requête web.
 *
 * refresh() :
 * 1. Refuse un nouveau lancement si un a déjà été fait il y a moins de 30 s
 *    (évite les clics répétés qui empileraient des processus)
 * 2. Lance `php artisan monitor:check-all` dans un processus détaché
 * 3. Redirige immédiatement avec un message de confirmation
 *
 * Le binaire PHP est trouvé via PhpExecutableFinder : sous Apache/PHP-FPM,
 * la constante PHP_BINARY pointe vers php-fpm et non vers le PHP en ligne
 * de commande, il ne faut donc pas l'utiliser ici.
 */
class MonitorController extends Controller
{
    public function refresh(): RedirectResponse
    {
        // Anti-rafale : un seul lancement toutes les 30 secondes
        if (!Cache::add('monitor:manual-refresh', true, 30)) {
            return back()->with('success', 'Une vérification vient déjà d\'être lancée, patiente quelques secondes.');
        }

        $php = (new PhpExecutableFinder())->find(false) ?: 'php';
        $cmd = escapeshellarg($php) . ' ' . escapeshellarg(base_path('artisan')) . ' monitor:check-all';

        if (PHP_OS_FAMILY === 'Windows') {
            pclose(popen('start /B "" ' . $cmd . ' > NUL 2>&1', 'r'));
        } else {
            exec($cmd . ' > /dev/null 2>&1 &');
        }

        return back()
            ->with('success', 'Vérification lancée : les résultats se mettent à jour dans quelques secondes.')
            ->with('refreshing', true);
    }
}