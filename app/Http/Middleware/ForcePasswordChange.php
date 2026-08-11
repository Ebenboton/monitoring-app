<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * ForcePasswordChange
 *
 * Rôle : middleware qui force l'utilisateur à changer son mot de passe
 * avant d'accéder à n'importe quelle page de l'application.
 *
 * Déclenché quand : must_change_password = true sur le compte utilisateur.
 * Cas d'usage :
 * - Première connexion après création de compte
 * - Première connexion après réinitialisation par un admin
 *
 * Routes exclues : la page de changement elle-même et la déconnexion.
 */
class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        if (
            auth()->check() &&
            auth()->user()->must_change_password &&
            !$request->routeIs('password.change') &&
            !$request->routeIs('password.change.update') &&
            !$request->routeIs('logout')
        ) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}