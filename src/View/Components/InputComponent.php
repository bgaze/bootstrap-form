<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * Base for the text-like field components (text, email, number, date, textarea, range, ...).
 *
 * Each concrete tag sets $type to the backing BF builder method; the signature
 * (name, label, value, options) is shared. Delegating to BF guarantees byte-identical markup
 * with the facade and directives.
 */
abstract class InputComponent extends BootstrapComponent
{
    /**
     * The BF builder method backing this input.
     */
    protected string $type = 'text';

    public function __construct(
        public string $name,
        public mixed $label = null,
        public mixed $value = null,
    ) {}

    protected function renderHtml(array $data): string
    {
        return (string) BF::{$this->type}($this->name, $this->label, $this->value, $this->bootstrapOptions());
    }
}
