<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support\Facades;

use Bgaze\BootstrapForm\BootstrapForm;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin BootstrapForm
 */
class BF extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bootstrap_form';
    }
}
