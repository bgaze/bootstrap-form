<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * The <x-bf::hidden> component. Delegates to BF::hidden() (a bare input, no group/label).
 */
class Hidden extends BootstrapComponent
{
    public function __construct(
        public string $name,
        public mixed $value = null,
    ) {}

    protected function renderHtml(array $data): string
    {
        return (string) BF::hidden($this->name, $this->value, $this->bootstrapOptions());
    }
}
