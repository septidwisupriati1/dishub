<?php

namespace App\Helpers;

use Illuminate\Foundation\Application;

class LaravelVersionHelper
{
    /**
     * Get the current Laravel version
     *
     * @return string
     */
    public static function getVersion(): string
    {
        return Application::VERSION;
    }

    /**
     * Get major version number (e.g., 10, 11, 12, 13)
     *
     * @return int
     */
    public static function getMajorVersion(): int
    {
        $version = self::getVersion();
        preg_match('/^(\d+)\./', $version, $matches);
        return (int) ($matches[1] ?? 0);
    }

    /**
     * Check if running Laravel 10 or higher
     *
     * @return bool
     */
    public static function isLaravel10Plus(): bool
    {
        return self::getMajorVersion() >= 10;
    }

    /**
     * Check if running Laravel 11 or higher
     *
     * @return bool
     */
    public static function isLaravel11Plus(): bool
    {
        return self::getMajorVersion() >= 11;
    }

    /**
     * Check if running Laravel 12 or higher
     *
     * @return bool
     */
    public static function isLaravel12Plus(): bool
    {
        return self::getMajorVersion() >= 12;
    }

    /**
     * Check if running Laravel 13 or higher
     *
     * @return bool
     */
    public static function isLaravel13Plus(): bool
    {
        return self::getMajorVersion() >= 13;
    }

    /**
     * Check if running specific major version
     *
     * @param int $majorVersion
     * @return bool
     */
    public static function is(int $majorVersion): bool
    {
        return self::getMajorVersion() === $majorVersion;
    }

    /**
     * Get full version info
     *
     * @return array
     */
    public static function getVersionInfo(): array
    {
        $version = self::getVersion();
        $majorVersion = self::getMajorVersion();
        
        return [
            'version' => $version,
            'major' => $majorVersion,
            'is_lts' => in_array($majorVersion, [11]), // Update as new LTS versions are released
            'php_version' => PHP_VERSION,
            'release_date' => self::getReleaseDateForVersion($majorVersion),
        ];
    }

    /**
     * Get the release date for a Laravel version
     *
     * @param int $majorVersion
     * @return string|null
     */
    private static function getReleaseDateForVersion(int $majorVersion): ?string
    {
        $releases = [
            10 => '2023-02-07',
            11 => '2024-03-12',
            12 => '2024-09-03', // Estimated
            13 => '2025-03-12', // Estimated
        ];

        return $releases[$majorVersion] ?? null;
    }

    /**
     * Get version-specific configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function config(string $key, $default = null)
    {
        $majorVersion = self::getMajorVersion();
        $versionSpecificKey = "app.version_specific.v{$majorVersion}.{$key}";
        
        return config($versionSpecificKey, config("app.version_specific.default.{$key}", $default));
    }

    /**
     * Check compatibility with a specific Laravel version range
     *
     * @param string $range (e.g., ">=10,<13" or "11.x")
     * @return bool
     */
    public static function isCompatibleWith(string $range): bool
    {
        // Simple version constraint checker
        $majorVersion = self::getMajorVersion();
        
        if (str_contains($range, 'x')) {
            $version = (int) explode('.', $range)[0];
            return $majorVersion === $version;
        }

        // Handle >=X,<Y format
        if (str_contains($range, ',')) {
            [$min, $max] = explode(',', $range);
            $minVersion = (int) filter_var($min, FILTER_SANITIZE_NUMBER_INT);
            $maxVersion = (int) filter_var($max, FILTER_SANITIZE_NUMBER_INT);
            
            return $majorVersion >= $minVersion && $majorVersion < $maxVersion;
        }

        // Handle >=X or <=X format
        if (str_contains($range, '>=')) {
            $version = (int) filter_var($range, FILTER_SANITIZE_NUMBER_INT);
            return $majorVersion >= $version;
        }

        if (str_contains($range, '<=')) {
            $version = (int) filter_var($range, FILTER_SANITIZE_NUMBER_INT);
            return $majorVersion <= $version;
        }

        return false;
    }

    /**
     * Log version info for debugging
     *
     * @return void
     */
    public static function logVersionInfo(): void
    {
        $info = self::getVersionInfo();
        logger()->info('Laravel Version Info', $info);
    }
}
