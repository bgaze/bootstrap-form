<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * The <x-bf::file> component. Delegates to BF::file() (which takes no value).
 */
class File extends BootstrapComponent
{
    public function __construct(
        public string $name,
        public mixed $label = null,
    ) {}

    protected function renderHtml(array $data): string
    {
        return (string) BF::file(
            $this->name,
            $this->resolveLabel($data, $this->label),
            $this->withAddonSlots($data, $this->bootstrapOptions()),
        );
    }
}
