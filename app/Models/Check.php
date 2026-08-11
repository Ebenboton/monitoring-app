<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Check extends Model
{
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'application_id', 'status', 'http_code',
        'response_time_ms', 'keyword_ok', 'auth_ok',
        'ssl_days_remaining', 'ssl_valid',
        'error_message', 'checked_at',
    ];

    protected $casts = [
        'keyword_ok'  => 'boolean',
        'auth_ok'     => 'boolean',
        'ssl_valid'   => 'boolean',
        'checked_at'  => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($model) => $model->id = (string) Str::uuid());
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}