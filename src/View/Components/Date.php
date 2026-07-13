<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::date> component. Delegates to BF::date().
 */
class Date extends InputComponent
{
    protected string $type = 'date';
}
