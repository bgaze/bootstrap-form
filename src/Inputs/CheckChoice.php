<?php

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Inputs\CheckInput;

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

    /**
     * The format to use to display error messages.
     */
    const ERRORS_FORMAT = '<div class="invalid-feedback d-block">:message</div>';

    /**
     * Get the input default options.
     *
     * @return Collection
     */
    protected function defaults()
    {
        return parent::defaults()->merge([
            'tag' => 'checkbox',
            'choices' => [],
            'inline' => false,
            'switch' => false,
        ]);
    }

    /**
     * The class constructor.
     * 
     * @param string $name
     * @param mixed  $label
     * @param array  $choices
     * @param mixed  $checked
     * @param array  $options
     */
    public function __construct($name, $label = null, array $choices = [], $checked = [], array $options = [])
    {
        parent::__construct($name, $label, $checked, $options);
        $this->choices = $choices;
    }

    /**
     * Instanciate a CheckChoice.
     * 
     * @param string $name
     * @param mixed  $label
     * @param array  $choices
     * @param mixed  $checked
     * @param array  $options
     * @return CheckChoice
     */
    public static function make($name, $label = null, array $choices = [], $checked = [], array $options = [])
    {
        return new static($name, $label, $choices, $checked, $options);
    }

    /**
     * Set label configuration and attributes.
     * 
     * @param mixed $label
     * @param array $options 
     */
    protected function configureLabel($label, array $options)
    {
        parent::configureLabel($label, $options);

        if ($this->layout === 'horizontal') {
            $this->label_attributes->addClass('pt-0');
        }
    }

    /**
     * Compile input to a HTML string.
     *
     * @return string
     */
    public function input()
    {
        $inputs = '';

        foreach ($this->choices as $value => $label) {
            $checked = in_array($value, (array) $this->value);

            $options = $this->settings
                ->except(['choices', 'name', 'value', 'label'])
                ->merge([
                    'layout' => 'vertical',
                    'disable_errors' => true,
                    'group' => false,
                    'help' => false,
                    'id' => $this->flattenName('_') . '_' . $value,
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
