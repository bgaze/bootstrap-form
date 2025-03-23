<?php

namespace Bgaze\BootstrapForm\Bootstrap;

use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property string $tag
 * @property bool $inline
 * @property bool $custom
 * @property bool $switch
 */
class CheckChoice extends AbstractChoiceInput
{
    protected function defaults(): Collection
    {
        return parent::defaults()->merge([
            'tag' => 'checkbox',
            'inline' => false,
            'switch' => false,
        ]);
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
            ->map(fn ($label, $value) => (string) $this->makeCheckInput($value, $label))
            ->join('');
    }

    protected function makeCheckInput($value, $label): CheckInput
    {
        $options = $this->settings
            ->except(['choices', 'name', 'value', 'label'])
            ->merge($this->input_attributes)
            ->merge([
                'layout' => 'vertical',
                'disable_errors' => true,
                'group' => false,
                'help' => false,
                'id' => $this->flattenName($this->name, '_').'_'.$value,
            ])
            ->toArray();

        if ($this->layout === 'inline') {
            $options['inline'] = true;
        }

        return CheckInput::make($this->name, $label, $value, in_array($value, $this->value), $options);
    }
}
