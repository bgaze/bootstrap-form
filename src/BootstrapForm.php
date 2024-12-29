<?php

namespace Bgaze\BootstrapForm;

use ArrayAccess;
use Bgaze\BootstrapForm\Support\Html\Attributes;
use Bgaze\BootstrapForm\Support\Html\Html;
use Bgaze\BootstrapForm\Support\Traits\HasSettings;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

/**
 * Form configuration:
 *
 * @property ?Model $model
 * @property string $error_bag
 * @property ?bool $files
 * @property ?string $url
 * @property null|string|array $route
 * @property null|string|array $action
 * @property null|string|array $store
 * @property null|string|array $update
 * @property string $layout
 * @property bool $custom
 * @property bool $show_all_errors
 * @property ?array $group
 * @property string $left_class
 * @property string $right_class
 * @property ?string $pull_right
 * @property ?string $lspace
 * @property ?string $hspace
 * @property ?string $vspace
 */
class BootstrapForm
{
    use HasSettings;
    use Macroable;

    /**
     * Configuration keys that won't be inherited by form inputs.
     */
    const RESERVED = ['model', 'url', 'route', 'action', 'update', 'store', 'files'];

    /**
     * The form attribute set.
     */
    protected Attributes $attributes;

    /**
     * Is a form opened ?
     */
    protected bool $form = false;

    public function __construct()
    {
        $this->resetForm();
    }

    // MISC UTILITIES

    /**
     * Get inheritable forms settings.
     */
    public function settings(): Collection
    {
        return $this->settings->except(static::RESERVED);
    }

    /**
     * Get session store.
     */
    public function getSessionStore(): Session
    {
        return Request::session();
    }


    // FORM ELEMENT

    protected function resetForm(): void
    {
        // Set defaults options.
        $reserved = array_fill_keys(self::RESERVED, null);
        $this->settings = Collection::make($reserved)
            ->put('error_bag', 'default')
            ->merge(config('bootstrap_form'))
            ->except('blade_directives');

        // Force an array for group option.
        if (!is_array($this->group)) {
            $this->group = [];
        }

        // Set default attributes.
        $this->attributes = Attributes::make([
            'role' => 'form',
            'accept-charset' => 'utf-8',
            'method' => 'POST'
        ]);

        // Mark form as closed.
        $this->form = false;
    }

    protected function getFormAction(): ?string
    {
        if ($this->url) {
            return URL::to($this->url);
        }

        if ($this->model && !$this->route && !$this->action) {
            $action = null;

            if ($this->model->exists && $this->update) {
                $action = [...Arr::wrap($this->update), $this->model->getRouteKey()];
                $this->attributes->method = 'PUT';
            } elseif (!$this->model->exists && $this->store) {
                $action = Arr::wrap($this->store);
                $this->attributes->method = 'POST';
            }

            if ($action) {
                if (is_array(Arr::first($action)) || Str::contains(Arr::first($action), '@')) {
                    $this->action = $action;
                } else {
                    $this->route = $action;
                }
            }
        }

        if ($action = $this->route ?: $this->action) {
            $parameters = Arr::wrap($action);
            $action = array_shift($parameters);

            if ($this->route) {
                return URL::route($action, $parameters);
            }

            return URL::action($action, $parameters);
        }

        return null;
    }

    /**
     * Open a form.
     */
    public function open(array $options = []): string
    {
        // Mark form as opened.
        $this->form = true;

        // Merge with provided options.
        $settings = collect($options)->only($this->settings->keys())->except('group');
        $this->settings = $this->settings->merge($settings);

        // Manage group option.
        if (isset($options['group']) && $options['group'] === false) {
            $this->group = false;
        } elseif (isset($options['group']) && is_array($options['group'])) {
            $this->group = array_merge($this->group, $options['group']);
        }

        // Ignore model if not an instance of Illuminate\Database\Eloquent\Model
        if (!$this->model instanceof Model) {
            $this->model = null;
        }

        // Set form attributes.
        $attributes = collect($options)->except($this->settings->keys())->except('group');
        $this->attributes = $this->attributes->merge($attributes);

        // Set form class based on form layout.
        if ($this->layout !== 'vertical') {
            $this->attributes->addClass('form-' . $this->layout);
        }

        // Manage enctype
        if ($this->files) {
            $this->attributes->enctype = 'multipart/form-data';
        }

        // Set form action.
        if ($action = $this->getFormAction()) {
            $this->attributes->action = $action;
        }

        return Html::form($this->attributes->toArray())->open();
    }

    /**
     * Reset and close the form.
     */
    public function close(): string
    {
        $this->resetForm();

        return Html::form()->close();
    }

    /**
     * Open a vertical Bootstrap form.
     */
    public function vertical(array $options = []): string
    {
        return $this->open(['layout' => 'vertical'] + $options);
    }

    /**
     * Open an inline Bootstrap form.
     */
    public function inline(array $options = []): string
    {
        return $this->open(['layout' => 'inline'] + $options);
    }

    /**
     * Open a horizontal Bootstrap form.
     */
    public function horizontal(array $options = []): string
    {
        return $this->open(['layout' => 'horizontal'] + $options);
    }

    // BUILD FORM INPUTS

    /**
     * Create a Bootstrap input by tag.
     */
    public function input(string $type, string $name, $label = null, $value = null, array $options = []): string
    {
        return Inputs\TextInput::make($name, $label, $value, compact('type') + $options)->toHtml();
    }

