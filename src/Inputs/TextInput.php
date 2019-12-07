<?php

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Support\Traits\HasAddons;

/**
 * Specific settings:
 * 
 * @property string $tag
 * @property string $size
 */
class TextInput extends Input
{

    use HasAddons;

    /**
     * Get the input default options.
     *
     * @return Collection
     */
    protected function defaults()
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

    /**
     * Instanciate a TextInput.
     * 
     * @param string $name
     * @param mixed  $label
     * @param mixed  $value
     * @param array  $options
     * @return TextInput
     */
    public static function make($name, $label = null, $value = null, array $options = [])
    {
        return new static($name, $label, $value, $options);
    }

    /**
     * Set input attributes.
     * 
     * @param array  $options 
     */
    protected function setInputAttributes(array $options)
    {
        parent::setInputAttributes($options);

        $this->input_attributes->addClass('form-control');

        if ($this->size === 'sm' || $this->size === 'lg') {
            $this->input_attributes->addClass('form-control-' . $this->size);
        }
    }

    /**
     * Compile input to a HTML string.
     *
     * @return string
     */
    public function input()
    {
        if ($this->tag === 'password') {
            return $this->form->password($this->name, $this->input_attributes->toArray())->toHtml();
        }

        return $this->form->{$this->tag}($this->name, $this->value, $this->input_attributes->toArray())->toHtml();
    }
}
