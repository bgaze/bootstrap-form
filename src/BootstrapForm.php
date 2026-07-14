<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm;

use Bgaze\BootstrapForm\Support\Attributes;
use Bgaze\BootstrapForm\Support\Drivers\DriverManager;
use Bgaze\BootstrapForm\Support\Drivers\VersionDriver;
use Bgaze\BootstrapForm\Support\FieldValue;
use Bgaze\BootstrapForm\Support\FormContext;
use Bgaze\BootstrapForm\Support\FormElements;
use Bgaze\BootstrapForm\Support\Html;
use Bgaze\BootstrapForm\Support\Input;
use Bgaze\BootstrapForm\Support\Options;
use Bgaze\BootstrapForm\Support\Traits\HasSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

/**
 * Form configuration (dynamic settings accessed via HasSettings):
 *
 * @property mixed $model
 * @property string $error_bag
 * @property string|array $url
 * @property string|array $route
 * @property string|array $action
 * @property string|array $store
 * @property string|array $update
 * @property string $layout
 * @property int $bootstrap_version
 * @property bool $custom
 * @property string $left_class
 * @property string $right_class
 * @property string|false $hspace
 * @property string|false $vspace
 * @property string|false $lspace
 * @property bool $show_all_errors
 * @property bool $show_valid_feedback
 * @property bool|array $group
 */
class BootstrapForm
{
    use HasSettings;
    use Macroable;

    /**
     * Configuration keys that won't be inherited by form inputs.
     */
    const RESERVED = ['model', 'url', 'route', 'action', 'update', 'store'];

    /**
     * The form attribute set.
     */
    protected Attributes $attributes;

    /**
     * Whether a form is currently opened.
     */
    protected bool $opened = false;

    public function __construct(
        protected readonly Html $html,
        protected readonly FormElements $elements,
        protected readonly FieldValue $fieldValue,
        protected readonly FormContext $context,
    ) {
        $this->resetForm();
    }

    // ## MISC UTILITIES #########################################################

    /**
     * Returns the "inheritable" forms settings.
     */
    public function settings(): Collection
    {
        return $this->settings->except(static::RESERVED);
    }

    public function html(): Html
    {
        return $this->html;
    }

    public function elements(): FormElements
    {
        return $this->elements;
    }

    public function fieldValue(): FieldValue
    {
        return $this->fieldValue;
    }

    public function context(): FormContext
    {
        return $this->context;
    }

    // ## CONFIGURATION ##########################################################

    /**
     * Reset form settings and attributes to default configuration.
     */
    protected function resetForm(): void
    {
        $config = config('bootstrap_form');
        $version = (int) ($config['bootstrap_version'] ?? 5);

        // Default options: agnostic root settings + the active version layout section.
        // Assembled as a plain (heterogeneous) array, then wrapped once.
        $settings = array_fill_keys(self::RESERVED, null);
        $settings['error_bag'] = 'default';
        $settings = array_merge($settings, Arr::except($config, ['blade_directives', 'bootstrap4', 'bootstrap5']));
        // "custom" is always a known setting (a no-op in Bootstrap 5) so it is never
        // mistaken for an HTML attribute, even when absent from the version section.
        $settings['custom'] = false;
        $settings = array_merge($settings, $this->versionLayout($version));
        $settings['bootstrap_version'] = $version;

        $this->settings = Collection::make($settings);

        if (! is_array($this->group)) {
            $this->group = [];
        }

        $this->attributes = Attributes::make(['role' => 'form']);
        $this->opened = false;
    }

    /**
     * Get the layout options declared for a Bootstrap version.
     */
    protected function versionLayout(int $version): array
    {
        return config('bootstrap_form.bootstrap'.$version, []);
    }

    /**
     * Get the driver for the active Bootstrap version.
     */
    public function driver(): VersionDriver
    {
        return DriverManager::make((int) $this->bootstrap_version);
    }

