<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'                     => 'sometimes|required|string|max:150',
            'url'                      => ['sometimes', 'required', 'string', 'max:500', 'regex:/^https?:\/\/.+/'],
            'method'                   => 'sometimes|required|in:GET,POST,HEAD',
            'accepted_codes'           => 'sometimes|required|string|max:50',
            'check_interval_seconds'   => 'sometimes|required|integer|min:30|max:3600',
            'timeout_ms'               => 'sometimes|required|integer|min:1000|max:30000',
            'latency_warn_ms'          => 'sometimes|required|integer|min:100',
            'latency_down_ms'          => 'sometimes|required|integer|min:100',
            'keyword_expected'         => 'nullable|string',
            'keyword_forbidden'        => 'nullable|string',
            'ssl_check'                => 'boolean',
            'ssl_alert_days'           => 'nullable|integer|min:1',
            'retry_count'              => 'sometimes|required|integer|min:1|max:10',
            'auth_enabled'             => 'boolean',
            'auth_type'                => 'nullable|in:basic,bearer,form_post,cookie',
            'auth_url'                 => ['nullable', 'string', 'max:500', 'regex:/^https?:\/\/.+/'],
            'auth_credential'          => 'nullable|string',
            'auth_password'            => 'nullable|string',
            'auth_success_keyword'     => 'nullable|string',
            'headers'                  => 'nullable|array',
            'group_name'               => 'nullable|string|max:100',
            'tags'                     => 'nullable|array',
            'is_active'                => 'boolean',
        ];
    }
}