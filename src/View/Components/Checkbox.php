<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::checkbox> component. Delegates to BF::checkbox() (value defaults to 1).
 */
class Checkbox extends Checkable
{
    protected string $type = 'checkbox';

    protected mixed $defaultValue = 1;
}
