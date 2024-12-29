<?php

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Html\Html;
use Bgaze\BootstrapForm\Support\Html\HtmlElement;
use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Support\Traits\HasAddons;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property array $value
 * @property array $choices
 * @property bool $custom
 * @property string $size
 */
class SelectInput extends Input
{
    use HasAddons;

    protected function defaults(): Collection
    {
        return parent::defaults()->merge([
            'choices' => [],
            'size' => null,
            'prepend' => false,
            'append' => false,
        ]);
    }

    public function __construct(string $name, $label = null, array $choices = [], $selected = null, array $options = [])
    {
        parent::__construct($name, $label, $selected, $options);
        $this->choices = $choices;
    }

    public static function make(string $name, $label = null, array $choices = [], $selected = null, array $options = []): static
    {
        return new static($name, $label, $choices, $selected, $options);
    }

    protected function setValue($value): array
    {
        return Arr::wrap(parent::setValue($value));
    }

    protected function setInputAttributes(array $options): void
    {
        parent::setInputAttributes($options);

        $class = $this->custom ? 'custom-select' : 'form-control';
        $this->input_attributes->addClass($class);

        if ($this->size === 'sm' || $this->size === 'lg') {
            $this->input_attributes->addClass($class . '-' . $this->size);
        }
    }

    public function input(): string
    {
        $select = Html::select($this->input_attributes->toArray())->attribute('name', $this->name);

        foreach ($this->choices as $key => $value) {
            if (is_array($value)) {
                $group = Html::optgroup()->attribute('label', $key)->appendTo($select);
                foreach ($value as $k => $v) {
                    $this->makeOption($k, $v)->appendTo($group);
                }
            } else {
                $this->makeOption($key, $value)->appendTo($select);
            }
        }

        return $select->toHtml();
    }

    protected function makeOption($value, $label): HtmlElement
    {
        return Html::option()
            ->attribute('value', $value)
            ->attribute('selected', in_array($value, $this->value))
            ->append($label);
    }
}
