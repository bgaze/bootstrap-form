<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * The <x-bf::password> component. Delegates to BF::password(), which takes no value.
 */
class Password extends InputComponent
{
    protected string $type = 'password';

    protected function renderHtml(array $data): string
    {
        return (string) BF::password($this->name, $this->label, $this->bootstrapOptions());
    }
}
