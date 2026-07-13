<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Support\Collection;

/**
 * Resolves the value / checked / selected state of a field from old input, the request
 * flash and the bound model.
 *
 * Ported verbatim from the former Collective FormBuilder, minus its unreachable paths
 * (considerRequest/request and the labels-based id fallback), which are iso to drop
 * here: the request feature was never enabled and inputs always carry an explicit id.
 */
class FieldValue
{
    /**
     * The type of the field currently being resolved (drives old-input array shifting).
     */
    protected ?string $type = null;

    /**
     * Per-key accumulator for shifting repeated old-input values.
     */
    protected array $payload = [];

    public function __construct(protected readonly FormContext $context)
    {
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function value(?string $name, mixed $value = null): mixed
    {
        if (is_null($name)) {
            return $value;
        }

        $old = $this->old($name);

        if (!is_null($old) && $name !== '_method') {
            return $old;
        }

        // With the ConvertEmptyStringsToNull middleware, a failed submit repopulates
        // an untouched field as empty rather than from the model.
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

        return null;
    }

    public function old(string $name): mixed
    {
        $session = $this->context->session();

        if (!isset($session)) {
            return null;
        }

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

    public function oldInputIsEmpty(): bool
    {
        $session = $this->context->session();

        return isset($session) && count((array) $session->getOldInput()) === 0;
    }

    /**
     * Whether an option value is selected. Returns 'selected'|null, or a bool for the
     * legacy int-value / bool-selected comparison.
     */
    public function selected(mixed $value, mixed $selected): string|bool|null
    {
        if (is_array($selected)) {
            return in_array($value, $selected, true) || in_array((string) $value, $selected, true) ? 'selected' : null;
        }

        if ($selected instanceof Collection) {
            return $selected->contains($value) ? 'selected' : null;
        }

        if (is_int($value) && is_bool($selected)) {
            return (bool) $value === $selected;
        }

        return ((string) $value === (string) $selected) ? 'selected' : null;
    }

    public function checked(string $type, string $name, mixed $value, mixed $checked): bool
    {
        $this->type = $type;

        return match ($type) {
            'checkbox' => $this->getCheckboxCheckedState($name, $value, $checked),
            'radio' => $this->getRadioCheckedState($name, $value, $checked),
            default => $this->compareValues($name, $value),
        };
    }

    protected function getCheckboxCheckedState(string $name, mixed $value, mixed $checked): bool
    {
        $session = $this->context->session();

        if (isset($session) && !$this->oldInputIsEmpty() && is_null($this->old($name))) {
            return false;
        }

        if ($this->missingOldAndModel($name)) {
            return (bool) $checked;
        }

        $posted = $this->value($name, $checked);

        if (is_array($posted)) {
            return in_array($value, $posted);
        }

        if ($posted instanceof Collection) {
            return $posted->contains('id', $value);
        }

        return (bool) $posted;
    }

    protected function getRadioCheckedState(string $name, mixed $value, mixed $checked): bool
    {
        if ($this->missingOldAndModel($name)) {
            return (bool) $checked;
        }

        return $this->compareValues($name, $value);
    }

    protected function compareValues(string $name, mixed $value): bool
    {
        return $this->value($name) == $value;
    }

    protected function missingOldAndModel(string $name): bool
    {
        return is_null($this->old($name)) && is_null($this->modelValue($name));
    }

    protected function modelValue(string $name): mixed
    {
        $key = $this->transformKey($name);
        $model = $this->context->getModel();

        if (is_object($model) && method_exists($model, 'getFormValue')) {
            return $model->getFormValue($key);
        }

        return data_get($model, $key);
    }

    public function transformKey(string $key): string
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }
}
