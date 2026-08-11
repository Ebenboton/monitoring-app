@extends('layouts.app')

@section('title', 'Paramètres — M-Monitoring')

@section('page-title', 'Paramètres système')
@section('page-subtitle', 'Dashboard / Paramètres')

@section('page-actions')
<button type="submit" form="settings-form"
    class="flex items-center gap-2 bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
    Sauvegarder
</button>
@endsection

@section('content')

<form method="POST" action="{{ route('settings.test-email') }}" class="flex gap-3">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- COLONNE GAUCHE --}}
        <div class="space-y-6">

            {{-- Serveur SMTP --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Serveur SMTP</h2>
                <div class="space-y-4">

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Hôte SMTP</label>
                        <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}"
                            placeholder="smtp.gmail.com"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('mail_host') border-red-500 @enderror">
                        @error('mail_host')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Port</label>
                            <input type="number" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}"
                                class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Chiffrement</label>
                            <select name="mail_encryption"
                                class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                                <option value="tls" @selected(($settings['mail_encryption'] ?? 'tls' )==='tls' )>TLS</option>
                                <option value="ssl" @selected(($settings['mail_encryption'] ?? '' )==='ssl' )>SSL</option>
                                <option value="none" @selected(($settings['mail_encryption'] ?? '' )==='none' )>Aucun</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Utilisateur SMTP</label>
                        <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}"
                            placeholder="votre.email@gmail.com"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Mot de passe SMTP</label>
                        <input type="password" name="mail_password"
                            placeholder="Laisser vide pour ne pas modifier"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Expéditeur (From)</label>
                        <input type="text" name="mail_from" value="{{ $settings['mail_from'] ?? '' }}"
                            placeholder="M-Monitoring &lt;monitoring@exemple.com&gt;"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>
                </div>
            </div>

            {{-- Test email --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Test de configuration</h2>
                <form method="POST" action="{{ route('settings.test-email') }}" class="flex gap-3">
                    @csrf
                    <input type="email" name="test_email" placeholder="Email de destination du test"
                        class="flex-1 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 placeholder-gray-400 dark:placeholder-gray-500">
                    <button type="submit"
                        class="flex items-center gap-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2.5 rounded-lg text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Envoyer un test
                    </button>
                </form>
                @error('test_email')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- COLONNE DROITE --}}
        <div class="space-y-6">

            {{-- Destinataires par défaut --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Destinataires par défaut</h2>
                <div class="space-y-4">

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Email admin principal (Niveau 1)</label>
                        <input type="email" name="alert_email_level1" value="{{ $settings['alert_email_level1'] ?? '' }}"
                            placeholder="admin@organisation.com"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 placeholder-gray-400 dark:placeholder-gray-500">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Reçoit toutes les alertes de niveau 1</p>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Email responsable (Niveau 2 — escalade)</label>
                        <input type="email" name="alert_email_level2" value="{{ $settings['alert_email_level2'] ?? '' }}"
                            placeholder="responsable@organisation.com"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 placeholder-gray-400 dark:placeholder-gray-500">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Délai d'escalade niveau 2 (minutes)</label>
                        <input type="number" name="alert_delay_level2" value="{{ $settings['alert_delay_level2'] ?? '15' }}"
                            min="1"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Email direction (Niveau 3)</label>
                        <input type="email" name="alert_email_level3" value="{{ $settings['alert_email_level3'] ?? '' }}"
                            placeholder="direction@organisation.com"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 placeholder-gray-400 dark:placeholder-gray-500">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Délai d'escalade niveau 3 (minutes)</label>
                        <input type="number" name="alert_delay_level3" value="{{ $settings['alert_delay_level3'] ?? '30' }}"
                            min="1"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>
                </div>
            </div>

            {{-- Paramètres globaux surveillance --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Paramètres globaux de surveillance</h2>
                <div class="space-y-4">

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Fréquence par défaut (secondes)</label>
                        <input type="number" name="default_check_interval" value="{{ $settings['default_check_interval'] ?? '60' }}"
                            min="30"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Retries avant alerte (défaut)</label>
                        <input type="number" name="default_retry_count" value="{{ $settings['default_retry_count'] ?? '3' }}"
                            min="1" max="10"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Rétention des checks (jours)</label>
                        <input type="number" name="check_retention_days" value="{{ $settings['check_retention_days'] ?? '90' }}"
                            min="7" max="365"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>

                    {{-- Toggle alerte rétablissement --}}
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Alerte de rétablissement</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Envoyer un email quand une app revient UP</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="send_recovery_alert" value="1"
                                class="sr-only peer"
                                @checked(($settings['send_recovery_alert'] ?? '1' )==='1' )>
                            <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:ring-2 peer-focus:ring-teal-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection