<!-- @extends('layouts.app')

@section('title', 'Planifier une maintenance — M-Monitoring')

@section('page-title', 'Planifier une maintenance')
@section('page-subtitle', 'Dashboard / Maintenances / Nouvelle')

@section('page-actions')
    <a href="{{ route('maintenances.index') }}"
       class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition">
        Annuler
    </a>
    <button type="submit" form="maintenance-form"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Planifier
    </button>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    <form id="maintenance-form" method="POST" action="{{ route('maintenances.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Informations de la maintenance</h2>
            <div class="space-y-4">

                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Application <span class="text-red-500">*</span>
                    </label>
                    <select name="application_id"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('application_id') border-red-500 @enderror">
                        <option value="">-- Sélectionner une application --</option>
                        @foreach($applications as $app)
                        <option value="{{ $app->id }}" @selected(old('application_id')===$app->id)>
                            {{ $app->name }} {{ $app->group_name ? '('.$app->group_name.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('application_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        placeholder="Ex: Mise à jour serveur, Migration base de données..."
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('title') border-red-500 @enderror">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                            Début <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('starts_at') border-red-500 @enderror">
                        @error('starts_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                            Fin <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('ends_at') border-red-500 @enderror">
                        @error('ends_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Raison (optionnel)</label>
                    <textarea name="reason" rows="3"
                        placeholder="Décrivez la raison de cette maintenance..."
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none placeholder-gray-400 dark:placeholder-gray-500">{{ old('reason') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Info box --}}
        <div class="flex items-start gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-700 dark:text-blue-300">
                <p class="font-medium mb-1">Pendant la fenêtre de maintenance</p>
                <ul class="text-xs space-y-1 text-blue-600 dark:text-blue-400">
                    <li>• Les checks continuent normalement</li>
                    <li>• Aucun email d'alerte ne sera envoyé</li>
                    <li>• Le statut affiché sera MAINTENANCE (gris)</li>
                    <li>• Si l'app est DOWN à la fin → alerte immédiate</li>
                </ul>
            </div>
        </div>
    </form>
</div>
@endsection -->