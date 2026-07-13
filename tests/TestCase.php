<?php

namespace Bgaze\BootstrapForm\Tests;

use Bgaze\BootstrapForm\BootstrapFormServiceProvider;
use Bgaze\BootstrapForm\Support\Facades\BF;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Register the package provider.
     */
    protected function getPackageProviders($app)
    {
        return [
            BootstrapFormServiceProvider::class,
        ];
    }

    /**
     * Register the package facade alias.
     */
    protected function getPackageAliases($app)
    {
        return [
            'BF' => BF::class,
        ];
    }

    /**
     * Flash a validation error bag into the session to exercise error rendering.
     *
     * @param  array  $errors  field => message(s)
     */
    protected function withErrors(array $errors, string $bag = 'default'): void
    {
        $viewBag = new ViewErrorBag;
        $viewBag->put($bag, new MessageBag($errors));

        $this->app['session.store']->put('errors', $viewBag);
    }

    /**
     * Flash old input into the session to exercise field re-population.
     *
     * @param  array  $input  field => value(s)
     */
    protected function withOldInput(array $input): void
    {
        $this->app['session.store']->flashInput($input);
    }
}
