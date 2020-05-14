<?php

namespace Bgaze\BootstrapForm;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BootstrapFormServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Register the service provider.
     */
    public function register()
    {
        // Merge configuration.
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'bootstrap_form');

        // Register service.
        $this->app->singleton('bootstrap_form', function ($app) {
            return new BootstrapForm($app['html'], $app['form']);
        });
    }


    /**
     * Boot the service provider.
     */
    public function boot()
    {
        // Publish configuration.
        $this->publishes([__DIR__ . '/config/config.php' => config_path('bootstrap_form.php')], 'config');

        // Register blade directives if enabled.
        if (config('bootstrap_form.blade_directives', true)) {
            $this->registerBladeDirectives();
        }
    }


    /**
     * Register blade directive.
     */
    protected function registerBladeDirectives()
    {
        $functions = [
            'open', 'close', 'vertical', 'inline', 'horizontal',
            'text', 'email', 'url', 'tel', 'number', 'date', 'time', 'textarea', 'password','color',
            'file', 'hidden', 'select', 'range',
            'checkbox', 'checkboxes', 'radio', 'radios',
            'label', 'submit', 'reset', 'button', 'link',
        ];

        foreach ($functions as $f) {
            Blade::directive($f, function ($expression) use ($f) {
                return "<?= BF::{$f}({$expression}); ?>";
            });
        }
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bootstrap_form'];
    }
}
