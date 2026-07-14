<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\ChoiceList;
use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Support\Traits\HasAddons;
use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property iterable $choices
 * @property bool $custom
 * @property string $size
 * @property array $option_attributes
 * @property array $optgroup_attributes
 *
 * @phpstan-consistent-constructor
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
            'option_attributes' => [],
            'optgroup_attributes' => [],
        ]);
    }

    public function __construct(string $name, mixed $label = null, iterable $choices = [], mixed $selected = null, array $options = [])
    {
        parent::__construct($name, $label, $selected, $options);
        $this->choices = $choices;
    }

    public static function make(string $name, mixed $label = null, iterable $choices = [], mixed $selected = null, array $options = []): static
    {
        return new static($name, $label, $choices, $selected, $options);
    }

    protected function setInputAttributes(array $options): void
    {
        parent::setInputAttributes($options);

        $this->input_attributes->addClass($this->driver->selectClass((bool) $this->custom));

        if ($this->size === 'sm' || $this->size === 'lg') {
            $this->input_attributes->addClass($this->driver->selectSizeClass((bool) $this->custom, $this->size));
        }
    }

    public function input(): string
    {
        [$list, $optionsAttributes, $optgroupsAttributes] = ChoiceList::select(
            $this->choices,
            (array) $this->option_attributes,
            (array) $this->optgroup_attributes,
        );

        return $this->elements->select(
            $this->name,
            $list,
            $this->value,
            $this->input_attributes->toArray(),
            $optionsAttributes,
            $optgroupsAttributes,
        )->toHtml();
    }

    protected function isFloatable(): bool
    {
        return true;
    }

    /**
     * A floating <select> does not use the placeholder attribute (that would create a
     * blank <option>); the label floats without it.
     */
    protected function floatingNeedsPlaceholder(): bool
    {
        return false;
    }
}
