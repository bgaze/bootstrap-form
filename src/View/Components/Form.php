<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;
use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\View\Component;

/**
 * The <x-bf::form> component.
 *
 * The builder is stateful: BF::open() mutates the shared form state that inner field
 * components read at render time. Blade buffers the default slot (the fields) between
 * withAttributes() and renderComponent(), so the form state must be established in
 * withAttributes() — the earliest hook where the attribute bag is populated and which still
 * runs BEFORE the slot is rendered.
 *
 * Rendering stays pure PHP (no Blade view): render() returns a closure that, evaluated by
 * renderComponent() with the buffered slot, yields an Htmlable wrapping open + slot + close.
 * resolveView() returns that closure as-is to bypass the default resolver, which would
 * otherwise recompile a returned string as a Blade template.
 */
class Form extends Component
{
    /**
     * The captured <form ...> open tag (built once the attribute bag is known).
     */
    protected string $openTag = '';

    public function withAttributes(array $attributes): static
    {
        parent::withAttributes($attributes);

        // Establish the form state before the slot (fields) is buffered.
        $this->openTag = BF::open($this->buildOptions());

        return $this;
    }

    /**
     * Map the component attribute bag to the BF::open() options array.
     *
     * PoC scope: pass the raw attributes through. Attribute normalization (kebab->snake,
     * label:/group:/input: prefixes) is wired in the shared resolver trait later.
     */
    protected function buildOptions(): array
    {
        return $this->attributes->getAttributes();
    }

    public function render(): Closure
    {
        // $data carries the already-buffered slot (the fields, rendered against the state set
        // in withAttributes()). close() resets the state once the slot has been assembled.
        return fn (array $data): Htmlable => new HtmlString($this->openTag.$data['slot'].BF::close());
    }

    /**
     * Return the render closure untouched so renderComponent() emits its Htmlable result
     * as-is. The default resolver would recompile a string return as Blade — avoided here.
     */
    public function resolveView(): Closure
    {
        return $this->render();
    }
}
