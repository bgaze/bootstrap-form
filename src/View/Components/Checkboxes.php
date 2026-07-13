<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::checkboxes> component. Delegates to BF::checkboxes().
 */
class Checkboxes extends Choice
{
    protected string $type = 'checkboxes';
}
