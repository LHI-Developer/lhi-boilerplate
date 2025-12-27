<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\SystemSetting;

/**
 * SettingService
 * 
 * Service for managing system settings with caching support.
 */
class SettingService
{
    /**
     * Cache key prefix for settings.
     */
    private const CACHE_PREFIX = 'system_setting_';

    /**
     * Cache TTL in seconds (1 hour).
     */
    private const CACHE_TTL = 3600;

    /**
     * Get a setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            self::CACHE_PREFIX . $key,
            self::CACHE_TTL,
            fn() => SystemSetting::getValue($key, $default)
        );
    }

    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return SystemSetting
     */
    public function set(string $key, mixed $value): SystemSetting
    {
        $setting = SystemSetting::setValue($key, $value);

        // Clear cache for this key
        Cache::forget(self::CACHE_PREFIX . $key);

        return $setting;
    }

    /**
     * Check if a setting exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return SystemSetting::where('key', $key)->exists();
    }

    /**
     * Delete a setting.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        Cache::forget(self::CACHE_PREFIX . $key);

        return SystemSetting::where('key', $key)->delete() > 0;
    }

    /**
     * Get all settings as an array.
     *
     * @return array
     */
    public function all(): array
    {
        return SystemSetting::all()
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Clear all settings cache.
     *
     * @return void
     */
    public function clearCache(): void
    {
        $keys = SystemSetting::pluck('key');

        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }
    }
}
