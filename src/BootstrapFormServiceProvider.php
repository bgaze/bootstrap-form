<?php

namespace Bgaze\BootstrapForm;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BootstrapFormServiceProvider extends ServiceProvider
{
    protected bool $defer = true;

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'bootstrap_form');

        $this->app->singleton('bootstrap_form', fn ($app) => new BootstrapForm);
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
            'open', 'close', 'vertical', 'inline', 'horizontal',
            'text', 'email', 'url', 'tel', 'number', 'date', 'time', 'textarea', 'password', 'color',
            'file', 'hidden', 'select', 'range',
            'checkbox', 'checkboxes', 'radio', 'radios',
            'label', 'submit', 'reset', 'button', 'link',
        ];

        foreach ($functions as $f) {
            Blade::directive($f, fn ($expression) => sprintf('<?= BF::%s(%s) ?>', $f, $expression));
        }
    }

    public function provides(): array
    {
        return ['bootstrap_form'];
    }
}
