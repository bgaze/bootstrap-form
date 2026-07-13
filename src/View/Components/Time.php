<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::time> component. Delegates to BF::time().
 */
class Time extends InputComponent
{
    protected string $type = 'time';
}
