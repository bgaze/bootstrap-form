<?php

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Support\Traits\HasAddons;

/**
 * Specific settings:
 * 
 * @property array $choices
 * @property bool  $custom
 * @property string $size
 */
class SelectInput extends Input
{

    use HasAddons;

    /**
     * Get the input default options.
     *
     * @return Collection
     */
    protected function defaults()
    {
        return parent::defaults()->merge([
            'choices' => [],
            'size' => null,
            'prepend' => false,
            'append' => false,
        ]);
    }

    /**
     * The class constructor.
     * 
     * @param string $name
     * @param mixed  $label
     * @param array  $choices
     * @param mixed  $selected
     * @param array  $options
     */
    public function __construct($name, $label = null, $choices = [], $selected = null, array $options = [])
    {
        parent::__construct($name, $label, $selected, $options);
        $this->choices = $choices;
    }

    /**
     * Instanciate a SelectInput.
     * 
     * @param string $name
     * @param mixed  $label
     * @param array  $choices
     * @param mixed  $selected
     * @param array  $options
     * @return SelectInput
     */
    public static function make($name, $label = null, $choices = [], $selected = null, array $options = [])
    {
        return new static($name, $label, $choices, $selected, $options);
    }

    /**
     * Set input configuration and attributes.
     * 
     * @param string $name
     * @param mixed  $value
     * @param array  $options 
     */
    protected function configureInput($name, $value, array $options)
    {
        parent::configureInput($name, $value, $options);

        $class = $this->custom ? 'custom-select' : 'form-control';
        $this->input_attributes->addClass($class);

        if ($this->size === 'sm' || $this->size === 'lg') {
            $this->input_attributes->addClass($class . '-' . $this->size);
        }
    }

    /**
     * Compile input to a HTML string.
     *
     * @return string
     */
    public function input()
    {
        return $this->form->select($this->name, $this->choices, $this->value, $this->input_attributes->toArray())->toHtml();
    }
}
