<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * Base for the checkable collection components (checkboxes, radios). Choices are passed as
 * an array via :choices, the checked state via :checked.
 */
abstract class Choice extends BootstrapComponent
{
    protected string $type = 'checkboxes';

    /**
     * @param  array<array-key, mixed>  $choices
     */
    public function __construct(
        public string $name,
        public mixed $label = null,
        public array $choices = [],
        public mixed $checked = null,
    ) {}

    protected function renderHtml(array $data): string
    {
        return (string) BF::{$this->type}($this->name, $this->resolveLabel($data, $this->label), $this->choices, $this->checked, $this->bootstrapOptions());
    }
}
