<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Incident extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'application_id', 'started_at', 'resolved_at',
        'duration_seconds', 'root_cause', 'notes',
        'acknowledged_by', 'acknowledged_at', 'is_resolved',
    ];

    protected $casts = [
        'started_at'       => 'datetime',
        'resolved_at'      => 'datetime',
        'acknowledged_at'  => 'datetime',
        'is_resolved'      => 'boolean',
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

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'incident_id');
    }

    // Helpers
    public function acknowledge(User $user): void
    {
        $this->update([
            'acknowledged_by' => $user->id,
            'acknowledged_at' => now(),
        ]);
    }

    public function resolve(): void
    {
        $duration = $this->started_at->diffInSeconds(now());
        $this->update([
            'is_resolved'      => true,
            'resolved_at'      => now(),
            'duration_seconds' => $duration,
        ]);
    }
}