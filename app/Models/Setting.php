<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Setting
 *
 * Rôle : gère les paramètres système sous forme clé/valeur.
 *
 * Utilisation :
 * - Setting::get('mail_host')           → lit une valeur
 * - Setting::set('mail_host', 'smtp…') → écrit une valeur
 * - Setting::all()                      → retourne tous les paramètres
 */
class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Récupère la valeur d'un paramètre
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);
        return $setting ? $setting->value : $default;
    }

    /**
     * Enregistre la valeur d'un paramètre
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Retourne tous les paramètres sous forme de tableau clé/valeur
     */
    public static function allAsArray(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }
}