    /**
     * Create a Bootstrap text input.
     */
    public function text(string $name, $label = null, $value = null, array $options = []): string
    {
        return $this->input('text', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap email input.
     */
    public function email(string $name = 'email', $label = null, $value = null, array $options = []): string
    {
        return $this->input('email', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap URL input.
     */
    public function url(string $name, $label = null, $value = null, array $options = []): string
    {
        return $this->input('url', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap tel input.
     */
    public function tel(string $name, $label = null, $value = null, array $options = []): string
    {
        return $this->input('tel', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap number input.
     */
    public function number(string $name, $label = null, $value = null, array $options = []): string
    {
        return $this->input('number', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap date input.
     */
    public function date(string $name, $label = null, $value = null, array $options = []): string
    {
        return $this->input('date', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap time input.
     */
    public function time(string $name, $label = null, $value = null, array $options = []): string
    {
        return $this->input('time', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap password input.
     */
    public function password(string $name, $label = null, array $options = []): string
    {
        return $this->input('password', $name, $label, null, $options);
    }

    /**
     * Create a Bootstrap color input.
     */
    public function color(string $name, $label = null, $value = null, array $options = []): string
    {
        return $this->input('color', $name, $label, $value, $options);
    }

    /**
     * Create a Bootstrap textarea input.
     */
    public function textarea(string $name, $label = null, $value = null, array $options = []): string
    {
        return $this->input('textarea', $name, $label, $value, $options);
    }

    /**
     * Create a select box field.
     */
    public function select(string $name, $label = null, ArrayAccess|array $choices = [], $selected = null, array $options = []): string
    {
        return Inputs\SelectInput::make($name, $label, $choices, $selected, $options)->toHtml();
    }

    /**
     * Create a Boostrap file upload button.
     */
    public function file(string $name, $label = null, array $options = []): string
    {
        return Inputs\FileInput::make($name, $label, $options)->toHtml();
    }

    /**
     * Create a Boostrap file upload button.
     */
    public function range(string $name, $label = null, $value = null, array $options = []): string
    {
        return Inputs\RangeInput::make($name, $label, $value, $options)->toHtml();
    }

    /**
     * Create a Bootstrap checkbox input.
     */
    public function checkbox(string $name, $label = null, $value = 1, $checked = null, array $options = []): string
    {
        return Inputs\CheckInput::make($name, $label, $value, $checked, ['type' => 'checkbox'] + $options)->toHtml();
    }

    /**
     * Create a collection of Bootstrap checkboxes.
     */
    public function checkboxes(string $name, $label = null, array $choices = [], $checked = null, array $options = []): string
    {
        return Inputs\CheckChoice::make($name, $label, $choices, $checked, ['type' => 'checkbox'] + $options)->toHtml();
    }

    /**
     * Create a Bootstrap radio input.
     */
    public function radio(string $name, $label = null, $value = null, $checked = null, array $options = []): string
    {
        return Inputs\CheckInput::make($name, $label, $value, $checked, ['type' => 'radio'] + $options)->toHtml();
    }

    /**
     * Create a collection of Bootstrap radio inputs.
     */
    public function radios(string $name, $label = null, array $choices = [], $checked = null, array $options = []): string
    {
        $options['tag'] = 'radio';

        return Inputs\CheckChoice::make($name, $label, $choices, $checked, ['type' => 'radio'] + $options)->toHtml();
    }

    /**
     * Create a hidden field.
     */
    public function hidden(string $name, $value = null, array $options = []): string
    {
        if (!isset($options['id'])) {
            $options['id'] = $this->flattenName($name, '_');
        }


        return Html::input($options)
            ->attributes([
                'type' => 'hidden',
                'name' => $name,
                'value' => $value
            ])
            ->toHtml();
    }

    // MISC

    /**
     * Create a Bootstrap label.
     */
    public function label(string $for, $content = null, array $options = []): string
    {
        return Html::label($options)->attribute('for', $for)->append($content)->toHtml();
    }

    /**
     * Create options for Boostrap button.
     */
    protected function btn(string $style, array|string|null $options): array
    {
        $attributes = Attributes::make()
            ->addClass('btn')
            ->addClass(sprintf('btn-%s', (!empty($options) && is_string($options)) ? $options : $style));

        if ($this->form && $this->layout === 'inline') {
            if ($this->hspace) {
                $attributes->addClass($this->hspace);
            }

            if ($this->vspace) {
                $attributes->addClass($this->vspace);
            }
        }

        if (!empty($options) && !is_string($options)) {
            $attributes = $attributes->merge($options);
        }

        return $attributes->toArray();
    }

    /**
     * Create a Boostrap submit input.
     */
    public function submit($value = null, array|string|null $options = null): string
    {
        return Html::input($this->btn('primary', $options))
            ->attributes(['type' => 'submit', 'value' => $value])
            ->toHtml();
    }

    /**
     * Create a Boostrap reset input.
     */
    public function reset($value = null, array|string|null $options = null): string
    {
        return Html::input($this->btn('danger', $options))
            ->attributes(['type' => 'reset', 'value' => $value])
            ->toHtml();
    }

    /**
     * Create a Boostrap button.
     */
    public function button($content = null, array|string|null $options = null): string
    {
        return Html::button(['type' => 'button'])
            ->attributes($this->btn('primary', $options))
            ->append($content)
            ->toHtml();
    }

    /**
     * Create a Boostrap link button.
     */
    public function link(string $href, $content = null, array|string|null $options = null): string
    {
        return Html::a($this->btn('primary', $options))
            ->attribute('href', $href)
            ->append($content)
            ->toHtml();
    }
}
