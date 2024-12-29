<?php

namespace Bgaze\BootstrapForm\Support\Traits;

use Bgaze\BootstrapForm\Support\Html\Html;
use Bgaze\BootstrapForm\Support\Html\HtmlElement;

/**
 * @method string input()
 * @property mixed $prepend
 * @property mixed $append
 */
trait HasAddons
{
    /**
     * Build a Bootstrap input group if append or prepend options are provided.
     */
    public function inputGroup(): string
    {
        return $this->buildInputGroup($this->input());
    }

    protected function buildInputGroup(string $input): string
    {
        if ($this->append || $this->prepend) {
            $group = Html::div()->append($input)->addClass([
                'input-group',
                'input-group-sm' => ($this->size === 'sm'),
                'input-group-lg' => ($this->size === 'lg'),
            ]);

            if ($this->prepend) {
                Html::div()
                    ->addClass('input-group-prepend')
                    ->append($this->prepend)
                    ->prependTo($group);
            }

            if ($this->append) {
                Html::div()
                    ->addClass('input-group-append')
                    ->append($this->append)
                    ->appendTo($group);
            }

            return $group->toHtml();
        }

        return $input;
    }
}
