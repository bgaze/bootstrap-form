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
     * Set input attributes.
     * 
     * @param array  $options 
     */
    protected function setInputAttributes(array $options)
    {
        parent::setInputAttributes($options);

        if ($this->custom && $this->driver->usesCustomFile()) {
            $this->input_attributes->addClass($this->driver->customFileInputClass());
        } else {
            $class = $this->driver->fileInputClass();
            if ($class !== '') {
                $this->input_attributes->addClass($class);
            }
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
        return $this->elements->file($this->name, $this->input_attributes->toArray())->toHtml();
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

        // Only versions with a dedicated custom-file markup wrap the input.
        if (!$this->custom || !$this->driver->usesCustomFile()) {
            return $input;
        }

        // Prepare button.
        $attr = ['class' => $this->driver->customFileLabelClass()];
        if ($this->button) {
            $attr['data-browse'] = $this->button;
        }
        $button = $this->elements->label($this->input_attributes->id, $this->text, $attr, false);

        // Wrap into the custom-file block.
        $input = $this->html->tag('div', $input . $button, [
            'class' => $this->driver->customFileWrapperClass($this->layout === 'inline'),
        ])->toHtml();

        // Check if addons.
        if (!$this->append && !$this->prepend) {
            return $input;
        }

        // Resolve prepend / append content and let the driver assemble the input group.
        $prepend = $this->prepend
            ? (is_array($this->prepend) ? implode('', $this->prepend) : $this->prepend)
            : '';

        $append = $this->append
            ? (is_array($this->append) ? implode('', $this->append) : $this->append)
            : '';

        return $this->driver->inputGroup($this->html, $prepend, $input, $append, null);
    }
}
