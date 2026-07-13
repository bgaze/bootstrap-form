<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support\Facades;

use Illuminate\Support\Facades\Facade;

class BF extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bootstrap_form';
    }
}
