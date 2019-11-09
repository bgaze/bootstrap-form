<?php

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;

/**
 * Specific settings:
 * 
 * @property bool  $custom
 */
class RangeInput extends Input
{

    /**
     * Instanciate a RangeInput.
     * 
     * @param string $name
     * @param mixed  $label
     * @param mixed  $value
     * @param array  $options
     * @return RangeInput
     */
    public static function make($name, $label = null, $value = null, array $options = [])
    {
        return new static($name, $label, $value, $options);
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

        if ($this->custom) {
            $this->input_attributes->addClass('custom-range');
        } else {
            $this->input_attributes->addClass('form-control-range');
        }
    }

    /**
     * Compile input to a HTML string.
     *
     * @return string
     */
    public function input()
    {
        return $this->form->range($this->name, $this->value, $this->input_attributes->toArray())->toHtml();
    }
}
