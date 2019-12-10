<?php

namespace Bgaze\BootstrapForm\Support;

use Collective\Html\FormBuilder;
use Collective\Html\HtmlBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Bgaze\BootstrapForm\Support\Traits\HasSettings;
use Bgaze\BootstrapForm\Support\Attributes;
use BF;

/**
 * Input settings:
 *
 * @property string $errors
 * @property string $name
 * @property string $value
 * @property mixed  $label
 * @property string $help
 *
 * Inherited from form:
 *
 * @property string       $layout
 * @property string       $error_bag
 * @property bool         $show_all_errors
 * @property bool         $pull_right
 * @property string       $left_class
 * @property string       $right_class
 * @property string       $spacer
 * @property bool|array   $group
 */
abstract class Input
{

    use HasSettings;

    /**
     * Illuminate HtmlBuilder instance.
     *
     * @var HtmlBuilder
     */
    protected $html;

    /**
     * Illuminate FormBuilder instance.
     *
     * @var FormBuilder
     */
    protected $form;

    /**
     * The input attributes repository.
     *
     * @var Attributes
     */
    protected $input_attributes;

    /**
     * The label attributes repository.
     *
     * @var Attributes
     */
    protected $label_attributes;

    /**
     * The group attributes repository.
     *
     * @var Attributes
     */
    protected $group_attributes;

    /**
     * The class constructor.
     *
     * @param string $name
     * @param mixed  $label
     * @param mixed  $value
     * @param array  $options
     */
    public function __construct($name, $label = null, $value = null, array $options = [])
    {
        // Resolve html builders.
        $this->html = BF::htmlBuilder();
        $this->form = BF::formBuilder();

        // Get field configuration.
        $this->configure($name, $label, $value, $options);
        $this->setInputAttributes($options);
        $this->setLabelAttributes($options);
        $this->setGroupAttributes($options);
        $this->getErrors();
    }

    /**
     * Return the input as a HTML string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->group();
    }

    ### CONFIGURATION ##########################################################

    /**
     * Get the input default options.
     *
     * @return Collection
     */
    protected function defaults()
    {
        return BF::settings()->merge([
            'help' => false,
        ]);
    }

    /**
     * Merge provided options with default configuration.
     *
     * @param string $name
     * @param mixed  $label
     * @param mixed  $value
     * @param array  $options
     */
    protected function configure($name, $label, $value, array $options)
    {
        // Get default settings.
        $this->settings = static::defaults();

        // Merge with provided configuration.
        $settings = collect($options)
            ->only($this->settings->keys())
            ->except('group');
        $this->settings = $this->settings->merge($settings);

        // Add reserved values.
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
        $this->errors = '';
    }

    /**
     * Set input attributes.
     *
     * @param array  $options
     */
    protected function setInputAttributes(array $options)
    {
        $this->input_attributes = Attributes::make($options)->except($this->settings->keys());

        if (!$this->input_attributes->id) {
            $this->input_attributes->id = $this->flattenName($this->name, '_');
        }
    }

    /**
     * Set label attributes.
     *
     * @param array $options
     */
    protected function setLabelAttributes(array $options)
    {
        if ($this->label !== false && $this->label !== '0' && empty($this->label)) {
            $this->label = str_replace('_', ' ', Str::ucfirst(Str::lower(Str::title($this->name))));
        }

        if (isset($options['label']) && is_array($options['label'])) {
            $this->label_attributes = Attributes::make($options['label']);
        } else {
            $this->label_attributes = Attributes::make();
        }
    }

    /**
     * Set group attributes.
     *
     * @param array $options
     */
    protected function setGroupAttributes(array $options)
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

        if (is_array($this->group)) {
            $this->group_attributes = Attributes::make($this->group);
            $this->group = true;
        } else {
            $this->group_attributes = Attributes::make();
        }

        if (!$this->group_attributes->id) {
            $this->group_attributes->id = $this->flattenName($this->name, '_') . '_group';
        }
    }

    /**
     * Get the template to build up error messages.
     */
    protected function errorTemplate()
    {
        return '<div class="invalid-feedback">:message</div>';
    }

    /**
     * Compile inputs errors to and HTML string.
     * Add error class to input and group if there is any errors.
     */
    protected function getErrors()
    {
        $errors = $this->form->getSessionStore()->get('errors');
        if (empty($errors)) {
            return;
        }

        $errorBag = $errors->{$this->error_bag} ?? false;
        if (!$errorBag) {
            return;
        }

        $field = $this->flattenName($this->name, '.');
        if (!$errorBag->has($field)) {
            return;
        }

        if ($this->show_all_errors) {
            $this->errors = implode('', $errorBag->get($field, $this->errorTemplate()));
        } else {
            $this->errors = $errorBag->first($field, $this->errorTemplate());
        }

        $this->input_attributes->addClass('is-invalid');
        $this->group_attributes->addClass('is-invalid');
    }

    ### COMPONENTS #############################################################

    /**
     * Compile input to a HTML string.
     *
     * @return string
     */
    public abstract function input();

    /**
     * Decorate the input to get the final Bootstrap format.
     *
     * @return string
     */
    public function inputGroup()
    {
        return $this->input();
    }

    /**
     * Compile label to a HTML string.
     *
     * @return string
     */
    public function label()
    {
        if ($this->label === false) {
            return '';
        }

        return $this->form->label($this->input_attributes->id, $this->label, $this->label_attributes->toArray(), false)->toHtml();
    }

    /**
     * Compile label to a HTML string.
     *
     * @return string
     */
    public function help()
    {
        if ($this->help === false) {
            return '';
        }

        return $this->html->tag('small', $this->help, ['class' => 'form-text'])->toHtml();
    }

    /**
     * Compile group to a HTML string.
     *
     * @return string
     */
    public function group()
    {
        if (!$this->group) {
            return $this->inputGroup();
        }

        $this->group_attributes->addClass('form-group');

        if ($this->layout === 'horizontal') {
            $this->group_attributes->addClass('row');
        }

        if ($this->layout === 'inline' && $this->hspace) {
            $this->group_attributes->addClass($this->hspace);
        }

        if ($this->layout === 'inline' && $this->vspace) {
            $this->group_attributes->addClass($this->vspace);
        }

        $content = $this->leftGroupColumn() . $this->rightGroupColumn();

        return $this->html->tag('div', $content, $this->group_attributes->toArray())->toHtml();
    }

    /**
     * Get the "left" part of the form group (label or spacer in "pull right" mode).
     *
     * @return string
     */
    protected function leftGroupColumn()
    {
        if ($this->layout === 'horizontal' && $this->pull_right && !$this->label) {
            return $this->html->tag('div', '', ['class' => $this->left_class])->toHtml();
        }

        if ($this->layout === 'horizontal') {
            $this->label_attributes->addClass('col-form-label')->addClass($this->left_class);
        }

        if ($this->layout === 'inline' && $this->lspace) {
            $this->label_attributes->addClass($this->lspace);
        }

        return $this->label();
    }

    /**
     * Get the "right" part of the form group (input + errors + help wrapped in a div).
     *
     * @return string
     */
    protected function rightGroupColumn()
    {
        $content = $this->inputGroup();
        $content .= $this->errors;
        $content .= $this->help();

        $attributes = Attributes::make();

        if ($this->layout === 'horizontal') {
            $attributes->addClass($this->pull_right ? 'col' : $this->right_class);
        }

        return $this->html->tag('div', $content, $attributes->toArray())->toHtml();
    }
}
