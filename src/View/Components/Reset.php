<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::reset> component. Delegates to BF::reset().
 */
class Reset extends ButtonComponent
{
    protected string $type = 'reset';
}
