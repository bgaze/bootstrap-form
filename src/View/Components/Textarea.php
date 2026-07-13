<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::textarea> component. Delegates to BF::textarea().
 */
class Textarea extends InputComponent
{
    protected string $type = 'textarea';
}
