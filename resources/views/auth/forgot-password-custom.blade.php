<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — M-Monitoring</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gray-100 dark:bg-gray-950 flex items-center justify-center">

    <div class="w-full max-w-md mx-4">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">M-Monitoring</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Réinitialisation du mot de passe</p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8 shadow-sm">

            @if(session('success'))
            {{-- Message de confirmation --}}
            <div class="flex items-start gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">Email envoyé</p>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">{{ session('success') }}</p>
                </div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                <p class="text-xs text-blue-700 dark:text-blue-300 font-medium mb-1">Prochaines étapes</p>
                <ol class="text-xs text-blue-600 dark:text-blue-400 space-y-1 list-decimal list-inside">
                    <li>Consultez votre boîte email</li>
                    <li>Connectez-vous avec le mot de passe temporaire</li>
                    <li>Définissez votre nouveau mot de passe</li>
                </ol>
            </div>

            <a href="{{ route('login') }}"
                class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg text-sm transition">
                Retour à la connexion
            </a>

            @else
            {{-- Formulaire --}}
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Mot de passe oublié ?</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Saisissez votre adresse email. Vous recevrez un mot de passe temporaire pour vous reconnecter.
                </p>
            </div>

            <form method="POST" action="{{ route('password.forgot.send') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Adresse email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="votre.email@exemple.com"
                        autofocus
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-400 dark:placeholder-gray-500 @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg text-sm transition">
                    Recevoir le mot de passe temporaire
                </button>
            </form>
            @endif

            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 text-center">
                <a href="{{ route('login') }}"
                    class="text-sm text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                    ← Retour à la connexion
                </a>
            </div>
        </div>
    </div>

</body>

</html>