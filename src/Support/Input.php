<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support;

use Bgaze\BootstrapForm\Support\Drivers\DriverManager;
use Bgaze\BootstrapForm\Support\Drivers\VersionDriver;
use Bgaze\BootstrapForm\Support\Facades\BF;
use Bgaze\BootstrapForm\Support\Traits\HasSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Input settings:
 *
 * @property string $errors
 * @property string $name
 * @property string $value
 * @property mixed $label
 * @property string $help
 *
 * Inherited from form:
 *
 * @property string $layout
 * @property string $error_bag
 * @property bool $show_all_errors
 * @property bool $pull_right
 * @property string $left_class
 * @property string $right_class
 * @property string $spacer
 * @property bool|array $group
 */
abstract class Input
{
    use HasSettings;

    protected VersionDriver $driver;

    protected Html $html;

    protected FormElements $elements;

    protected Attributes $input_attributes;

    protected Attributes $label_attributes;

    protected Attributes $group_attributes;

    public function __construct(string $name, mixed $label = null, mixed $value = null, array $options = [])
    {
        // Resolve renderers from the active form.
        $this->html = BF::html();
        $this->elements = BF::elements();

        // Get field configuration.
        $this->configure($name, $label, $value, $options);
        $this->setInputAttributes($options);
        $this->setLabelAttributes($options);
        $this->setGroupAttributes($options);
        $this->getErrors();
        $this->setAriaAttributes();
    }

    public function __toString(): string
    {
        return $this->group();
    }

    ### CONFIGURATION ##########################################################

    protected function defaults(): Collection
    {
        return BF::settings()->merge([
            'help' => false,
        ]);
    }

    protected function configure(string $name, mixed $label, mixed $value, array $options): void
    {
        // Default settings, then merge the provided ones.
        $this->settings = static::defaults();
        $this->settings = $this->settings->merge(Options::settings($options, $this->settings->keys()));

        // Add reserved values.
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
        $this->errors = '';

        // Resolve the version driver from the (possibly overridden) settings.
        $this->driver = DriverManager::make((int) $this->settings->get('bootstrap_version', 4));
    }

    protected function setInputAttributes(array $options): void
    {
        $this->input_attributes = Attributes::make(Options::attributes($options, $this->settings->keys()));

        $id = $this->input_attributes->id;

        // id => false disables the attribute; null / '' / whitespace-only triggers generation.
        if ($id !== false && (is_null($id) || trim((string) $id) === '')) {
            $this->input_attributes->id = $this->flattenName($this->name, '-');
        }
    }

    protected function setLabelAttributes(array $options): void
    {
        $label = $this->label;

        if ($label !== false && $label !== '0' && empty($label)) {
            $this->label = str_replace('_', ' ', Str::ucfirst(Str::lower(Str::title($this->name))));
        }

        if (isset($options['label']) && is_array($options['label'])) {
            $this->label_attributes = Attributes::make($options['label']);
        } else {
            $this->label_attributes = Attributes::make();
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

        if (is_array($this->group)) {
            $this->group_attributes = Attributes::make($this->group);
            $this->group = true;
        } else {
            $this->group_attributes = Attributes::make();
        }

        if (!$this->group_attributes->id) {
            $this->group_attributes->id = $this->flattenName($this->name, '-') . '-group';
        }
    }

    protected function errorTemplate(): string
    {
        return '<div class="' . $this->driver->feedbackClass($this->feedbackIsBlock()) . '"' . $this->feedbackId('error') . '>:message</div>';
    }

    /**
     * The generated id of the field, or null when it carries no id (disabled).
     */
    protected function fieldId(): ?string
    {
        $id = $this->input_attributes->id;

        return (is_string($id) && $id !== '') ? $id : null;
    }

    /**
     * An ` id="{fieldId}-{suffix}"` fragment for a feedback/help element, or '' when the
     * field has no id to derive from.
     */
    protected function feedbackId(string $suffix): string
    {
        $id = $this->fieldId();

        return is_null($id) ? '' : ' id="' . $id . '-' . $suffix . '"';
    }

    /**
     * Whether this field's validation feedback is actually rendered (drives the
     * aria-describedby error reference). Choice children disable their own feedback.
     */
    protected function errorsAreRendered(): bool
    {
        return $this->errors !== '';
    }

    /**
     * Whether the field renders as a single describable control. Choice collections
     * render several inputs, so there is no single element to wire aria attributes to.
     */
    protected function hasSingleInput(): bool
    {
        return true;
    }

    /**
     * Link the input to its help / error text (aria-describedby) and flag it invalid,
     * for accessible screen-reader association. No-op when the field has no id or is a
     * multi-input collection.
     */
    protected function setAriaAttributes(): void
    {
        if (!$this->hasSingleInput() || is_null($this->fieldId())) {
            return;
        }

        $describedby = [];

        if ($this->errorsAreRendered()) {
            $describedby[] = $this->fieldId() . '-error';
        }

        if ($this->help !== false) {
            $describedby[] = $this->fieldId() . '-help';
        }

        if ($describedby !== []) {
            $this->input_attributes->{'aria-describedby'} = implode(' ', $describedby);
        }

        if ($this->errors !== '') {
            $this->input_attributes->{'aria-invalid'} = 'true';
        }
    }

    /**
     * Whether the validation feedback must be forced to display as a block.
     * Overridden where the layout requires it (input groups, choice collections).
     */
    protected function feedbackIsBlock(): bool
    {
        return false;
    }

    /**
     * Compile the field errors and flag the input / group as invalid when any exist.
     */
    protected function getErrors(): void
    {
        $errors = BF::context()->session()->get('errors');
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

        $this->input_attributes->addClass($this->driver->invalidClass());
        $this->group_attributes->addClass($this->driver->invalidClass());
    }

    ### COMPONENTS #############################################################

    abstract public function input(): string;

    /**
     * Decorate the input to get the final Bootstrap format.
     */
    public function inputGroup(): string
    {
        return $this->input();
    }

    public function label(): string
    {
        if ($this->label === false) {
            return '';
        }

        return $this->elements->label($this->input_attributes->id, $this->label, $this->label_attributes->toArray(), false)->toHtml();
    }

    public function help(): string
    {
        if ($this->help === false) {
            return '';
        }

        $attributes = [];
        if (!is_null($this->fieldId())) {
            $attributes['id'] = $this->fieldId() . '-help';
        }
        $attributes['class'] = $this->driver->helpClass();

        return $this->html->tag('small', $this->help, $attributes)->toHtml();
    }

    public function group(): string
    {
        if (!$this->group) {
            return $this->inputGroup();
        }

        $this->group_attributes->addClass($this->driver->formGroupClass());

        if ($this->layout === 'horizontal') {
            $this->group_attributes->addClass($this->driver->rowClass());
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
     * The "left" part of the form group: label, or a spacer in "pull right" mode.
     */
    protected function leftGroupColumn(): string
    {
        if ($this->layout === 'horizontal' && $this->pull_right && !$this->label) {
            return $this->html->tag('div', '', ['class' => $this->left_class])->toHtml();
        }

        if ($this->layout === 'horizontal') {
            $this->label_attributes->addClass($this->driver->colFormLabelClass())->addClass($this->left_class);
        } elseif ($labelClass = $this->driver->labelClass()) {
            $this->label_attributes->addClass($labelClass);
        }

        if ($this->layout === 'inline' && $this->lspace) {
            $this->label_attributes->addClass($this->lspace);
        }

        return $this->label();
    }

    /**
     * The "right" part of the form group: input + errors + help wrapped in a div.
     */
    protected function rightGroupColumn(): string
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
