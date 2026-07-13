<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * The <x-bf::select> component. Delegates to BF::select(). Choices are passed as an array via
 * :choices, the selected value(s) via :selected.
 */
class Select extends BootstrapComponent
{
    /**
     * @param  array<array-key, mixed>  $choices
     */
    public function __construct(
        public string $name,
        public mixed $label = null,
        public array $choices = [],
        public mixed $selected = null,
    ) {}

    protected function renderHtml(array $data): string
    {
        return (string) BF::select($this->name, $this->label, $this->choices, $this->selected, $this->bootstrapOptions());
    }
}
