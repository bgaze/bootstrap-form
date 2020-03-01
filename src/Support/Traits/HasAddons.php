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
        if ($this->append || $this->prepend) {
            return '<div class="invalid-feedback d-block">:message</div>';
        }

        return '<div class="invalid-feedback">:message</div>';
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

        // Prepare prepend group.
        $prepend = '';
        if ($this->prepend) {
            $content = is_array($this->prepend) ? implode('', $this->prepend) : $this->prepend;
            $prepend = $this->html->tag('div', $content, ['class' => 'input-group-prepend']);
        }

        // Prepare append group.
        $append = '';
        if ($this->append) {
            $content = is_array($this->append) ? implode('', $this->append) : $this->append;
            $append = $this->html->tag('div', $content, ['class' => 'input-group-append']);
        }

        // Prepare group class.
        $class = 'input-group';
        if ($this->size === 'sm') {
            $class .= ' input-group-sm';
        } elseif ($this->size === 'lg') {
            $class .= ' input-group-lg';
        }

        // Wrap elements.
        return $this->html->tag('div', $prepend . $this->input() . $append, ['class' => $class])->toHtml();
    }
}
