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

        $this->input_attributes->addClass($this->custom ? 'custom-control-input' : 'form-check-input');
    }

    /**
     * Set label attributes.
     * 
     * @param array $options 
     */
    protected function setLabelAttributes(array $options)
    {
        parent::setLabelAttributes($options);

        $this->label_attributes->addClass($this->custom ? 'custom-control-label' : 'form-check-label');

        if ($this->label === false && !$this->custom) {
            $this->input_attributes->addClass('position-static');
        }
    }

    /**
     * Compile input to a HTML string.
     *
     * @return string
     */
    public function input()
    {
        return $this->form->{$this->tag}($this->name, $this->value, $this->checked, $this->input_attributes->toArray())->toHtml();
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
            $content .= $this->html->tag('small', $this->help, ['class' => 'form-text']);
        }

        $class = $this->custom ? 'custom-control' : 'form-check';

        if ($this->inline) {
            $class .= $this->custom ? ' custom-control-inline' : ' form-check-inline';
        }

        if ($this->switch) {
            $class .= ' custom-switch';
        } elseif ($this->custom) {
            $class .= " custom-{$this->tag}";
        }

        return $this->html->tag('div', $content, ['class' => $class])->toHtml();
    }

    /**
     * Compile label to a HTML string.
     * 
     * @return string
     */
    public function label()
    {
        if ($this->label === false && $this->custom) {
            return sprintf($this->form->label($this->input_attributes->id, '%s', $this->label_attributes->toArray(), false), '');
        }

        if ($this->label === false) {
            return '';
        }

        return $this->form->label($this->input_attributes->id, $this->label, $this->label_attributes->toArray(), false)->toHtml();
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
