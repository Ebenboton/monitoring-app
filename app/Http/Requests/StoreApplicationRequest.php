<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Vérifie que l'utilisateur est connecté et est admin
        if (!auth()->check()) {
            return false;
        }
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'                   => 'required|string|max:150',
            'url'                    => ['required', 'string', 'max:500', 'regex:/^https?:\/\/.+/'],
            'method'                 => 'required|in:GET,POST,HEAD',
            'accepted_codes'         => 'required|string|max:50',
            'check_interval_seconds' => 'required|integer|min:30|max:3600',
            'timeout_ms'             => 'required|integer|min:1000|max:30000',
            'latency_warn_ms'        => 'required|integer|min:100',
            'latency_down_ms'        => 'required|integer|min:100',
            'keyword_expected'       => 'nullable|string',
            'keyword_forbidden'      => 'nullable|string',
            'ssl_check'              => 'boolean',
            'ssl_alert_days'         => 'nullable|integer|min:1',
            'retry_count'            => 'required|integer|min:1|max:10',
            'auth_enabled'           => 'boolean',
            'auth_type'              => 'nullable|in:basic,bearer,form_post,cookie',
            'auth_url'               => ['nullable', 'string', 'max:500', 'regex:/^https?:\/\/.+/'],
            'auth_credential'        => 'nullable|string',
            'auth_password'          => 'nullable|string',
            'auth_success_keyword'   => 'nullable|string',
            'headers'                => 'nullable|array',
            'group_name'             => 'nullable|string|max:100',
            'tags'                   => 'nullable|array',
            'is_active'              => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'              => 'Le nom est obligatoire.',
            'url.required'               => 'L\'URL est obligatoire.',
            'url.regex'                  => 'L\'URL doit commencer par http:// ou https://',
            'method.in'                  => 'La méthode doit être GET, POST ou HEAD.',
            'check_interval_seconds.min' => 'La fréquence minimum est 30 secondes.',
            'latency_down_ms.min'        => 'Le seuil DOWN doit être supérieur à 100ms.',
            'auth_type.in'               => 'Le type d\'auth doit être basic, bearer, form_post ou cookie.',
        ];
    }
}