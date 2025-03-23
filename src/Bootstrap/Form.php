<?php

namespace Bgaze\BootstrapForm\Bootstrap;

use ArrayAccess;
use Bgaze\BootstrapForm\Html\Html;
use Bgaze\BootstrapForm\Html\PlainElement;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class Form extends PlainElement
{
    public function __construct(array|ArrayAccess $attributes = [])
    {
        parent::__construct('form', [
            'role' => 'form',
            'accept-charset' => 'utf-8',
            'method' => 'POST',
        ]);

        $this->attributes->merge($attributes);
    }

    public static function make(array|ArrayAccess $attributes = []): static
    {
        return new static($attributes);
    }

    public function open(): string
    {
        // Force POST method if missing
        $this->attributes->method = Str::upper($this->attributes->method ?? 'POST');

        // Create opening tag
        $html = parent::open();

        // Spoof the form's method
        if (in_array($this->attributes->method, ['DELETE', 'PATCH', 'PUT'])) {
            $html .= Html::input(['type' => 'hidden', 'name' => '_method', 'value' => $this->attributes->method]);
        }

        // On any other method than get, the form needs a CSRF token
        if ($this->attributes->method !== 'GET') {
            $html .= Html::input(['type' => 'hidden', 'name' => '_token', 'value' => Request::session()->token()]);
        }

        return $html;
    }
}
