<?php

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Support\Traits\HasAddons;

/**
 * Specific settings:
 * 
 * @property bool    $custom
 * @property string  $text
 * @property string  $button
 */
class FileInput extends Input
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
            'custom' => false,
            'text' => 'Choose file',
            'button' => null,
            'prepend' => false,
            'append' => false,
        ]);
    }

    /**
     * The class constructor.
     * 
     * @param string $name
     * @param mixed  $label
     * @param array  $options
     */
    public function __construct($name, $label = null, array $options = [])
    {
        parent::__construct($name, $label, null, $options);
    }

    /**
     * Instanciate a FileInput.
     * 
     * @param string $name
     * @param mixed  $label
     * @param array  $options
     * @return FileInput
     */
    public static function make($name, $label = null, array $options = [])
    {
        return new static($name, $label, $options);
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
            $this->input_attributes->addClass('custom-file-input');
        } else {
            $this->append = false;
            $this->prepend = false;
        }
    }

    /**
     * Compile input to a HTML string.
     *
     * @return string
     */
    public function input()
    {
        return $this->form->file($this->name, $this->input_attributes->toArray())->toHtml();
    }

    /**
     * Decorate the input to get the final Bootstrap format.
     *
     * @return string
     */
    public function inputGroup()
    {
        // Prepare file input.
        $input = $this->input();
        if (!$this->custom) {
            return $input;
        }

        // Prepare button.
        $attr = ['class' => 'custom-file-label'];
        if ($this->button) {
            $attr['data-browse'] = $this->button;
        }
        $button = $this->form->label($this->input_attributes->id, $this->text, $attr, false);

        // Wrap elements.
        return $this->html->tag('div', $input . $button, [
            'class' => ($this->layout === 'inline') ? 'custom-file w-auto' : 'custom-file'
        ])->toHtml();
    }
}
