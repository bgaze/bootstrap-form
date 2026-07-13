<?php

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;

/**
 * Specific settings:
 * 
 * @property string $tag
 * @property bool   $disable_errors
 * @property bool   $checked
 * @property bool   $inline
 * @property bool   $custom
 * @property bool   $switch
 */
class CheckInput extends Input
{

    /**
     * Resolved wrapper/input/label classes and extra input attributes for the control.
     *
     * @var array
     */
    protected $check_classes = [];


    /**
     * Get the input default options.
     *
     * @return Collection
     */
    protected function defaults()
    {
        return parent::defaults()->merge([
            'tag' => 'checkbox',
            'disable_errors' => false,
            'checked' => null,
            'inline' => false,
            'switch' => false,
        ]);
    }

    /**
     * The class constructor.
     * 
     * @param string $name
     * @param mixed  $label
     * @param mixed  $value
     * @param mixed  $checked
     * @param array  $options
     */
    public function __construct($name, $label = null, $value = 1, $checked = null, array $options = [])
    {
        parent::__construct($name, $label, $value, $options);
        $this->checked = $checked;
    }

    /**
     * Instanciate a CheckInput.
     * 
     * @param string $name
     * @param mixed  $label
     * @param mixed  $value
     * @param mixed  $checked
     * @param array  $options
     * @return CheckInput
     */
    public static function make($name, $label = null, $value = 1, $checked = null, array $options = [])
    {
        return new static($name, $label, $value, $checked, $options);
    }

    /**
     * Set input attributes.
     * 
     * @param array  $options 
     */
    protected function setInputAttributes(array $options)
    {
        parent::setInputAttributes($options);

        if ($this->tag === 'radio') {
            $this->switch = false;
        } else {
            $this->tag = 'checkbox';
        }

        if ($this->switch) {
            $this->custom = true;
        }

        $this->check_classes = $this->driver->checkClasses(
            $this->tag,
            (bool) $this->custom,
            (bool) $this->switch,
            (bool) $this->inline,
            $this->label === false
        );

        $this->input_attributes->addClass($this->check_classes['input']);

        foreach ($this->check_classes['input_attributes'] as $key => $value) {
            $this->input_attributes->{$key} = $value;
        }
    }

    /**
     * Set label attributes.
     *
     * @param array $options
     */
    protected function setLabelAttributes(array $options)
    {
        parent::setLabelAttributes($options);

        $this->label_attributes->addClass($this->check_classes['label']);
    }

    /**
     * Compile input to a HTML string.
     *
     * @return string
     */
    public function input()
    {
        return $this->elements->{$this->tag}($this->name, $this->value, $this->checked, $this->input_attributes->toArray())->toHtml();
    }

    /**
     * Decorate the input to get the final Bootstrap format.
     *
     * @return string
     */
    public function inputGroup()
    {
        $content = $this->input();
        $content .= $this->label();

        if (!$this->disable_errors) {
            $content .= $this->errors;
        }

        if ($this->help) {
            $content .= $this->html->tag('small', $this->help, ['class' => $this->driver->helpClass()]);
        }

        return $this->html->tag('div', $content, ['class' => $this->check_classes['wrapper']])->toHtml();
    }

    /**
     * Compile label to a HTML string.
     * 
     * @return string
     */
    public function label()
    {
        if ($this->label === false && $this->custom) {
            return sprintf($this->elements->label($this->input_attributes->id, '%s', $this->label_attributes->toArray(), false), '');
        }

        if ($this->label === false) {
            return '';
        }

        return $this->elements->label($this->input_attributes->id, $this->label, $this->label_attributes->toArray(), false)->toHtml();
    }

    /**
     * Get the "left" part of the form group (empty or spacer in "pull right" mode).
     * 
     * @return string
     */
    protected function leftGroupColumn()
    {
        if ($this->layout === 'horizontal' && $this->pull_right) {
            return $this->html->tag('div', '', ['class' => $this->left_class])->toHtml();
        }

        return '';
    }
}
