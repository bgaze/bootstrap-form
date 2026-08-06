<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\ChoiceList;
use Bgaze\BootstrapForm\Support\Input;
use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property string $tag
 * @property iterable $choices
 * @property bool $inline
 * @property bool $custom
 * @property bool $switch
 * @property array $option_attributes
 *
 * @phpstan-consistent-constructor
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
            'option_attributes' => [],
        ]);
    }

    public function __construct(string $name, mixed $label = null, iterable $choices = [], mixed $checked = [], array $options = [])
    {
        parent::__construct($name, $label, $checked, $options);
        $this->choices = $choices;
    }

    public static function make(string $name, mixed $label = null, iterable $choices = [], mixed $checked = [], array $options = []): static
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

    /**
     * A collection renders several controls, so there is no single input to carry the
     * aria-describedby / aria-invalid wiring (each child manages its own state).
     */
    protected function hasSingleInput(): bool
    {
        return false;
    }

    public function input(): string
    {
        $inputs = '';

        foreach (ChoiceList::checkables($this->choices, (array) $this->option_attributes) as $choice) {
            $value = $choice['value'];
            $checked = in_array($value, (array) $this->value);

            // Per-item (advanced-form) attributes win over the propagated field attributes.
            // A per-item id overrides the generated one; everything else stays structural.
            $attributes = $choice['attributes'];
            $id = $attributes['id'] ?? $this->flattenName($this->name, '-').'-'.$value;
            unset($attributes['id']);

            $options = $this->settings
                ->except(['choices', 'name', 'value', 'label', 'option_attributes'])
                // all(): raw items — keep any LITERAL_PREFIX ('~') intact so the
                // escape survives into each generated child control.
                ->merge($this->input_attributes->all())
                ->merge($attributes)
                ->merge([
                    'layout' => 'vertical',
                    'disable_errors' => true,
                    'group' => false,
                    'help' => false,
                    'id' => $id,
                    // The required mark belongs on the collection's global label, never
                    // on the individual choice labels.
                    'required_mark' => false,
                ])
                ->toArray();

            if ($this->layout === 'inline') {
                $options['inline'] = true;
            }

            $inputs .= CheckInput::make($this->name, $choice['label'], $value, $checked, $options);
        }

        return $inputs;
    }
}
