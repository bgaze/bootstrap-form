<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Support\Traits\HasAddons;
use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property bool $custom
 * @property string $text
 * @property string $button
 *
 * @phpstan-consistent-constructor
 */
class FileInput extends Input
{
    use HasAddons;

    protected function defaults(): Collection
    {
        return parent::defaults()->merge([
            'text' => 'Choose file',
            'button' => null,
            'prepend' => false,
            'append' => false,
        ]);
    }

    public function __construct(string $name, mixed $label = null, array $options = [])
    {
        parent::__construct($name, $label, null, $options);
    }

    public static function make(string $name, mixed $label = null, array $options = []): static
    {
        return new static($name, $label, $options);
    }

    protected function setInputAttributes(array $options): void
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

    public function input(): string
    {
        return $this->elements->file($this->name, $this->input_attributes->toArray())->toHtml();
    }

    public function inputGroup(): string
    {
        $input = $this->input();

        // Only versions with a dedicated custom-file markup wrap the input.
        if (! $this->custom || ! $this->driver->usesCustomFile()) {
            return $input;
        }

        // Prepare the browse button label.
        $attr = ['class' => $this->driver->customFileLabelClass()];
        if ($this->button) {
            $attr['data-browse'] = $this->button;
        }
        $button = $this->elements->label($this->input_attributes->id, $this->text, $attr, false);

        // Wrap into the custom-file block.
        $input = $this->html->tag('div', $input.$button, [
            'class' => $this->driver->customFileWrapperClass($this->layout === 'inline'),
        ])->toHtml();

        if (! $this->append && ! $this->prepend) {
            return $input;
        }

        // Let the driver assemble the input group.
        return $this->driver->inputGroup(
            $this->html,
            $this->resolveAddon($this->prepend),
            $input,
            $this->resolveAddon($this->append),
            null,
        );
    }
}
