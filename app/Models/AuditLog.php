<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'action', 'resource_type',
        'resource_id', 'payload', 'ip_address', 'created_at',
    ];

    protected $casts = [
        'payload'    => 'array',
        'created_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($model) => $model->id = (string) Str::uuid());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function log(
        string $action,
        string $resourceType,
        ?string $resourceId = null,
        ?array $payload = null
    ): void {
        static::create([
            'user_id'       => auth()->user()?->id,  // corrigé
            'action'        => $action,
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'payload'       => $payload,
            'ip_address'    => request()->ip(),
            'created_at'    => now(),
        ]);
    }
}