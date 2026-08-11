@extends('layouts.app')

@section('title', 'Maintenances — M-Monitoring')

@section('page-title', 'Maintenances')
@section('page-subtitle', 'Dashboard / Maintenances planifiées')

@section('page-actions')
<button onclick="toggleForm()"
    class="flex items-center gap-2 bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
    Planifier
</button>
@endsection

@section('content')

{{-- Formulaire caché --}}
<div id="maintenance-form-container" class="hidden mb-6">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Planifier une maintenance</h2>

        <form method="POST" action="{{ route('maintenances.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                {{-- Application --}}
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Application <span class="text-red-500">*</span>
                    </label>
                    <select name="application_id"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('application_id') border-red-500 @enderror">
                        <option value="">-- Sélectionner --</option>
                        @foreach($applications as $app)
                        <option value="{{ $app->id }}" @selected(old('application_id')===$app->id)>
                            {{ $app->name }} {{ $app->group_name ? '('.$app->group_name.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('application_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Début --}}
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Début <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('starts_at') border-red-500 @enderror">
                    @error('starts_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Fin --}}
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Fin <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('ends_at') border-red-500 @enderror">
                    @error('ends_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Motif --}}
            <div class="mb-4">
                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                    Motif <span class="text-red-500">*</span>
                </label>
                <input type="text" name="reason" value="{{ old('reason') }}"
                    placeholder="Ex: Migration base de données, Mise à jour serveur..."
                    class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 placeholder-gray-400 dark:placeholder-gray-500 @error('reason') border-red-500 @enderror">
                @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Note info --}}
            <div class="flex items-start gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-5">
                <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 mt-1"></span>
                <p class="text-xs text-blue-700 dark:text-blue-300">
                    Pendant la fenêtre de maintenance, les checks continuent mais aucun email ne sera envoyé.
                    Une alerte sera envoyée si l'application reste DOWN à la fin de la maintenance.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                    Enregistrer la maintenance
                </button>
                <button type="button" onclick="toggleForm()"
                    class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg text-sm transition">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Tableau des maintenances --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Maintenances planifiées et passées</h3>
        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $maintenances->total() }} maintenance(s)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Application</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Motif</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Début</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Fin</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Créée par</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($maintenances as $maintenance)
                @php
                $now = now();
                if ($now->between($maintenance->starts_at, $maintenance->ends_at)) {
                $statusLabel = 'EN COURS';
                $statusClass = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400';
                } elseif ($now->lt($maintenance->starts_at)) {
                $statusLabel = 'PLANIFIÉE';
                $statusClass = 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400';
                } else {
                $statusLabel = 'TERMINÉE';
                $statusClass = 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400';
                }
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                    {{-- Application --}}
                    <td class="px-5 py-4">
                        <a href="{{ route('applications.show', $maintenance->application) }}"
                            class="font-semibold text-gray-800 dark:text-gray-100 hover:text-teal-600 dark:hover:text-teal-400">
                            {{ $maintenance->application->name }}
                        </a>
                        @if($maintenance->application->group_name)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $maintenance->application->group_name }}</p>
                        @endif
                    </td>

                    {{-- Motif --}}
                    <td class="px-5 py-4 text-gray-600 dark:text-gray-400">
                        {{ $maintenance->reason ?? '—' }}
                    </td>

                    {{-- Début --}}
                    <td class="px-5 py-4 text-sm font-mono text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        {{ $maintenance->starts_at->format('d/m H:i') }}
                    </td>

                    {{-- Fin --}}
                    <td class="px-5 py-4 text-sm font-mono text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        {{ $maintenance->ends_at->format('d/m H:i') }}
                    </td>

                    {{-- Créée par --}}
                    <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                        {{ $maintenance->creator?->name ?? '—' }}
                    </td>

                    {{-- Statut --}}
                    <td class="px-5 py-4">
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">

                            @if($statusLabel === 'PLANIFIÉE')
                            {{-- Modifier --}}
                            <button onclick="openEditModal('{{ $maintenance->id }}','{{ $maintenance->application_id }}','{{ $maintenance->starts_at->format('Y-m-d') }}T{{ $maintenance->starts_at->format('H:i') }}','{{ $maintenance->ends_at->format('Y-m-d') }}T{{ $maintenance->ends_at->format('H:i') }}','{{ addslashes($maintenance->reason) }}')"
                                class="px-3 py-1.5 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded text-xs font-medium transition">
                                Modifier
                            </button> {{-- Annuler --}}
                            <form method="POST" action="{{ route('maintenances.destroy', $maintenance) }}"
                                onsubmit="return confirm('Annuler cette maintenance ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1.5 border border-red-200 dark:border-red-900/50 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded text-xs font-medium transition">
                                    Annuler
                                </button>
                            </form>

                            @elseif($statusLabel === 'EN COURS')
                            {{-- Marquer terminée --}}
                            <form method="POST" action="{{ route('maintenances.finish', $maintenance) }}"
                                onsubmit="return confirm('Marquer cette maintenance comme terminée maintenant ?')">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 border border-green-200 dark:border-green-900/50 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded text-xs font-medium transition">
                                    Terminer
                                </button>
                            </form>
                            {{-- Annuler --}}
                            <form method="POST" action="{{ route('maintenances.destroy', $maintenance) }}"
                                onsubmit="return confirm('Annuler cette maintenance en cours ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1.5 border border-red-200 dark:border-red-900/50 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded text-xs font-medium transition">
                                    Annuler
                                </button>
                            </form>

                            @else
                            {{-- Terminée : voir uniquement --}}
                            <a href="{{ route('applications.show', $maintenance->application) }}"
                                class="px-3 py-1.5 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded text-xs transition">
                                Voir
                            </a>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-gray-400 dark:text-gray-500 py-12">
                        Aucune maintenance planifiée.
                        <button onclick="toggleForm()" class="text-teal-500 hover:underline ml-1">Planifier la première.</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($maintenances->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
        {{ $maintenances->links() }}
    </div>
    @endif
</div>


{{-- Modal modification maintenance PLANIFIÉE --}}
<div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9998;align-items:center;justify-content:center;">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 w-full max-w-lg mx-4">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-5">Modifier la maintenance</h3>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="application_id" id="edit_application_id">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Début</label>
                        <input type="datetime-local" name="starts_at" id="edit_starts_at"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Fin</label>
                        <input type="datetime-local" name="ends_at" id="edit_ends_at"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Motif <span class="text-red-500">*</span></label>
                    <input type="text" name="reason" id="edit_reason"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Annuler
                </button>
                <button type="submit"
                    class="px-5 py-2 bg-teal-500 hover:bg-teal-600 text-white rounded-lg text-sm font-medium transition">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>



<script>
function toggleForm() {
    const container = document.getElementById('maintenance-form-container');
    const isHidden = container.classList.contains('hidden');
    container.classList.toggle('hidden');
    if (isHidden) {
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function openEditModal(id, appId, startsAt, endsAt, reason) {
    document.getElementById('edit-form').action = '/maintenances/' + id;
    document.getElementById('edit_application_id').value = appId;
    document.getElementById('edit_starts_at').value = startsAt;
    document.getElementById('edit_ends_at').value = endsAt;
    document.getElementById('edit_reason').value = reason;
    document.getElementById('edit-modal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

@if($errors->any())
<script>
    document.getElementById('maintenance-form-container').classList.remove('hidden');
</script>
@endif

@endsection