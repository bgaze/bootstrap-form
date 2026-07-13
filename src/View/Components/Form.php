<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * The <x-bf::form> component.
 *
 * The builder is stateful: BF::open() mutates the shared form state that inner field
 * components read at render time. Blade buffers the default slot (the fields) between
 * withAttributes() and the closure evaluation, so the form state must be established in
 * withAttributes() — the earliest hook where the attribute bag is populated and which still
 * runs BEFORE the slot is rendered. renderHtml() then wraps the buffered slot; close()
 * resets the state once the slot has been assembled.
 */
class Form extends BootstrapComponent
{
    /**
     * Boolean layout shortcuts: <x-bf::form horizontal> is sugar for layout="horizontal".
     */
    private const LAYOUT_SHORTCUTS = ['vertical', 'horizontal', 'inline', 'floating'];

    /**
     * The captured <form ...> open tag (built once the attribute bag is known).
     */
    protected string $openTag = '';

    public function withAttributes(array $attributes): static
    {
        parent::withAttributes($attributes);

        // Establish the form state before the slot (fields) is buffered.
        $this->openTag = BF::open($this->bootstrapOptions());

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function bootstrapOptions(): array
    {
        $options = parent::bootstrapOptions();

        // Resolve boolean layout shortcuts to the layout setting, and never let them leak as
        // HTML attributes on the <form> tag.
        foreach (self::LAYOUT_SHORTCUTS as $layout) {
            if (($options[$layout] ?? null) === true) {
                $options['layout'] = $layout;
            }

            unset($options[$layout]);
        }

        return $options;
    }

    protected function renderHtml(array $data): string
    {
        // $data['slot'] holds the fields, already rendered against the state set above.
        return $this->openTag.$data['slot'].BF::close();
    }
}
