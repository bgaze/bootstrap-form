<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\View\Components\Concerns\ResolvesBootstrapAttributes;
use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\View\Component;

/**
 * Base class for every bootstrap-form x-component.
 *
 * Rendering is pure PHP (no Blade view file). It relies on a single mechanism, defined once
 * here so concrete components only implement renderHtml():
 *
 *  - render() returns a closure that yields an Htmlable, so renderComponent() emits the
 *    markup as-is (no Blade recompilation).
 *  - resolveView() returns that closure untouched, bypassing the default resolver (which
 *    would recompile a returned string as a Blade template).
 *  - the closure receives Blade's component data, notably the buffered default slot
 *    ($data['slot']); slotless components (fields, buttons) simply ignore it, while the
 *    stateful <x-bf::form> wraps it.
 *
 * Note on lifecycle: Blade calls withAttributes() AFTER render() but BEFORE the slot is
 * buffered and BEFORE the closure is evaluated. The attribute bag is therefore always
 * populated by the time renderHtml() runs.
 */
abstract class BootstrapComponent extends Component
{
    use ResolvesBootstrapAttributes;

    /**
     * Produce the component markup. $data carries Blade's component data (notably the
     * buffered default slot as $data['slot']).
     *
     * @param  array<string, mixed>  $data
     */
    abstract protected function renderHtml(array $data): string;

    public function render(): Closure
    {
        return fn (array $data): Htmlable => new HtmlString($this->renderHtml($data));
    }

    public function resolveView(): Closure
    {
        return $this->render();
    }

    /**
     * The rendered default slot, or null when empty — for components whose value/label can be
     * provided either as an attribute or as slot content (buttons, link, label).
     *
     * @param  array<string, mixed>  $data
     */
    protected function slotContent(array $data): ?string
    {
        $slot = isset($data['slot']) ? (string) $data['slot'] : '';

        return $slot !== '' ? $slot : null;
    }
}
