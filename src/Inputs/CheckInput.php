<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Inputs;

use Bgaze\BootstrapForm\Support\Input;
use Illuminate\Support\Collection;

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
     * @var array{wrapper: string, input: string, label: string, input_attributes: array}
     */
    protected array $check_classes = [];

    protected function defaults(): Collection
    {
        return parent::defaults()->merge([
            'tag' => 'checkbox',
            'disable_errors' => false,
            'checked' => null,
            'inline' => false,
            'switch' => false,
        ]);
    }

    public function __construct(string $name, mixed $label = null, mixed $value = 1, mixed $checked = null, array $options = [])
    {
        parent::__construct($name, $label, $value, $options);
        $this->checked = $checked;
    }

    public static function make(string $name, mixed $label = null, mixed $value = 1, mixed $checked = null, array $options = []): static
    {
        return new static($name, $label, $value, $checked, $options);
    }

    protected function setInputAttributes(array $options): void
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
            $this->label === false,
        );

        $this->input_attributes->addClass($this->check_classes['input']);

        foreach ($this->check_classes['input_attributes'] as $key => $value) {
            $this->input_attributes->{$key} = $value;
        }
    }

    protected function setLabelAttributes(array $options): void
    {
        parent::setLabelAttributes($options);

        $this->label_attributes->addClass($this->check_classes['label']);
    }

    public function input(): string
    {
        return $this->elements->{$this->tag}($this->name, $this->value, $this->checked, $this->input_attributes->toArray())->toHtml();
    }

    public function inputGroup(): string
    {
        $content = $this->input();
        $content .= $this->label();

        if (!$this->disable_errors) {
            $content .= $this->errors;
            $content .= $this->validFeedback();
        }

        if ($this->help) {
            $content .= $this->help();
        }

        return $this->html->tag('div', $content, ['class' => $this->check_classes['wrapper']])->toHtml();
    }

    /**
     * A choice child disables its own feedback (rendered once at the collection level),
     * so it must not advertise an error/valid target it does not render.
     */
    protected function rendersOwnFeedback(): bool
    {
        return !$this->disable_errors;
    }

    public function label(): string
    {
        // Custom, label-less controls still need an (empty) label for the markup to hold.
        if ($this->label === false && $this->custom) {
            return sprintf($this->elements->label($this->input_attributes->id, '%s', $this->label_attributes->toArray(), false)->toHtml(), '');
        }

        if ($this->label === false) {
            return '';
        }

        return $this->elements->label($this->input_attributes->id, $this->label, $this->label_attributes->toArray(), false)->toHtml();
    }

    /**
     * The "left" part of the form group: empty, or a spacer in "pull right" mode.
     */
    protected function leftGroupColumn(): string
    {
        if ($this->layout === 'horizontal' && $this->pull_right) {
            return $this->html->tag('div', '', ['class' => $this->left_class])->toHtml();
        }

        return '';
    }
}
