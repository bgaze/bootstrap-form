<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\Component;

/**
 * The <x-bf::text> component (PoC).
 *
 * Delegates to BF::text() so the rendered markup is byte-identical to the facade and the
 * "text" Blade directive.
 *
 * The attribute bag is only populated AFTER render() runs (Blade calls withAttributes()
 * once render() has already returned). Rendering is therefore deferred to toHtml(), invoked
 * by renderComponent() when the bag is ready; returning an Htmlable also makes Blade emit
 * the HTML as-is (no recompilation).
 */
class Text extends Component implements Htmlable
{
    public function __construct(
        public string $name,
        public mixed $label = null,
        public mixed $value = null,
    ) {}

    public function render(): Htmlable
    {
        return $this;
    }

    public function toHtml(): string
    {
        return (string) BF::text($this->name, $this->label, $this->value, $this->attributes->getAttributes());
    }
}
