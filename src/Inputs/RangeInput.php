<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;

/**
 * Specific settings:
 *
 * @property bool $custom
 */
class RangeInput extends Input
{
    public static function make(string $name, mixed $label = null, mixed $value = null, array $options = []): static
    {
        return new static($name, $label, $value, $options);
    }

    protected function setInputAttributes(array $options): void
    {
        parent::setInputAttributes($options);

        $this->input_attributes->addClass($this->driver->rangeClass((bool) $this->custom));
    }

    public function input(): string
    {
        return $this->elements->range($this->name, $this->value, $this->input_attributes->toArray())->toHtml();
    }
}
