<?php

namespace Bgaze\BootstrapForm;

use Illuminate\Database\Eloquent\Model;
use Collective\Html\HtmlBuilder;
use Collective\Html\FormBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Bgaze\BootstrapForm\Support\Attributes;
use Bgaze\BootstrapForm\Support\Traits\HasSettings;
use Bgaze\BootstrapForm\Inputs;

/**
 * Form configuration:
 * 
 * @property Model  $model
 * @property string $error_bag
 * @property string $url
 * @property string $route
 * @property string $action
 * @property string $store
 * @property string $update
 * @property string $layout
 * @property string $left_column_class
 * @property string $right_column_class
 * @property bool   $show_all_errors
 */
class BootstrapForm
{

    use Macroable;
    use HasSettings;

    /**
     * Configuration keys that won't be inherited by form inputs.
     */
    protected const RESERVED = ['model', 'url', 'route', 'action', 'update', 'store'];

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
     * The form attribute set.
     * 
     * @var \Bgaze\BootstrapForm\Support\Attributes 
     */
    protected $attributes;

    /**
     * Constructor.
     *
     */
    public function __construct(HtmlBuilder $html, FormBuilder $form)
    {
        $this->html = $html;
        $this->form = $form;
        $this->resetForm();
    }

    ### MISC UTILITIES #########################################################

    /**
     * Returns the "inheritables" forms settings.
     * 
     * @return Collection
     */
    public function settings()
    {
        return $this->settings->except(static::RESERVED);
    }

    /**
     * Returns the Illuminate HtmlBuilder instance.
     * 
     * @return HtmlBuilder
     */
    public function htmlBuilder()
    {
        return $this->html;
    }

    /**
     * Returns the Illuminate FormBuilder instance.
     * 
     * @return FormBuilder
     */
    public function formBuilder()
    {
        return $this->form;
    }

    ### CONFIGURATION ##########################################################

    /**
     * Reset form settings and attributes to default configuration.
     * 
     */
    protected function resetForm()
    {
        // Set defaults options.
        $reserved = array_fill_keys(self::RESERVED, null);
        $this->settings = Collection::make($reserved)
            ->put('error_bag', 'default')
            ->merge(config('bootstrap_form'))
            ->except('blade_directives');

        // Set default attributes.
        $this->attributes = Attributes::make(['role' => 'form']);
    }

    /**
     * Init form with provided option set.
     * 
     * @param array $options
     */
    protected function initForm(array $options = [])
    {
        // Merge with provided options.
        $settings = collect($options)->only($this->settings->keys());
        $this->settings = $this->settings->merge($settings);

        // Set form attributes.
        $attributes = collect($options)->except($this->settings->keys());
        $this->attributes = $this->attributes->merge($attributes);

        // Set form class based on form layout.
        if ($this->layout !== 'vertical') {
            $this->attributes->addClass('form-' . $this->layout);
        }

        // Use explicity passed URL, route or action.
        foreach (['url', 'route', 'action'] as $action) {
            if ($this->{$action}) {
                $this->attributes->{$action} = $this->{$action};
                return;
            }
        }
    }

    /**
     * Init Model related form settings.
     * 
     * @param Model $model
     */
    protected function initModelForm(Model $model)
    {
        // Get action details based on model existance.
        if ($model->exists) {
            $method = 'PUT';
            $action = $this->update;
        } else {
            $method = 'POST';
            $action = $this->store;
        }

        // Set method based on model existance.
        $this->attributes->method = $method;

        // Set action if provided.
        if ($action) {
            $layout = Str::contains($action, '@') ? 'action' : 'route';
            $this->attributes->{$layout} = [$action, $model->getRouteKey()];
        }
    }

    ### BUILD FORM ELEMENT #####################################################

    /**
     * Open a form.
     *
     * @param  array  $options
     * @return string
     */
    public function open(array $options = [])
    {
        // Configure form.
        $this->initForm($options);

        // If model, open model form.
        if ($this->model instanceof Model) {
            $this->initModelForm($this->model);
            //dd($this->model, $this->attributes->toArray());
            return $this->form->model($this->model, $this->attributes->toArray())->toHtml();
        }

        // Otherwise init standart form.
        return $this->form->open($this->attributes->toArray())->toHtml();
    }

    /**
     * Reset and close the form.
     *
     * @return string
     */
    public function close()
    {
        $this->resetForm();
        return $this->form->close()->toHtml();
    }

    /**
     * Open a vertical Bootstrap form.
     *
     * @param  array  $options
     * @return string
     */
    public function vertical(array $options = [])
    {
        $options['layout'] = 'vertical';
        return $this->open($options);
    }

    /**
     * Open an inline Bootstrap form.
     *
     * @param  array  $options
     * @return string
     */
    public function inline(array $options = [])
    {
        $options['layout'] = 'inline';
        return $this->open($options);
    }

    /**
     * Open a horizontal Bootstrap form.
     *
     * @param  array  $options
     * @return string
     */
    public function horizontal(array $options = [])
    {
        $options['layout'] = 'horizontal';
        return $this->open($options);
    }

    ### BUILD FORM INPUTS ######################################################

