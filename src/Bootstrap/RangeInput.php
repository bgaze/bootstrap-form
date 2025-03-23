<?php

namespace Bgaze\BootstrapForm\Bootstrap;

use Bgaze\BootstrapForm\Html\Html;
use Bgaze\BootstrapForm\Bootstrap\AbstractInput;

/**
 * Specific settings:
 *
 * @property bool $custom
 */
class RangeInput extends AbstractInput
{
    public static function make(string $name, $label = null, $value = null, array $options = []): static
    {
        return new static($name, $label, $value, $options);
    }

    protected function setInputAttributes(array $options): void
    {
        parent::setInputAttributes($options);

        if ($this->custom) {
            $this->input_attributes->addClass('custom-range');
        } else {
            $this->input_attributes->addClass('form-control-range');
        }
    }

    public function input(): string
    {
        return Html::input($this->input_attributes)
            ->attribute('type', 'range')
            ->attribute('name', $this->name)
            ->attribute('value', $this->value)
            ->toHtml();
    }
}
