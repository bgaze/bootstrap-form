<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::url> component. Delegates to BF::url().
 */
class Url extends InputComponent
{
    protected string $type = 'url';
}
