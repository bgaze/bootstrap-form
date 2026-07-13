<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::radio> component. Delegates to BF::radio().
 */
class Radio extends Checkable
{
    protected string $type = 'radio';
}
