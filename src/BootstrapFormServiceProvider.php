<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm;

use Bgaze\BootstrapForm\Support\FieldValue;
use Bgaze\BootstrapForm\Support\FormContext;
use Bgaze\BootstrapForm\Support\FormElements;
use Bgaze\BootstrapForm\Support\Html;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Kernel as FoundationHttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BootstrapFormServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'bootstrap_form');

        $this->app->singleton('bootstrap_form', function ($app) {
            // Resolve the ConvertEmptyStringsToNull middleware presence once (stable per
            // request) instead of scanning the kernel on every field value resolution.
            $kernel = $app->bound(HttpKernel::class) ? $app->make(HttpKernel::class) : null;
            $convertsEmptyStrings = $kernel instanceof FoundationHttpKernel
                && $kernel->hasMiddleware(ConvertEmptyStringsToNull::class);

            $context = new FormContext(
                $app['url'],
                $app['view'],
                $app['session.store'],
                $app['session.store']->token(),
                $convertsEmptyStrings,
            );

            $html = new Html($app['url']);
            $fieldValue = new FieldValue($context);
            $elements = new FormElements($html, $fieldValue, $context);

            return new BootstrapForm($html, $elements, $fieldValue, $context);
        });
    }

    public function boot(): void
    {
        $this->publishes([__DIR__.'/config/config.php' => config_path('bootstrap_form.php')], 'config');

        if (config('bootstrap_form.blade_directives', true)) {
            $this->registerBladeDirectives();
        }
    }

    protected function registerBladeDirectives(): void
    {
        $functions = [
            'open', 'close', 'vertical', 'inline', 'horizontal', 'floating',
            'text', 'email', 'url', 'tel', 'number', 'date', 'time', 'datetimeLocal', 'month', 'week', 'search',
            'textarea', 'password', 'color',
            'file', 'hidden', 'select', 'range',
            'checkbox', 'checkboxes', 'radio', 'radios',
            'label', 'submit', 'reset', 'button', 'link',
        ];

        foreach ($functions as $f) {
            Blade::directive($f, fn (string $expression): string => "<?= BF::{$f}({$expression}); ?>");
        }
    }
}
