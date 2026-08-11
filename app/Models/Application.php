<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Application extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name', 'url', 'method', 'accepted_codes',
        'check_interval_seconds', 'timeout_ms',
        'latency_warn_ms', 'latency_down_ms',
        'keyword_expected', 'keyword_forbidden',
        'ssl_check', 'ssl_alert_days', 'retry_count',
        'auth_enabled', 'auth_type', 'auth_url',
        'auth_credential', 'auth_password', 'auth_success_keyword',
        'headers', 'group_name', 'tags',
        'current_status', 'is_active', 'last_checked_at',
        'created_by',
    ];

    protected $casts = [
        'ssl_check'        => 'boolean',
        'auth_enabled'     => 'boolean',
        'is_active'        => 'boolean',
        'headers'          => 'array',
        'tags'             => 'array',
        'last_checked_at'  => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($model) => $model->id = (string) Str::uuid());
    }

    // Relations
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checks()
    {
        return $this->hasMany(Check::class, 'application_id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class, 'application_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'application_id');
    }

    public function escalationRules()
    {
        return $this->hasMany(EscalationRule::class, 'application_id');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'application_id');
    }

    // Helpers
    public function isUnderMaintenance(): bool
    {
        return $this->maintenances()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->exists();
    }

    public function latestCheck()
    {
        return $this->hasOne(Check::class, 'application_id')->latestOfMany('checked_at');
    }
}