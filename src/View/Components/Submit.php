<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::submit> component. Delegates to BF::submit().
 */
class Submit extends ButtonComponent
{
    protected string $type = 'submit';
}
