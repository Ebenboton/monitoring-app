@extends('layouts.app')

@section('title', 'Ajouter une application — M-Monitoring')

@section('page-title', 'Ajouter une application')
@section('page-subtitle', 'Dashboard / Nouvelle application')

@section('page-actions')
    <a href="{{ route('applications.index') }}"
       class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition">
        Annuler
    </a>
    <button type="submit" form="app-form"
        class="flex items-center gap-2 bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Enregistrer
    </button>
@endsection

@section('content')
<form id="app-form" method="POST" action="{{ route('applications.store') }}" class="pb-24">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- COLONNE GAUCHE --}}
        <div class="space-y-6">

            {{-- Informations générales --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Informations générales</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                            Nom de l'application <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="Ex: ERP Logistique"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 placeholder-gray-400 dark:placeholder-gray-500 @error('name') border-red-500 @enderror">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Groupe</label>
                        <select name="group_name" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">-- Aucun --</option>
                            @foreach(['Production', 'Staging', 'Local', 'Test', 'Demo'] as $g)
                            <option value="{{ $g }}" @selected(old('group_name')===$g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Toggle surveillance active --}}
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Surveillance active</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Active ou désactive les checks automatiques</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', true))>
                            <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:ring-2 peer-focus:ring-teal-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Paramètres HTTP --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Paramètres HTTP</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                            URL de vérification <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="url" value="{{ old('url') }}"
                            placeholder="https://example.com"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 placeholder-gray-400 dark:placeholder-gray-500 @error('url') border-red-500 @enderror">
                        @error('url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Méthode HTTP</label>
                            <select name="method" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                                @foreach(['GET','POST','HEAD'] as $m)
                                <option value="{{ $m }}" @selected(old('method', 'GET')===$m)>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Codes HTTP acceptés</label>
                            <input type="text" name="accepted_codes" value="{{ old('accepted_codes', '200') }}"
                                placeholder="200,201,301"
                                class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Fréquence (secondes)</label>
                            <input type="number" name="check_interval_seconds" value="{{ old('check_interval_seconds', 60) }}"
                                min="30" max="3600"
                                class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Timeout (ms)</label>
                            <input type="number" name="timeout_ms" value="{{ old('timeout_ms', 5000) }}"
                                class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Seuil WARN (ms)</label>
                            <input type="number" name="latency_warn_ms" value="{{ old('latency_warn_ms', 800) }}"
                                class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Seuil DOWN (ms)</label>
                            <input type="number" name="latency_down_ms" value="{{ old('latency_down_ms', 3000) }}"
                                class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Retries avant alerte</label>
                        <input type="number" name="retry_count" value="{{ old('retry_count', 3) }}"
                            min="1" max="10"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE --}}
        <div class="space-y-6">

            {{-- Vérification de contenu --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Vérification de contenu</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Mot-clé attendu (MUST CONTAIN)</label>
                        <input type="text" name="keyword_expected" value="{{ old('keyword_expected') }}"
                            placeholder="Ex: Bienvenue, dashboard..."
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 placeholder-gray-400 dark:placeholder-gray-500">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Si absent → état DOWN même si HTTP 200. Séparez par une virgule.</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Mot-clé interdit (MUST NOT CONTAIN)</label>
                        <input type="text" name="keyword_forbidden" value="{{ old('keyword_forbidden') }}"
                            placeholder="Ex: Fatal error, 500..."
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 placeholder-gray-400 dark:placeholder-gray-500">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Si présent → état DOWN. Séparez par une virgule.</p>
                    </div>
                </div>
            </div>

            {{-- Certificat SSL --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Certificat SSL</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Vérification SSL activée</p>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="ssl_check" value="1" class="sr-only peer" @checked(old('ssl_check', true))>
                            <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:ring-2 peer-focus:ring-teal-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Alerte avant expiration (jours)</label>
                        <input type="number" name="ssl_alert_days" value="{{ old('ssl_alert_days', 30) }}"
                            min="1"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Une alerte email sera envoyée X jours avant l'expiration</p>
                    </div>
                </div>
            </div>

            {{-- Authentification --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Vérification d'authentification</h2>
                    <span class="text-xs font-bold px-2.5 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full uppercase tracking-wider">
                        Obligatoire
                    </span>
                </div>

                <div class="flex items-start gap-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-3 mb-5">
                    <span class="w-2 h-2 rounded-full bg-orange-400 flex-shrink-0 mt-1"></span>
                    <p class="text-xs text-orange-700 dark:text-orange-300">
                        L'authentification vérifie que les utilisateurs peuvent réellement se connecter, pas seulement que le serveur répond.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Vérification auth activée</p>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="auth_enabled" value="1" id="auth_enabled" class="sr-only peer" @checked(old('auth_enabled'))>
                            <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:ring-2 peer-focus:ring-teal-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                        </label>
                    </div>

                    <div id="auth_fields" class="{{ old('auth_enabled') ? '' : 'hidden' }} space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Type d'authentification</label>
                            <select name="auth_type" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">-- Choisir --</option>
                                @foreach(['basic'=>'HTTP Basic','bearer'=>'Bearer Token','form_post'=>'Formulaire POST','cookie'=>'Cookie session'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('auth_type')===$val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">URL de login</label>
                            <input type="text" name="auth_url" value="{{ old('auth_url') }}"
                                placeholder="https://example.com/login"
                                class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Identifiant (chiffré)</label>
                                <input type="text" name="auth_credential" value="{{ old('auth_credential') }}"
                                    placeholder="monitoring@exemple.com"
                                    class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 placeholder-gray-400 dark:placeholder-gray-500">
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Compte dédié au monitoring</p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Mot de passe (chiffré)</label>
                                <input type="password" name="auth_password"
                                    placeholder="••••••••"
                                    class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Keyword post-login (succès)</label>
                            <input type="text" name="auth_success_keyword" value="{{ old('auth_success_keyword') }}"
                                placeholder="Texte visible uniquement après login réussi..."
                                class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 placeholder-gray-400 dark:placeholder-gray-500">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Texte visible uniquement après connexion réussie</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.getElementById('auth_enabled').addEventListener('change', function() {
        document.getElementById('auth_fields').classList.toggle('hidden', !this.checked);
    });
</script>
@endsection