<?php

namespace Bgaze\BootstrapForm\Support\Traits;

/**
 * Specific settings:
 * 
 * @property mixed $prepend
 * @property mixed $append
 */
trait HasAddons {

    /**
     * Build a Bootstrap input group if append or prepend options are provided.
     * 
     * @return string
     */
    public function inputGroup() {
        if (!$this->append && !$this->prepend) {
            return $this->input();
        }

        $prepend = '';
        if ($this->prepend) {
            $content = is_array($this->prepend) ? implode('', $this->prepend) : $this->prepend;
            $prepend = $this->html->tag('div', $content, ['class' => 'input-group-prepend']);
        }

        $append = '';
        if ($this->append) {
            $content = is_array($this->append) ? implode('', $this->append) : $this->append;
            $append = $this->html->tag('div', $content, ['class' => 'input-group-append']);
        }

        $class = 'input-group';
        if ($this->size === 'sm') {
            $class .= ' input-group-sm';
        } elseif ($this->size === 'lg') {
            $class .= ' input-group-lg';
        }

        return $this->html->tag('div', $prepend . $this->input() . $append, ['class' => $class])->toHtml();
    }

}
