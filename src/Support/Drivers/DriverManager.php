<?php

namespace Bgaze\BootstrapForm\Support\Drivers;

/**
 * Resolves and memoizes the version driver for a given Bootstrap version.
 */
class DriverManager
{

    /**
     * Resolved driver instances, keyed by version.
     *
     * @var array
     */
    protected static $drivers = [];

    /**
     * Get the driver for the given Bootstrap version (defaults to 4 for any unknown value).
     *
     * @param  int  $version
     * @return VersionDriver
     */
    public static function make($version)
    {
        $version = ((int) $version === 5) ? 5 : 4;

        if (!isset(static::$drivers[$version])) {
            static::$drivers[$version] = ($version === 5)
                ? new Bootstrap5Driver()
                : new Bootstrap4Driver();
        }

        return static::$drivers[$version];
    }
}
