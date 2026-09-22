<?php

namespace App\Traits;

use App\Helpers\LaravelVersionHelper;

/**
 * Trait to handle version-specific behavior in models and classes
 *
 * Usage:
 * class MyModel extends Model {
 *     use VersionCompatible;
 * }
 *
 * Then use: $model->ifLaravel11Plus(function () { ... })
 */
trait VersionCompatible
{
    /**
     * Execute callback if running Laravel 10 or higher
     *
     * @param callable $callback
     * @return mixed
     */
    public function ifLaravel10Plus(callable $callback)
    {
        return LaravelVersionHelper::isLaravel10Plus() ? $callback($this) : null;
    }

    /**
     * Execute callback if running Laravel 11 or higher
     *
     * @param callable $callback
     * @return mixed
     */
    public function ifLaravel11Plus(callable $callback)
    {
        return LaravelVersionHelper::isLaravel11Plus() ? $callback($this) : null;
    }

    /**
     * Execute callback if running Laravel 12 or higher
     *
     * @param callable $callback
     * @return mixed
     */
    public function ifLaravel12Plus(callable $callback)
    {
        return LaravelVersionHelper::isLaravel12Plus() ? $callback($this) : null;
    }

    /**
     * Execute callback if running Laravel 13 or higher
     *
     * @param callable $callback
     * @return mixed
     */
    public function ifLaravel13Plus(callable $callback)
    {
        return LaravelVersionHelper::isLaravel13Plus() ? $callback($this) : null;
    }

    /**
     * Execute callback based on specific version
     *
     * @param int $majorVersion
     * @param callable $callback
     * @return mixed
     */
    public function ifVersion(int $majorVersion, callable $callback)
    {
        return LaravelVersionHelper::is($majorVersion) ? $callback($this) : null;
    }

    /**
     * Execute different code based on version
     *
     * @param array $versionCallbacks [10 => function, 11 => function, ...]
     * @return mixed
     */
    public function switchVersion(array $versionCallbacks)
    {
        $majorVersion = LaravelVersionHelper::getMajorVersion();
        
        if (isset($versionCallbacks[$majorVersion])) {
            return $versionCallbacks[$majorVersion]($this);
        }

        return $versionCallbacks['default']($this) ?? null;
    }

    /**
     * Get version-specific model attribute
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getVersionAttribute(string $key, $default = null)
    {
        return LaravelVersionHelper::config($key, $default);
    }
}
