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
 * Input settings (dynamic settings accessed via HasSettings):
 *
 * @property string $errors
 * @property string $name
 * @property mixed $value
 * @property mixed $label
 * @property string|false $help
 * @property string|false $success
 *
 * Inherited from form:
 * @property string $layout
 * @property int $bootstrap_version
 * @property bool $custom
 * @property string $error_bag
 * @property bool $show_all_errors
 * @property bool $show_valid_feedback
 * @property string|false $required_mark
 * @property string|false $pull_right
 * @property string $left_class
 * @property string $right_class
 * @property string|false $hspace
 * @property string|false $vspace
 * @property string|false $lspace
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

    /**
     * Whether the field is in a "valid" state (submitted, no error, feature enabled).
     */
    protected bool $isValid = false;

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

    // ## CONFIGURATION ##########################################################

    protected function defaults(): Collection
    {
        return BF::settings()->merge([
            'help' => false,
            'success' => false,
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
        $this->driver = DriverManager::make((int) $this->settings->get('bootstrap_version', 5));
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

        if (! $this->group_attributes->id) {
            $this->group_attributes->id = $this->flattenName($this->name, '-').'-group';
        }
    }

    protected function errorTemplate(): string
    {
        return '<div class="'.$this->driver->feedbackClass($this->feedbackIsBlock()).'"'.$this->feedbackId('error').'>:message</div>';
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

        return is_null($id) ? '' : ' id="'.$id.'-'.$suffix.'"';
    }

    /**
     * Whether the field renders its own validation feedback. Choice children disable it
     * (feedback is rendered once at the collection level).
     */
    protected function rendersOwnFeedback(): bool
    {
        return true;
    }

    /**
     * Whether an error message is actually rendered for this field (drives the
     * aria-describedby error reference).
     */
    protected function errorsAreRendered(): bool
    {
        return $this->errors !== '' && $this->rendersOwnFeedback();
    }

    /**
     * Whether a valid-feedback message is actually rendered for this field.
     */
    protected function validFeedbackIsRendered(): bool
    {
        return $this->isValid && $this->success !== false && $this->rendersOwnFeedback();
    }

    /**
     * The valid-feedback markup, or '' when there is no success message to render.
     */
    protected function validFeedback(): string
    {
        if (! $this->validFeedbackIsRendered()) {
            return '';
        }

        $attributes = ['class' => $this->driver->validFeedbackClass($this->feedbackIsBlock())];
        if (! is_null($this->fieldId())) {
            $attributes['id'] = $this->fieldId().'-valid';
        }

        return $this->html->tag('div', $this->success, $attributes)->toHtml();
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
        if (! $this->hasSingleInput() || is_null($this->fieldId())) {
            return;
        }

        $describedby = [];

        if ($this->errorsAreRendered()) {
            $describedby[] = $this->fieldId().'-error';
        } elseif ($this->validFeedbackIsRendered()) {
            $describedby[] = $this->fieldId().'-valid';
        }

        if ($this->help !== false) {
            $describedby[] = $this->fieldId().'-help';
        }

        if ($describedby !== []) {
            $this->input_attributes->{'aria-describedby'} = implode(' ', $describedby);
        }

        if ($this->errors !== '') {
            $this->input_attributes->{'aria-invalid'} = 'true';
        } elseif ($this->isValid) {
            $this->input_attributes->{'aria-invalid'} = 'false';
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
        if (! $errorBag) {
            return;
        }

        $field = $this->flattenName($this->name, '.');
        if (! $errorBag->has($field)) {
            // The form was submitted (a bag exists) and no error concerns this field.
            $this->setValidState();

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

    /**
     * Flag the field (and its group) valid, when the valid-feedback feature is enabled.
     * Mutually exclusive with the error state: only reached when the field has no error.
     */
    protected function setValidState(): void
    {
        if (! $this->show_valid_feedback) {
            return;
        }

        $this->isValid = true;
        $this->input_attributes->addClass($this->driver->validClass());
        $this->group_attributes->addClass($this->driver->validClass());
    }

    // ## COMPONENTS #############################################################

    abstract public function input(): string;

    /**
     * Decorate the input to get the final Bootstrap format.
     */
    public function inputGroup(): string
    {
        return $this->controlBody();
    }

    /**
     * The core control markup placed in the group's input area: the bare input, or — in
     * the floating layout — the input wrapped with its label in a .form-floating block.
     */
    protected function controlBody(): string
    {
        if (! $this->isFloating()) {
            return $this->input();
        }

        if ($this->floatingNeedsPlaceholder() && is_null($this->input_attributes->placeholder)) {
            $this->input_attributes->placeholder = ' ';
        }

        return $this->driver->floatingGroup($this->html, $this->input(), $this->label());
    }

    /**
     * Whether the field renders in the floating-label layout (a floatable field, the
     * floating layout selected, and a driver that supports it).
     */
    protected function isFloating(): bool
    {
        return $this->layout === 'floating' && $this->isFloatable() && $this->driver->supportsFloating();
    }

    /**
     * Whether this field type can render as a floating-label control.
     */
    protected function isFloatable(): bool
    {
        return false;
    }

    /**
     * Whether a placeholder must be injected for the floating CSS to work (text-like
     * controls); overridden to false where it is unnecessary or harmful (e.g. selects).
     */
    protected function floatingNeedsPlaceholder(): bool
    {
        return true;
    }

    public function label(): string
    {
        if ($this->label === false) {
            return '';
        }

        return $this->elements->label($this->input_attributes->id, $this->labelValue(), $this->label_attributes->toArray(), false)->toHtml();
    }

    /**
     * The label content with the required mark appended when applicable. The mark rides
     * the label's (unescaped) render path, so HTML marks are emitted verbatim.
     */
    protected function labelValue(): mixed
    {
        $mark = $this->requiredMark();

        return $mark === '' ? $this->label : $this->label.$mark;
    }

    /**
     * The required mark to append to the label, or '' when the field is not required or
     * the feature is disabled (required_mark set to false / null / '').
     */
    protected function requiredMark(): string
    {
        $mark = $this->required_mark;

        if ($mark === false || $mark === null || $mark === '' || ! $this->isRequired()) {
            return '';
        }

        return (string) $mark;
    }

    /**
     * Whether the field carries the HTML "required" attribute, in any of its forms:
     * ['required' => true], ['required' => 'required'] or the bare ['required'].
     */
    protected function isRequired(): bool
    {
        foreach ($this->input_attributes->all() as $key => $value) {
            if ($key === 'required') {
                return $value !== false && $value !== null;
            }

            if (is_int($key) && $value === 'required') {
                return true;
            }
        }

        return false;
    }

    public function help(): string
    {
        if ($this->help === false) {
            return '';
        }

        $attributes = [];
        if (! is_null($this->fieldId())) {
            $attributes['id'] = $this->fieldId().'-help';
        }
        $attributes['class'] = $this->driver->helpClass();

        return $this->html->tag('small', $this->help, $attributes)->toHtml();
    }

    public function group(): string
    {
        if (! $this->group) {
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

        $content = $this->leftGroupColumn().$this->rightGroupColumn();

        return $this->html->tag('div', $content, $this->group_attributes->toArray())->toHtml();
    }

    /**
     * The "left" part of the form group: label, or a spacer in "pull right" mode.
     */
    protected function leftGroupColumn(): string
    {
        // In the floating layout the label lives inside the .form-floating wrapper.
        if ($this->isFloating()) {
            return '';
        }

        if ($this->layout === 'horizontal' && $this->pull_right && ! $this->label) {
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
        $content .= $this->validFeedback();
        $content .= $this->help();

        $attributes = Attributes::make();

        if ($this->layout === 'horizontal') {
            $attributes->addClass($this->pull_right ? 'col' : $this->right_class);
        }

        return $this->html->tag('div', $content, $attributes->toArray())->toHtml();
    }
}
