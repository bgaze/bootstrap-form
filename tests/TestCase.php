<?php

namespace Bgaze\BootstrapForm\Tests;

use Bgaze\BootstrapForm\BootstrapFormServiceProvider;
use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Collective\Html\HtmlServiceProvider;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Register the package (and its HTML/Form dependency) providers.
     */
    protected function getPackageProviders($app)
    {
        return [
            HtmlServiceProvider::class,
            BootstrapFormServiceProvider::class,
        ];
    }

    /**
     * Register the package facade aliases.
     */
    protected function getPackageAliases($app)
    {
        return [
            'BF' => \Bgaze\BootstrapForm\Support\Facades\BF::class,
            'Form' => FormFacade::class,
            'Html' => HtmlFacade::class,
        ];
    }

    /**
     * Flash a validation error bag into the session to exercise error rendering.
     *
     * @param  array  $errors  field => message(s)
     * @param  string  $bag
     */
    protected function withErrors(array $errors, string $bag = 'default'): void
    {
        $viewBag = new ViewErrorBag();
        $viewBag->put($bag, new MessageBag($errors));

        $this->app['session.store']->put('errors', $viewBag);
    }
}
