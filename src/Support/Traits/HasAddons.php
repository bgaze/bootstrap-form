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
     * Get the template to build up error messages.
     */
    protected function errorTemplate()
    {
        return '<div class="' . $this->driver->feedbackClass((bool) ($this->append || $this->prepend)) . '">:message</div>';
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

        // Resolve prepend / append content.
        $prepend = $this->prepend
            ? (is_array($this->prepend) ? implode('', $this->prepend) : $this->prepend)
            : '';

        $append = $this->append
            ? (is_array($this->append) ? implode('', $this->append) : $this->append)
            : '';

        // Let the driver assemble the input group (structure differs across versions).
        return $this->driver->inputGroup($this->html, $prepend, $this->input(), $append, $this->size);
    }
}
