<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support\Traits;

/**
 * @property mixed $prepend
 * @property mixed $append
 */
trait HasAddons
{
    /**
     * Force block display of validation feedback when the input is wrapped in a group.
     */
    protected function feedbackIsBlock(): bool
    {
        return (bool) ($this->append || $this->prepend);
    }

    /**
     * Build a Bootstrap input group if append or prepend options are provided.
     */
    public function inputGroup(): string
    {
        if (!$this->append && !$this->prepend) {
            return $this->controlBody();
        }

        // Let the driver assemble the input group (structure differs across versions).
        // controlBody() yields the floating-wrapped control when the floating layout is
        // active, so .form-floating nests correctly inside .input-group.
        return $this->driver->inputGroup(
            $this->html,
            $this->resolveAddon($this->prepend),
            $this->controlBody(),
            $this->resolveAddon($this->append),
            $this->size,
        );
    }

    /**
     * Flatten a prepend / append option (string or array) to a string ('' when empty).
     */
    protected function resolveAddon(mixed $addon): string
    {
        if (!$addon) {
            return '';
        }

        return is_array($addon) ? implode('', $addon) : (string) $addon;
    }
}
