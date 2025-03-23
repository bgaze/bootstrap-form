<?php

namespace Bgaze\BootstrapForm\Bootstrap;

use Bgaze\BootstrapForm\Bootstrap\AbstractInput;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @property array $value
 * @property array $choices
 */
abstract class AbstractChoiceInput extends AbstractInput
{
    protected function defaults(): Collection
    {
        return parent::defaults()->merge([
            'choices' => [],
        ]);
    }

    public function __construct(string $name, $label = null, Arrayable|array $choices = [], $value = null, array $options = [])
    {
        parent::__construct($name, $label, $value, $options);
        $this->choices = $choices instanceof Arrayable ? $choices->toArray() : $choices;
    }

    public static function make(string $name, $label = null, Arrayable|array $choices = [], $value = null, array $options = []): static
    {
        return new static($name, $label, $choices, $value, $options);
    }

    protected function setValue($value): array
    {
        return Arr::wrap(parent::setValue($value));
    }
}
