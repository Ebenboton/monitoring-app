<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Définir mon mot de passe — M-Monitoring</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-100 dark:bg-gray-950 flex items-center justify-center">

<div class="w-full max-w-md mx-4">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">M-Monitoring</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Définissez votre mot de passe</p>
    </div>

    {{-- Card --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8 shadow-sm">

        {{-- Bannière info --}}
        <div class="flex items-start gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Première connexion</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                    Pour votre sécurité, vous devez définir un mot de passe personnel avant d'accéder à la plateforme.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('password.change.update') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                    Nouveau mot de passe <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password"
                    class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('password') border-red-500 @enderror"
                    oninput="checkStrength(this.value)">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Indicateur de force --}}
            <div id="strength-indicator" class="hidden space-y-2">
                <div class="flex gap-1">
                    <div id="bar1" class="h-1.5 flex-1 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                    <div id="bar2" class="h-1.5 flex-1 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                    <div id="bar3" class="h-1.5 flex-1 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                    <div id="bar4" class="h-1.5 flex-1 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                </div>
                <div class="grid grid-cols-2 gap-1 text-xs">
                    <span id="req-length"  class="text-gray-400">✗ 8 caractères minimum</span>
                    <span id="req-upper"   class="text-gray-400">✗ Une majuscule</span>
                    <span id="req-lower"   class="text-gray-400">✗ Une minuscule</span>
                    <span id="req-number"  class="text-gray-400">✗ Un chiffre</span>
                    <span id="req-special" class="text-gray-400">✗ Un caractère spécial</span>
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                    Confirmer le mot de passe <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation"
                    class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg text-sm transition mt-2">
                Définir mon mot de passe
            </button>
        </form>

        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <span class="text-xs text-gray-400 dark:text-gray-500">Connecté en tant que</span>
            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ auth()->user()->email }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="text-center mt-4">
        @csrf
        <button type="submit" class="text-sm text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
            Se déconnecter
        </button>
    </form>
</div>

<script>
function checkStrength(password) {
    const indicator = document.getElementById('strength-indicator');
    indicator.classList.remove('hidden');

    const checks = {
        length:  password.length >= 8,
        upper:   /[A-Z]/.test(password),
        lower:   /[a-z]/.test(password),
        number:  /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password),
    };

    // Met à jour les indicateurs
    setReq('req-length',  checks.length,  '✓ 8 caractères minimum', '✗ 8 caractères minimum');
    setReq('req-upper',   checks.upper,   '✓ Une majuscule',        '✗ Une majuscule');
    setReq('req-lower',   checks.lower,   '✓ Une minuscule',        '✗ Une minuscule');
    setReq('req-number',  checks.number,  '✓ Un chiffre',           '✗ Un chiffre');
    setReq('req-special', checks.special, '✓ Un caractère spécial', '✗ Un caractère spécial');

    // Barres de force
    const score = Object.values(checks).filter(Boolean).length;
    const colors = ['', 'bg-red-500', 'bg-orange-400', 'bg-yellow-400', 'bg-teal-500'];
    const bars = ['bar1','bar2','bar3','bar4'];

    bars.forEach((id, i) => {
        const el = document.getElementById(id);
        el.className = 'h-1.5 flex-1 rounded-full ';
        el.className += (i < score) ? (colors[score] || 'bg-teal-500') : 'bg-gray-200 dark:bg-gray-700';
    });
}

function setReq(id, ok, okText, failText) {
    const el = document.getElementById(id);
    el.textContent = ok ? okText : failText;
    el.className = ok ? 'text-green-600 dark:text-green-400' : 'text-gray-400';
}
</script>
</body>
</html>