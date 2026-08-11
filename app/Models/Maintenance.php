<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Maintenance extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'application_id', 'title',
        'starts_at', 'ends_at',
        'reason', 'is_active', 'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_active' => 'boolean',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return now()->between($this->starts_at, $this->ends_at);
    }
}