<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::text> component. Delegates to BF::text().
 */
class Text extends InputComponent
{
    protected string $type = 'text';
}
