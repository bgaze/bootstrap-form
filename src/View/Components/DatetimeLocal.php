<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

/**
 * The <x-bf::datetime-local> component. Delegates to BF::datetimeLocal().
 */
class DatetimeLocal extends InputComponent
{
    protected string $type = 'datetimeLocal';
}
