<?php

namespace Bgaze\BootstrapForm\Tests;

/**
 * Base for the frozen Bootstrap 4 compatibility suites.
 *
 * Bootstrap 5 is the package default (see config). Bootstrap 4 is frozen and kept for
 * backward compatibility, so these suites pin the version to 4 explicitly — locking the
 * historical Bootstrap 4 markup independently of the default. Their golden files and
 * expected strings are the proven B4 baseline and must not drift.
 */
abstract class Bootstrap4TestCase extends TestCase
{
    protected function defineEnvironment($app)
    {
        $app['config']->set('bootstrap_form.bootstrap_version', 4);
    }
}