    /**
     * Init form with the provided option set.
     */
    protected function initForm(array $options = []): void
    {
        // Switch the version layout defaults if the version is overridden at open().
        if (isset($options['bootstrap_version'])) {
            $version = (int) $options['bootstrap_version'];
            $this->settings = $this->settings
                ->merge($this->versionLayout($version))
                ->put('bootstrap_version', $version);
        }

        // Merge with provided options.
        $this->settings = $this->settings->merge(Options::settings($options, $this->settings->keys()));

        // Manage group option.
        if (isset($options['group']) && $options['group'] === false) {
            $this->group = false;
        } elseif (isset($options['group']) && is_array($options['group'])) {
            $this->group = array_merge($this->group, $options['group']);
        }

        // Set form attributes.
        $this->attributes = $this->attributes->merge(Options::attributes($options, $this->settings->keys()));

        // Set form class based on form layout.
        $layoutClass = $this->driver()->formLayoutClass($this->layout);
        if ($layoutClass !== '') {
            $this->attributes->addClass($layoutClass);
        }

        // Use explicitly passed URL, route or action.
        foreach (['url', 'route', 'action'] as $action) {
            if ($this->{$action}) {
                $this->attributes->{$action} = $this->{$action};

                return;
            }
        }
    }

    /**
     * Init model-related form settings.
     */
    protected function initModelForm(Model $model): void
    {
        if ($model->exists && $this->update) {
            $this->attributes->method = 'PUT';

            if (is_array($this->update)) {
                $type = Str::contains(Arr::first($this->update), '@') ? 'action' : 'route';
                $update = $this->update;
                $update[] = $model->getRouteKey();
                $this->attributes->{$type} = $update;
            } else {
                $type = Str::contains($this->update, '@') ? 'action' : 'route';
                $this->attributes->{$type} = [$this->update, $model->getRouteKey()];
            }
        } elseif (! $model->exists && $this->store) {
            $this->attributes->method = 'POST';

            $name = is_array($this->store) ? Arr::first($this->store) : $this->store;
            $type = Str::contains($name, '@') ? 'action' : 'route';
            $this->attributes->{$type} = $this->store;
        }
    }

    // ## BUILD FORM ELEMENT #####################################################

    public function open(array $options = []): string
    {
        $this->initForm($options);
        $this->opened = true;

        if ($this->model instanceof Model) {
            $this->initModelForm($this->model);

            return $this->elements->model($this->model, $this->attributes->toArray())->toHtml();
        }

        return $this->elements->open($this->attributes->toArray())->toHtml();
    }

    public function close(): string
    {
        $this->resetForm();

        return $this->elements->close()->toHtml();
    }

    public function vertical(array $options = []): string
    {
        $options['layout'] = 'vertical';

        return $this->open($options);
    }

    public function inline(array $options = []): string
    {
        $options['layout'] = 'inline';

        return $this->open($options);
    }

    public function horizontal(array $options = []): string
    {
        $options['layout'] = 'horizontal';

        return $this->open($options);
    }

    /**
     * Open a form using the Bootstrap 5 floating-label layout (degrades to vertical on
     * Bootstrap 4 and for non-floatable fields).
     */
    public function floating(array $options = []): string
    {
        $options['layout'] = 'floating';

        return $this->open($options);
    }

    // ## BUILD FORM INPUTS ######################################################

    /**
     * Create a Bootstrap input by tag.
     */
    protected function input(string $tag, string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        $options['tag'] = $tag;

        return Inputs\TextInput::make($name, $label, $value, $options);
    }

    public function text(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('text', $name, $label, $value, $options);
    }

    public function email(string $name = 'email', mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('email', $name, $label, $value, $options);
    }

    public function url(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('url', $name, $label, $value, $options);
    }

    public function tel(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('tel', $name, $label, $value, $options);
    }

    public function number(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('number', $name, $label, $value, $options);
    }

    public function date(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('date', $name, $label, $value, $options);
    }

    public function time(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('time', $name, $label, $value, $options);
    }

