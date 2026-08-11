<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — M-Monitoring</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gray-100 dark:bg-gray-950 flex items-center justify-center">

    <div class="w-full max-w-md mx-4">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">M-Monitoring</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Plateforme de surveillance applicative</p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Bon retour</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Connectez-vous à votre espace</p>

            {{-- Message de statut (reset password Breeze) --}}
            @if (session('status'))
            <div class="flex items-start gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 mb-5">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('status') }}</p>
            </div>
            @endif

            {{-- Erreurs globales --}}
            @if ($errors->any())
            <div class="flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-5">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-red-700 dark:text-red-300">{{ $errors->first() }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Adresse email
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        placeholder="votre.email@exemple.com"
                        autofocus autocomplete="username"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-400 dark:placeholder-gray-500 @error('email') border-red-500 @enderror">
                </div>

                {{-- Mot de passe --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm text-gray-600 dark:text-gray-400">
                            Mot de passe
                        </label>
                        <a href="{{ route('password.forgot') }}"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                            Mot de passe oublié ?
                        </a>
                    </div>
                    <input id="password" type="password" name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('password') border-red-500 @enderror">
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-2">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-400">
                    <label for="remember_me" class="text-sm text-gray-600 dark:text-gray-400">
                        Se souvenir de moi
                    </label>
                </div>

                {{-- Bouton connexion --}}
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg text-sm transition mt-2">
                    Se connecter
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 dark:text-gray-500 mt-6">
            M-Monitoring © {{ date('Y') }} — Tous droits réservés.
        </p>
    </div>

</body>

</html>