    /**
     * Create a Bootstrap input by tag.
     *
     * @param  string  $tag
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    protected function input($tag, $name, $label = null, $value = null, array $options = [])
    {
        $options['tag'] = $tag;
        return Inputs\TextInput::make($name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap text field input.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function text($name, $label = null, $value = null, array $options = [])
    {
        return $this->input('text', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap email field input.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function email($name = 'email', $label = null, $value = null, array $options = [])
    {
        return $this->input('email', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap URL field input.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function url($name, $label = null, $value = null, array $options = [])
    {
        return $this->input('url', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap tel field input.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function tel($name, $label = null, $value = null, array $options = [])
    {
        return $this->input('tel', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap number field input.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function number($name, $label = null, $value = null, array $options = [])
    {
        return $this->input('number', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap date field input.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function date($name, $label = null, $value = null, array $options = [])
    {
        return $this->input('date', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap email time input.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function time($name, $label = null, $value = null, array $options = [])
    {
        return $this->input('time', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap password field input.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  array   $options
     * @return string
     */
    public function password($name, $label = null, array $options = [])
    {
        return $this->input('password', $name, $label, null, $options);
    }

    /**
     * Create a Bootstrap textarea field input.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function textarea($name, $label = null, $value = null, array $options = [])
    {
        return $this->input('textarea', $name, $label, $value, $options);
    }

    /**
     * Create a select box field.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  array   $choices
     * @param  string  $selected
     * @param  array   $options
     * @return string
     */
    public function select($name, $label = null, $choices = [], $selected = null, array $options = [])
    {
        return Inputs\SelectInput::make($name, $label, $choices, $selected, $options);
    }

    /**
     * Create a Boostrap file upload button.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  array   $options
     * @return string
     */
    public function file($name, $label = null, array $options = [])
    {
        return Inputs\FileInput::make($name, $label, $options);
    }

    /**
     * Create a Boostrap file upload button.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function range($name, $label = null, $value = null, array $options = [])
    {
        return Inputs\RangeInput::make($name, $label, $value, $options);
    }

    /**
     * Create a hidden field.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function hidden($name, $value = null, $options = [])
    {
        return $this->form->hidden($name, $value, $options);
    }

    /**
     * Create a Bootstrap checkbox input.
     *
     * @param  string   $name
     * @param  string   $label
     * @param  string   $value
     * @param  bool     $checked
     * @param  array    $options
     * @return string
     */
    public function checkbox($name, $label = null, $value = 1, $checked = null, array $options = [])
    {
        $options['tag'] = 'checkbox';
        return Inputs\CheckInput::make($name, $label, $value, $checked, $options);
    }

    /**
     * Create a collection of Bootstrap checkboxes.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  array   $choices
     * @param  mixed   $checked
     * @param  array   $options
     * @return string
     */
    public function checkboxes($name, $label = null, array $choices = [], $checked = null, array $options = [])
    {
        $options['tag'] = 'checkbox';
        return Inputs\CheckChoice::make($name, $label, $choices, $checked, $options);
    }

    /**
     * Create a Bootstrap radio input.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  string  $value
     * @param  bool    $checked
     * @param  array   $options
     * @return string
     */
    public function radio($name, $label = null, $value = null, $checked = null, array $options = [])
    {
        $options['tag'] = 'radio';
        return Inputs\CheckInput::make($name, $label, $value, $checked, $options);
    }

    /**
     * Create a collection of Bootstrap radio inputs.
     *
     * @param  string  $name
     * @param  string  $label
     * @param  array   $choices
     * @param  mixed   $checked
     * @param  array   $options
     * @return string
     */
    public function radios($name, $label = null, array $choices = [], $checked = null, array $options = [])
    {
        $options['tag'] = 'radio';
        return Inputs\CheckChoice::make($name, $label, $choices, $checked, $options);
    }

    ### BUILD MISC ELEMENTS ####################################################

    /**
     * Create a Bootstrap label.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function label($name, $value = null, array $options = [], $escapeHtml = false)
    {
        return $this->form->label($name, $value, $options, $escapeHtml);
    }

    /**
     * Create a Boostrap submit button.
     *
     * @param  string  $value
     * @param  mixed   $options
     * @return string
     */
    public function submit($value = null, $options = 'primary')
    {
        if (is_array($options)) {
            $options = array_merge(['class' => 'btn btn-primary'], $options);
        } else {
            $options = ['class' => 'btn btn-' . $options];
        }

        return $this->form->submit($value, $options);
    }

    /**
     * Create a Boostrap reset button.
     *
     * @param  string  $value
     * @param  mixed   $options
     * @return string
     */
    public function reset($value = null, $options = 'danger')
    {
        if (is_array($options)) {
            $options = array_merge(['class' => 'btn btn-danger'], $options);
        } else {
            $options = ['class' => 'btn btn-' . $options];
        }

        return $this->form->reset($value, $options);
    }

    /**
     * Create a Boostrap button.
     *
     * @param  string  $value
     * @param  mixed   $options
     * @return string
     */
    public function button($value = null, $options = 'primary')
    {
        if (is_array($options)) {
            $options = array_merge(['class' => 'btn btn-primary'], $options);
        } else {
            $options = ['class' => 'btn btn-' . $options];
        }

        return $this->form->button($value, $options);
    }
}