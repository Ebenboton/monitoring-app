<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Alert extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'incident_id', 'application_id', 'recipient_email',
        'subject', 'body', 'alert_type', 'escalation_level',
        'status', 'sent_at', 'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($model) => $model->id = (string) Str::uuid());
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}