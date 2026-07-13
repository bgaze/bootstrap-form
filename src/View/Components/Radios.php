<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::radios> component. Delegates to BF::radios().
 */
class Radios extends Choice
{
    protected string $type = 'radios';
}
