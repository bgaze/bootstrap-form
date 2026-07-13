<?php

namespace Bgaze\BootstrapForm\Support\Traits;

/**
 * Specific settings:
 *
 * @property mixed $prepend
 * @property mixed $append
 */
trait HasAddons
{

    /**
     * Force block display of validation feedback when the input is wrapped in a group.
     *
     * @return bool
     */
    protected function feedbackIsBlock()
    {
        return (bool) ($this->append || $this->prepend);
    }


    /**
     * Build a Bootstrap input group if append or prepend options are provided.
     *
     * @return string
     */
    public function inputGroup()
    {
        // Check if addons.
        if (!$this->append && !$this->prepend) {
            return $this->input();
        }

        // Let the driver assemble the input group (structure differs across versions).
        return $this->driver->inputGroup(
            $this->html,
            $this->resolveAddon($this->prepend),
            $this->input(),
            $this->resolveAddon($this->append),
            $this->size
        );
    }


    /**
     * Flatten a prepend / append option (string or array) to a string ('' when empty).
     *
     * @param  mixed  $addon
     * @return string
     */
    protected function resolveAddon($addon)
    {
        if (!$addon) {
            return '';
        }

        return is_array($addon) ? implode('', $addon) : $addon;
    }
}
