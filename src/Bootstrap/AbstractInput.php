<?php

namespace Bgaze\BootstrapForm\Bootstrap;

use Bgaze\BootstrapForm\Html\Attributes;
use Bgaze\BootstrapForm\Html\Html;
use Bgaze\BootstrapForm\Support\Facade;
use Bgaze\BootstrapForm\Support\HasSettings;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Stringable;

/**
 * Input settings:
 *
 * @property ?string $errors
 * @property string $name
 * @property $value
 * @property $label
 * @property $help
 *
 * Inherited from form:
 * @property ?array $group
 * @property string $layout
 * @property string $error_bag
 * @property bool $show_all_errors
 * @property ?string $pull_right
 * @property string $left_class
 * @property string $right_class
 * @property ?string $lspace
 * @property ?string $hspace
 * @property ?string $vspace
 */
abstract class AbstractInput implements Htmlable, Renderable, Stringable
{
    use HasSettings;

    protected Attributes $input_attributes;

    protected Attributes $label_attributes;

    protected Attributes $group_attributes;

    public function __construct(string $name, $label = null, $value = null, array $options = [])
    {
        $this->configure($name, $label, $value, $options);
        $this->setInputAttributes($options);
        $this->setLabelAttributes($options);
        $this->setGroupAttributes($options);
        $this->setErrors();
    }

    public function toHtml(): string
    {
        return $this->group();
    }

    public function render(): string
    {
        return $this->toHtml();
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    // CONFIGURATION

    protected function defaults(): Collection
    {
        return Facade::settings()->merge([
            'help' => false,
        ]);
    }

    protected function configure(string $name, $label = null, $value = null, array $options = []): void
    {
        // Get default settings.
        $this->settings = static::defaults();

        // Merge with provided configuration.
        $settings = collect($options)->only($this->settings->keys())->except('group');
        $this->settings = $this->settings->merge($settings);

        // Add reserved values.
        $this->name = $name;
        $this->value = $this->setValue($value);
        $this->label = $label;
        $this->errors = '';
    }

    protected function setValue($value)
    {
        return $value;
    }

    protected function setInputAttributes(array $options): void
    {
        $this->input_attributes = Attributes::make(Arr::except($options, $this->settings->keys()));

        if (!$this->input_attributes->id) {
            $this->input_attributes->set('id', $this->flattenName($this->name, '_'));
        }
    }

    protected function setLabelAttributes(array $options): void
    {
        $label = $this->label;

        if ($label !== false && $label !== '0' && empty($label)) {
            $this->label = str_replace('_', ' ', Str::ucfirst(Str::lower(Str::title($this->name))));
        }

        $this->label_attributes = Attributes::make();

        if (isset($options['label']) && is_array($options['label'])) {
            $this->label_attributes->merge($options['label']);
        }

        $this->label_attributes->set('for', $this->input_attributes->id);

        if ($this->layout === 'inline' && $this->lspace) {
            $this->label_attributes->addClass($this->lspace);
        }
    }

    protected function setGroupAttributes(array $options): void
    {
        if (isset($options['group'])) {
            if ($options['group'] === false) {
                $this->group = false;
            } elseif (is_array($this->group) && is_array($options['group'])) {
                $this->group = array_merge($this->group, $options['group']);
            } elseif (is_array($options['group'])) {
                $this->group = $options['group'];
            }
        }

        $this->group_attributes = Attributes::make();

        if (is_array($this->group)) {
            $this->group_attributes->merge($this->group);
            $this->group = true;
        }

        if (!$this->group_attributes->id) {
            $this->group_attributes->set('id', $this->flattenName($this->name, '_') . '_group');
        }

        $this->group_attributes->addClass('form-group');

        if ($this->layout === 'horizontal') {
            $this->group_attributes->addClass('row');
        } elseif ($this->layout === 'inline') {
            $this->group_attributes->addClass([$this->hspace => !!$this->hspace, $this->vspace => !!$this->vspace]);
        }
    }

    protected function errorTemplate(): string
    {
        return '<div class="invalid-feedback d-block">:message</div>';
    }

    protected function setErrors(): void
    {
        $this->errors = null;

        if ($errorBag = Facade::getSessionStore()->get('errors')?->{$this->error_bag}) {
            $field = $this->flattenName($this->name, '.');

            if ($errorBag->has($field)) {
                if ($this->show_all_errors) {
                    $this->errors = implode('', $errorBag->get($field, $this->errorTemplate()));
                } else {
                    $this->errors = $errorBag->first($field, $this->errorTemplate());
                }

                $this->input_attributes->addClass('is-invalid');
                $this->group_attributes->addClass('is-invalid');
            }
        }
    }

    // COMPONENTS

    abstract public function input(): string;

    public function inputGroup(): string
    {
        return $this->input();
    }

    public function label(): ?string
    {
        return ($this->label === false) ? null : Html::label($this->label_attributes)->append($this->label)->toHtml();
    }

    public function help(): ?string
    {
        return ($this->help === false) ? null : Html::small()->addClass('form-text')->append($this->help)->toHtml();
    }

    public function group(): string
    {
        if ($this->group) {
            return Html::div($this->group_attributes)
                ->append([$this->leftGroupColumn(), $this->rightGroupColumn()])
                ->toHtml();
        }

        return $this->inputGroup();
    }

    protected function leftGroupColumn(): ?string
    {
        if ($this->layout === 'horizontal' && $this->pull_right && !$this->label) {
            return Html::div()->addClass($this->left_class);
        }

        if ($this->layout === 'horizontal') {
            $this->label_attributes->addClass('col-form-label')->addClass($this->left_class);
        }

        if ($this->layout === 'inline' && $this->lspace) {
            $this->label_attributes->addClass($this->lspace);
        }

        return $this->label();
    }

    protected function rightGroupColumn(): string
    {
        $div = Html::div()->append([$this->inputGroup(), $this->errors, $this->help()]);

        if ($this->layout === 'horizontal') {
            $div->addClass($this->pull_right ? 'col' : $this->right_class);
        }

        return $div;
    }
}
