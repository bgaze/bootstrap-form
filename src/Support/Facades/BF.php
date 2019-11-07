<?php

namespace Bgaze\BootstrapForm\Support\Facades;

use Illuminate\Support\Facades\Facade;

class BF extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'bootstrap_form';
    }

}
