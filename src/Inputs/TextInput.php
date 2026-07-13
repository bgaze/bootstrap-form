<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Support\Traits\HasAddons;
use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property string $tag
 * @property string $size
 */
class TextInput extends Input
{
    use HasAddons;

    protected function defaults(): Collection
    {
        return parent::defaults()
            ->except('custom')
            ->merge([
                'tag' => 'text',
                'size' => null,
                'prepend' => false,
                'append' => false,
            ]);
    }

    public static function make(string $name, mixed $label = null, mixed $value = null, array $options = []): static
    {
        return new static($name, $label, $value, $options);
    }

    protected function setInputAttributes(array $options): void
    {
        parent::setInputAttributes($options);

        $this->input_attributes->addClass($this->driver->inputClass());

        if ($this->size === 'sm' || $this->size === 'lg') {
            $this->input_attributes->addClass($this->driver->inputSizeClass($this->size));
        }
    }

    public function input(): string
    {
        if ($this->tag === 'password') {
            return $this->elements->password($this->name, $this->input_attributes->toArray())->toHtml();
        }

        return $this->elements->{$this->tag}($this->name, $this->value, $this->input_attributes->toArray())->toHtml();
    }
}
