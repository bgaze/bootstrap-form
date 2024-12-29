<?php

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Html\Html;
use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Support\Traits\HasAddons;
use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property string $type
 * @property string $size
 */
class TextInput extends Input
{
    use HasAddons;

    protected function defaults(): Collection
    {
        return parent::defaults()->except('custom')->merge([
            'type' => 'text',
            'size' => null,
            'prepend' => false,
            'append' => false,
        ]);
    }

    public static function make(string $name, $label = null, $value = null, array $options = []): static
    {
        return new static($name, $label, $value, $options);
    }

    protected function setInputAttributes(array $options): void
    {
        parent::setInputAttributes($options);

        $this->input_attributes->addClass('form-control');

        if ($this->size === 'sm' || $this->size === 'lg') {
            $this->input_attributes->addClass('form-control-' . $this->size);
        }
    }

    public function input(): string
    {
        $input = ($this->type === 'textarea') ? Html::textarea() : Html::input();

        $input->attributes($this->input_attributes->toArray())->attribute('name', $this->name);

        match ($this->type) {
            'password' => $input->attribute('type', 'password'),
            'textarea' => $input->append($this->value),
            default => $input->attributes(['type' => $this->type, 'value' => $this->value]),
        };

        return $input->toHtml();
    }
}
