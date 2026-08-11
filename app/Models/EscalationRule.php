<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EscalationRule extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'application_id',
        'level',
        'delay_minutes',
        'recipient_email',
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