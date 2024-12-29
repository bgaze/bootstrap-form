<?php

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Html\Html;
use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Support\Traits\HasAddons;
use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property bool $custom
 * @property string $text
 * @property string $button
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

    public function __construct(string $name, $label = null, array $options = [])
    {
        parent::__construct($name, $label, null, $options);
    }

    public static function make($name, $label = null, array $options = []): static
    {
        return new static($name, $label, $options);
    }

    protected function setInputAttributes(array $options): void
    {
        parent::setInputAttributes($options);

        if ($this->custom) {
            $this->input_attributes->addClass('custom-file-input');
        } else {
            $this->append = false;
            $this->prepend = false;
        }
    }

    public function input(): string
    {
        return Html::input($this->input_attributes->toArray())
            ->attribute('type', 'file')
            ->attribute('name', $this->name)
            ->attribute('value', $this->value)
            ->toHtml();
    }

    public function inputGroup(): string
    {
        if ($this->custom) {
            $button = Html::label()
                ->attribute('for', $this->input_attributes->id)
                ->addClass('custom-file-input')
                ->append($this->text);

            if ($this->button) {
                $button->attribute('data-browse', $this->button);
            }

            $input = Html::div()
                ->addClass(($this->layout === 'inline') ? 'custom-file w-auto' : 'custom-file')
                ->append([$this->input(), $button]);

            return $this->buildInputGroup($input);
        }

        return $this->input();
    }
}
