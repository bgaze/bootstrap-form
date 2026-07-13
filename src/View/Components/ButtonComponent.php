<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * Base for the button components (submit, reset, button). The label is given either via the
 * value attribute or as the default slot: <x-bf::submit>Login</x-bf::submit>.
 */
abstract class ButtonComponent extends BootstrapComponent
{
    protected string $type = 'submit';

    public function __construct(
        public mixed $value = null,
    ) {}

    protected function renderHtml(array $data): string
    {
        $value = $this->value ?? $this->slotContent($data);

        return (string) BF::{$this->type}($value, $this->bootstrapOptions());
    }
}
