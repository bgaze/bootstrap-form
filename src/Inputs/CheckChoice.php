<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;
use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property string $tag
 * @property array  $choices
 * @property bool   $inline
 * @property bool   $custom
 * @property bool   $switch
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

    public function __construct(string $name, mixed $label = null, array $choices = [], mixed $checked = [], array $options = [])
    {
        parent::__construct($name, $label, $checked, $options);
        $this->choices = $choices;
    }

    public static function make(string $name, mixed $label = null, array $choices = [], mixed $checked = [], array $options = []): static
    {
        return new static($name, $label, $choices, $checked, $options);
    }

    protected function setLabelAttributes(array $options): void
    {
        parent::setLabelAttributes($options);

        if ($this->layout === 'horizontal') {
            $this->label_attributes->addClass($this->driver->checkChoiceLabelClass());
        }
    }

    /**
     * Choice collections always render their validation feedback as a block.
     */
    protected function feedbackIsBlock(): bool
    {
        return true;
    }

    public function input(): string
    {
        $inputs = '';

        foreach ($this->choices as $value => $label) {
            $checked = in_array($value, (array) $this->value);

            $options = $this->settings
                ->except(['choices', 'name', 'value', 'label'])
                // all(): raw items — keep any LITERAL_PREFIX ('~') intact so the
                // escape survives into each generated child control.
                ->merge($this->input_attributes->all())
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

            $inputs .= CheckInput::make($this->name, $label, $value, $checked, $options);
        }

        return $inputs;
    }
}
