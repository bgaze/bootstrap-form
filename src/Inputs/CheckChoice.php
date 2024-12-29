<?php

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property string $tag
 * @property array $choices
 * @property bool $inline
 * @property bool $custom
 * @property bool $switch
 */
class CheckChoice extends Input
{
    protected function defaults(): Collection
    {
        return parent::defaults()->merge([
            'tag' => 'checkbox',
            'choices' => [],
            'inline' => false,
            'switch' => false,
        ]);
    }

    public function __construct(string $name, $label = null, array $choices = [], $checked = [], array $options = [])
    {
        parent::__construct($name, $label, $checked, $options);
        $this->choices = $choices;
    }

    public static function make(string $name, $label = null, array $choices = [], $checked = [], array $options = []): static
    {
        return new static($name, $label, $choices, $checked, $options);
    }

    protected function setValue($value): array
    {
        return Arr::wrap(parent::setValue($value));
    }

    protected function setLabelAttributes(array $options): void
    {
        parent::setLabelAttributes($options);

        if ($this->layout === 'horizontal') {
            $this->label_attributes->addClass('pt-0');
        }
    }

    public function input(): string
    {
        return collect($this->choices)
            ->map(fn($label, $value) => (string)$this->makeCheckInput($value, $label))
            ->join('');
    }

    protected function makeCheckInput($value, $label): CheckInput
    {
        $checked = in_array($value, $this->value);

        $options = $this->settings
            ->except(['choices', 'name', 'value', 'label'])
            ->merge($this->input_attributes)
            ->merge([
                'layout' => 'vertical',
                'disable_errors' => true,
                'group' => false,
                'help' => false,
                'id' => $this->flattenName($this->name, '_') . '_' . $value,
            ])
            ->toArray();

        if ($this->layout === 'inline') {
            $options['inline'] = true;
        }

        return CheckInput::make($this->name, $label, $value, $checked, $options);
    }
}
