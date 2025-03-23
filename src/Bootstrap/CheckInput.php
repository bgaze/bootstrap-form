<?php

namespace Bgaze\BootstrapForm\Bootstrap;

use Bgaze\BootstrapForm\Html\Html;
use Bgaze\BootstrapForm\Bootstrap\AbstractInput;
use Illuminate\Support\Collection;

/**
 * Specific settings:
 *
 * @property string $type
 * @property bool $disable_errors
 * @property bool $checked
 * @property bool $inline
 * @property bool $custom
 * @property bool $switch
 */
class CheckInput extends AbstractInput
{
    protected function defaults(): Collection
    {
        return parent::defaults()->merge([
            'type' => 'checkbox',
            'disable_errors' => false,
            'checked' => null,
            'inline' => false,
            'switch' => false,
        ]);
    }

    public function __construct(string $name, $label = null, $value = 1, $checked = null, array $options = [])
    {
        parent::__construct($name, $label, $value, $options);
        $this->checked = $checked;
    }

    public static function make(string $name, $label = null, $value = 1, $checked = null, array $options = []): static
    {
        return new static($name, $label, $value, $checked, $options);
    }

    protected function setInputAttributes(array $options): void
    {
        parent::setInputAttributes($options);

        if ($this->type === 'radio') {
            $this->switch = false;
        } else {
            $this->type = 'checkbox';
        }

        if ($this->switch) {
            $this->custom = true;
        }

        $this->input_attributes->addClass($this->custom ? 'custom-control-input' : 'form-check-input');
    }

    protected function setLabelAttributes(array $options): void
    {
        parent::setLabelAttributes($options);

        $this->label_attributes->addClass($this->custom ? 'custom-control-label' : 'form-check-label');

        if ($this->label === false && ! $this->custom) {
            $this->input_attributes->addClass('position-static');
        }
    }

    public function input(): string
    {
        return Html::input($this->input_attributes)
            ->attribute('type', $this->type)
            ->attribute('value', $this->value)
            ->attribute('checked', $this->checked)
            ->toHtml();
    }

    public function inputGroup(): string
    {
        $div = Html::div()
            ->append([$this->input(), $this->label()])
            ->addClass($this->custom ? 'custom-control' : 'form-check');

        if ($this->errors && ! $this->disable_errors) {
            $div->append($this->errors);
        }

        if ($help = $this->help()) {
            $div->append($help);
        }

        if ($this->inline) {
            $div->addClass($this->custom ? ' custom-control-inline' : ' form-check-inline');
        }

        if ($this->switch) {
            $div->addClass(' custom-switch');
        } elseif ($this->custom) {
            $div->addClass(" custom-{$this->type}");
        }

        return $div->toHtml();
    }

    public function label(): ?string
    {
        if ($this->label === false && $this->custom) {
            return Html::label($this->label_attributes)->attribute('for', $this->input_attributes->id);
        }

        return parent::label();
    }

    protected function leftGroupColumn(): ?string
    {
        if ($this->layout === 'horizontal' && $this->pull_right) {
            return Html::div()->addClass($this->left_class);
        }

        return null;
    }
}
