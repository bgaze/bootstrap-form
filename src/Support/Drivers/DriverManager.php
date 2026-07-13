<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support\Drivers;

/**
 * Resolves and memoizes the version driver for a given Bootstrap version.
 */
class DriverManager
{
    /**
     * Resolved driver instances, keyed by version.
     *
     * @var array<int, VersionDriver>
     */
    protected static array $drivers = [];

    /**
     * Get the driver for the given Bootstrap version (defaults to 4 for any unknown value).
     */
    public static function make(int $version): VersionDriver
    {
        $version = $version === 5 ? 5 : 4;

        return static::$drivers[$version] ??= match ($version) {
            5 => new Bootstrap5Driver,
            default => new Bootstrap4Driver,
        };
    }
}
