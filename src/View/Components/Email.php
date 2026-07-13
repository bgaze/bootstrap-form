<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::email> component. Delegates to BF::email().
 */
class Email extends InputComponent
{
    protected string $type = 'email';
}
