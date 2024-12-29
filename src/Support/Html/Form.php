<?php

namespace Bgaze\BootstrapForm\Support\Html;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class Form extends HtmlElement
{

    public function __construct(array $attributes = [])
    {
        parent::__construct('form', $attributes);
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