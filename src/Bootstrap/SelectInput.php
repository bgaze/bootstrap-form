<?php

namespace Bgaze\BootstrapForm\Bootstrap;

use Bgaze\BootstrapForm\Html\Html;
use Bgaze\BootstrapForm\Html\PlainElement;
use Bgaze\BootstrapForm\Support\HasAddons;
use Illuminate\Support\Collection;

/**
 * @property bool $custom
 * @property string $size
 */
class SelectInput extends AbstractChoiceInput
{
    use HasAddons;

    protected function defaults(): Collection
    {
        return parent::defaults()->merge([
            'size' => null,
            'prepend' => false,
            'append' => false,
        ]);
    }

    protected function setInputAttributes(array $options): void
    {
        parent::setInputAttributes($options);

        $class = $this->custom ? 'custom-select' : 'form-control';
        $this->input_attributes->addClass($class);

        if ($this->size === 'sm' || $this->size === 'lg') {
            $this->input_attributes->addClass($class.'-'.$this->size);
        }
    }

    public function input(): string
    {
        $select = Html::select($this->input_attributes)->attribute('name', $this->name);

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

    protected function makeOption($value, $label): PlainElement
    {
        return Html::option()
            ->attribute('value', $value)
            ->attribute('selected', in_array($value, $this->value))
            ->append($label);
    }
}
