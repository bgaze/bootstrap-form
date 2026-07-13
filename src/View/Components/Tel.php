<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::tel> component. Delegates to BF::tel().
 */
class Tel extends InputComponent
{
    protected string $type = 'tel';
}