    public function datetimeLocal(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('datetimeLocal', $name, $label, $value, $options);
    }

    public function month(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('month', $name, $label, $value, $options);
    }

    public function week(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('week', $name, $label, $value, $options);
    }

    public function search(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('search', $name, $label, $value, $options);
    }

    public function password(string $name, mixed $label = null, array $options = []): Input
    {
        return $this->input('password', $name, $label, null, $options);
    }

    public function color(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('color', $name, $label, $value, $options);
    }

    public function textarea(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return $this->input('textarea', $name, $label, $value, $options);
    }

    public function select(string $name, mixed $label = null, iterable $choices = [], mixed $selected = null, array $options = []): Input
    {
        return Inputs\SelectInput::make($name, $label, $choices, $selected, $options);
    }

    public function file(string $name, mixed $label = null, array $options = []): Input
    {
        return Inputs\FileInput::make($name, $label, $options);
    }

    public function range(string $name, mixed $label = null, mixed $value = null, array $options = []): Input
    {
        return Inputs\RangeInput::make($name, $label, $value, $options);
    }

    public function hidden(string $name, mixed $value = null, array $options = []): HtmlString
    {
        $id = $options['id'] ?? null;

        // Same id policy as inputs: false disables, null / '' / blank generates.
        if ($id !== false && (is_null($id) || trim((string) $id) === '')) {
            $options['id'] = $this->flattenName($name, '-');
        }

        return $this->elements->hidden($name, $value, $options);
    }

    public function checkbox(string $name, mixed $label = null, mixed $value = 1, mixed $checked = null, array $options = []): Input
    {
        $options['tag'] = 'checkbox';

        return Inputs\CheckInput::make($name, $label, $value, $checked, $options);
    }

    public function checkboxes(string $name, mixed $label = null, iterable $choices = [], mixed $checked = null, array $options = []): Input
    {
        $options['tag'] = 'checkbox';

        return Inputs\CheckChoice::make($name, $label, $choices, $checked, $options);
    }

    public function radio(string $name, mixed $label = null, mixed $value = null, mixed $checked = null, array $options = []): Input
    {
        $options['tag'] = 'radio';

        return Inputs\CheckInput::make($name, $label, $value, $checked, $options);
    }

    public function radios(string $name, mixed $label = null, iterable $choices = [], mixed $checked = null, array $options = []): Input
    {
        $options['tag'] = 'radio';

        return Inputs\CheckChoice::make($name, $label, $choices, $checked, $options);
    }

    // ## BUILD MISC ELEMENTS ####################################################

    public function label(string $name, mixed $value = null, array $options = [], bool $escapeHtml = false): HtmlString
    {
        return $this->elements->label($name, $value, $options, $escapeHtml);
    }

    /**
     * Create the options array for a Bootstrap-styled button.
     */
    protected function buttonOption(string $style, mixed $options): array
    {
        $class = '';

        if ($this->opened && $this->layout === 'inline') {
            if ($this->hspace) {
                $class .= $this->hspace.' ';
            }

            if ($this->vspace) {
                $class .= $this->vspace.' ';
            }
        }

        $class .= $this->driver()->buttonBaseClass();

        if (is_array($options)) {
            return array_merge(['class' => $class.$style], $options);
        }

        return ['class' => $class.(empty($options) ? $style : $options)];
    }

    public function submit(mixed $value = null, mixed $options = null): HtmlString
    {
        return $this->elements->submit($value, $this->buttonOption('primary', $options));
    }

    public function reset(mixed $value = null, mixed $options = null): HtmlString
    {
        return $this->elements->reset($value, $this->buttonOption('danger', $options));
    }

    public function button(mixed $value = null, mixed $options = null): HtmlString
    {
        return $this->elements->button($value, $this->buttonOption('primary', $options));
    }

    public function link(string $url, ?string $title = null, mixed $options = null): HtmlString
    {
        return $this->html->link($url, $title, $this->buttonOption('primary', $options), null, false);
    }
}
