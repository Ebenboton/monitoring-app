@extends('layouts.app')

@section('title', 'Audit Logs — M-Monitoring')

@section('page-title', 'Audit Logs')
@section('page-subtitle', 'Dashboard / Historique des actions')

@section('content')

{{-- Filtres --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('audit-logs.index') }}" class="flex flex-wrap gap-3 items-end">

        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Utilisateur</label>
            <select name="user_id" onchange="this.form.submit()"
                class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                <option value="">Tous les utilisateurs</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" @selected(request('user_id')===$user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Action</label>
            <input type="text" name="action" value="{{ request('action') }}"
                placeholder="Ex: app.created, user.deleted..."
                class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm w-48 focus:outline-none focus:ring-1 focus:ring-blue-400 placeholder-gray-400 dark:placeholder-gray-500">
        </div>

        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Ressource</label>
            <select name="resource_type" onchange="this.form.submit()"
                class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                <option value="">Toutes</option>
                @foreach($resourceTypes as $type)
                <option value="{{ $type }}" @selected(request('resource_type')===$type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
            Filtrer
        </button>

        @if(request('user_id') || request('action') || request('resource_type'))
        <a href="{{ route('audit-logs.index') }}"
            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 py-2">
            Réinitialiser
        </a>
        @endif
    </form>
</div>

{{-- Tableau --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Historique des actions</h3>
        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $logs->total() }} entrée(s)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Date/Heure</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Utilisateur</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Action</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Ressource</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Détails</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                // Couleur selon le type d'action
                $actionColor = match(true) {
                str_contains($log->action, 'created') => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                str_contains($log->action, 'updated') => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                str_contains($log->action, 'deleted') => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                str_contains($log->action, 'login') => 'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400',
                str_contains($log->action, 'reset') => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
                default => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
                };
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                    {{-- Date --}}
                    <td class="px-5 py-3 text-xs font-mono text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>

                    {{-- Utilisateur --}}
                    <td class="px-5 py-3">
                        @if($log->user)
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-semibold">{{ strtoupper(substr($log->user->name, 0, 1)) }}</span>
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $log->user->name }}</span>
                        </div>
                        @else
                        <span class="text-gray-400 dark:text-gray-500 text-xs">Système</span>
                        @endif
                    </td>

                    {{-- Action --}}
                    <td class="px-5 py-3">
                        <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold {{ $actionColor }}">
                            {{ $log->action }}
                        </span>
                    </td>

                    {{-- Ressource --}}
                    <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">{{ ucfirst($log->resource_type) }}</span>
                        @if($log->resource_id)
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-mono truncate max-w-xs">
                            {{ substr($log->resource_id, 0, 8) }}...
                        </p>
                        @endif
                    </td>

                    {{-- Détails --}}
                    <td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400 max-w-xs">
                        @if($log->payload)
                        @foreach($log->payload as $key => $value)
                        <span class="font-medium">{{ $key }}</span>: {{ is_array($value) ? json_encode($value) : $value }}<br>
                        @endforeach
                        @else
                        <span class="text-gray-300 dark:text-gray-600">—</span>
                        @endif
                    </td>

                    {{-- IP --}}
                    <td class="px-5 py-3 text-xs font-mono text-gray-400 dark:text-gray-500">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-400 dark:text-gray-500 py-12">
                        Aucune action enregistrée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-400 dark:text-gray-500">
            {{ $logs->firstItem() }}–{{ $logs->lastItem() }} sur {{ $logs->total() }}
        </p>
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection