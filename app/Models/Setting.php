<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'data_type',
        'description',
    ];

    /**
     * Get a setting value by key
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('setting_key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->setting_value, $setting->data_type);
    }

    /**
     * Set a setting value by key
     * @param string $key
     * @param mixed $value
     * @param string $dataType
     * @return Setting
     */
    public static function set(string $key, mixed $value, string $dataType = 'string'): Setting
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => is_array($value) || is_object($value) ? json_encode($value) : $value,
                'data_type' => $dataType,
            ]
        );
    }

    /**
     * Cast value to proper type
     * @param mixed $value
     * @param string $dataType
     * @return mixed
     */
    protected static function castValue(mixed $value, string $dataType = 'string'): mixed
    {
        return match ($dataType) {
            'integer' => (int) $value,
            'boolean' => (bool) $value,
            'array', 'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Get all settings as key-value array
     * @return array
     */
    public static function getAll(): array
    {
        $settings = [];
        foreach (self::all() as $setting) {
            $settings[$setting->setting_key] = self::castValue($setting->setting_value, $setting->data_type);
        }
        return $settings;
    }

    /**
     * Get the display name shown in the top navbar.
     */
    public static function getApplicationName(): string
    {
        $user = auth()->user();

        if ($user?->merchant?->business_name) {
            return (string) $user->merchant->business_name;
        }

        return (string) self::get('app_name', config('app.name', 'Aktas'));
    }
}
