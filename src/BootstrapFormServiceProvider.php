<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm;

use Bgaze\BootstrapForm\Support\FieldValue;
use Bgaze\BootstrapForm\Support\FormContext;
use Bgaze\BootstrapForm\Support\FormElements;
use Bgaze\BootstrapForm\Support\Html;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BootstrapFormServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'bootstrap_form');

        $this->app->singleton('bootstrap_form', function ($app) {
            $context = new FormContext(
                $app['url'],
                $app['view'],
                $app['session.store'],
                $app['session.store']->token(),
            );

            $html = new Html($app['url']);
            $fieldValue = new FieldValue($context);
            $elements = new FormElements($html, $fieldValue, $context);

            return new BootstrapForm($html, $elements, $fieldValue, $context);
        });
    }

    public function boot(): void
    {
        $this->publishes([__DIR__ . '/config/config.php' => config_path('bootstrap_form.php')], 'config');

        if (config('bootstrap_form.blade_directives', true)) {
            $this->registerBladeDirectives();
        }
    }

    protected function registerBladeDirectives(): void
    {
        $functions = [
            'open', 'close', 'vertical', 'inline', 'horizontal',
            'text', 'email', 'url', 'tel', 'number', 'date', 'time', 'textarea', 'password', 'color',
            'file', 'hidden', 'select', 'range',
            'checkbox', 'checkboxes', 'radio', 'radios',
            'label', 'submit', 'reset', 'button', 'link',
        ];

        foreach ($functions as $f) {
            Blade::directive($f, fn (string $expression): string => "<?= BF::{$f}({$expression}); ?>");
        }
    }
}
