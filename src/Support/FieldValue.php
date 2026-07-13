<?php

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Support\Collection;

/**
 * Resolves the value / checked / selected state of a field from old input, the request
 * flash and the bound model — the binding logic formerly buried in the Collective
 * FormBuilder, extracted as a single testable concern.
 *
 * Behavior is ported verbatim. The dead paths in the original are dropped (they were
 * unreachable from this package, so removing them is iso):
 *  - considerRequest / request(): never enabled (the accessor that toggled it is gone);
 *  - labels / getIdAttribute fallback: inputs always carry an explicit id.
 */
class FieldValue
{
    /**
     * @var FormContext
     */
    protected $context;

    /**
     * The type of the field currently being resolved (drives old-input array shifting).
     *
     * @var string|null
     */
    protected $type = null;

    /**
     * Per-key accumulator for shifting repeated old-input values.
     *
     * @var array
     */
    protected $payload = [];

    public function __construct(FormContext $context)
    {
        $this->context = $context;
    }

    /**
     * Set the type of the field being resolved.
     *
     * @param  string  $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value that should be assigned to the field.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @return mixed
     */
    public function value($name, $value = null)
    {
        if (is_null($name)) {
            return $value;
        }

        $old = $this->old($name);

        if (!is_null($old) && $name !== '_method') {
            return $old;
        }

        if (function_exists('app')) {
            $hasNullMiddleware = app("Illuminate\Contracts\Http\Kernel")
                ->hasMiddleware(ConvertEmptyStringsToNull::class);

            $errors = $this->context->view()->shared('errors');

            if ($hasNullMiddleware
                && is_null($old)
                && is_null($value)
                && !is_null($errors)
                && count(is_countable($errors) ? $errors : []) > 0
            ) {
                return null;
            }
        }

        if (!is_null($value)) {
            return $value;
        }

        if (!is_null($this->context->getModel())) {
            return $this->modelValue($name);
        }
    }

    /**
     * Get a value from the session's old input.
     *
     * @param  string  $name
     * @return mixed
     */
    public function old($name)
    {
        $session = $this->context->session();

        if (isset($session)) {
            $key = $this->transformKey($name);
            $payload = $session->getOldInput($key);

            if (!is_array($payload)) {
                return $payload;
            }

            if (!in_array($this->type, ['select', 'checkbox'])) {
                if (!isset($this->payload[$key])) {
                    $this->payload[$key] = collect($payload);
                }

                if (!empty($this->payload[$key])) {
                    return $this->payload[$key]->shift();
                }
            }

            return $payload;
        }
    }

    /**
     * Determine if the old input is empty.
     *
     * @return bool
     */
    public function oldInputIsEmpty()
    {
        $session = $this->context->session();

        return isset($session) && count((array) $session->getOldInput()) === 0;
    }

    /**
     * Determine if the value is selected (for select options).
     *
     * @param  mixed  $value
     * @param  mixed  $selected
     * @return string|null
     */
    public function selected($value, $selected)
    {
        if (is_array($selected)) {
            return in_array($value, $selected, true) || in_array((string) $value, $selected, true) ? 'selected' : null;
        } elseif ($selected instanceof Collection) {
            return $selected->contains($value) ? 'selected' : null;
        }

        if (is_int($value) && is_bool($selected)) {
            return (bool) $value === $selected;
        }

        return ((string) $value === (string) $selected) ? 'selected' : null;
    }

    /**
     * Get the checked state for a checkable input.
     *
     * @param  string  $type  checkbox | radio
     * @param  string  $name
     * @param  mixed  $value
     * @param  bool  $checked
     * @return bool
     */
    public function checked($type, $name, $value, $checked)
    {
        $this->type = $type;

        switch ($type) {
            case 'checkbox':
                return $this->getCheckboxCheckedState($name, $value, $checked);

            case 'radio':
                return $this->getRadioCheckedState($name, $value, $checked);

            default:
                return $this->compareValues($name, $value);
        }
    }

    /**
     * Get the checked state for a checkbox input.
     */
    protected function getCheckboxCheckedState($name, $value, $checked)
    {
        $session = $this->context->session();

        if (isset($session) && !$this->oldInputIsEmpty() && is_null($this->old($name))) {
            return false;
        }

        if ($this->missingOldAndModel($name)) {
            return $checked;
        }

        $posted = $this->value($name, $checked);

        if (is_array($posted)) {
            return in_array($value, $posted);
        } elseif ($posted instanceof Collection) {
            return $posted->contains('id', $value);
        } else {
            return (bool) $posted;
        }
    }

    /**
     * Get the checked state for a radio input.
     */
    protected function getRadioCheckedState($name, $value, $checked)
    {
        if ($this->missingOldAndModel($name)) {
            return $checked;
        }

        return $this->compareValues($name, $value);
    }

    /**
     * Loosely compare the field value to the given value (model casting friendly).
     */
    protected function compareValues($name, $value)
    {
        return $this->value($name) == $value;
    }

    /**
     * Determine if neither old input nor model input exist for a key.
     */
    protected function missingOldAndModel($name)
    {
        return is_null($this->old($name)) && is_null($this->modelValue($name));
    }

    /**
     * Get the model value that should be assigned to the field.
     *
     * @param  string  $name
     * @return mixed
     */
    protected function modelValue($name)
    {
        $key = $this->transformKey($name);
        $model = $this->context->getModel();

        if ((is_string($model) || is_object($model)) && method_exists($model, 'getFormValue')) {
            return $model->getFormValue($key);
        }

        return data_get($model, $key);
    }

    /**
     * Transform key from array to dot syntax.
     *
     * @param  string  $key
     * @return string
     */
    public function transformKey($key)
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }
}
