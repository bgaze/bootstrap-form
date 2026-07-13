<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::number> component. Delegates to BF::number().
 */
class Number extends InputComponent
{
    protected string $type = 'number';
}
