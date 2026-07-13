<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * Base for the single checkable field components (checkbox, radio).
 *
 * $defaultValue preserves the facade default when the value attribute is omitted
 * (checkbox => 1, radio => null), so <x-bf::checkbox name="x"/> === BF::checkbox('x').
 */
abstract class Checkable extends BootstrapComponent
{
    protected string $type = 'checkbox';

    protected mixed $defaultValue = null;

    public function __construct(
        public string $name,
        public mixed $label = null,
        public mixed $value = null,
        public mixed $checked = null,
    ) {}

    protected function renderHtml(array $data): string
    {
        $value = $this->value ?? $this->defaultValue;

        return (string) BF::{$this->type}($this->name, $this->label, $value, $this->checked, $this->bootstrapOptions());
    }
}
