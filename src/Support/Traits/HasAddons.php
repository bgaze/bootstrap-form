<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support\Traits;

use Illuminate\Contracts\Support\Htmlable;

/**
 * @property mixed $prepend
 * @property mixed $append
 * @property bool $escape
 */
trait HasAddons
{
    /**
     * Force block display of validation feedback when the input is wrapped in a group.
     */
    protected function feedbackIsBlock(): bool
    {
        return (bool) ($this->append || $this->prepend);
    }

    /**
     * Build a Bootstrap input group if append or prepend options are provided.
     */
    public function inputGroup(): string
    {
        if (! $this->append && ! $this->prepend) {
            return $this->controlBody();
        }

        // Let the driver assemble the input group (structure differs across versions).
        // controlBody() yields the floating-wrapped control when the floating layout is
        // active, so .form-floating nests correctly inside .input-group.
        return $this->driver->inputGroup(
            $this->html,
            $this->resolveAddon($this->prepend),
            $this->controlBody(),
            $this->resolveAddon($this->append),
            $this->size,
        );
    }

    /**
     * Resolve a prepend / append option (string or array) to its addon HTML ('' when empty).
     * An array is treated as several addon items, each resolved independently.
     */
    protected function resolveAddon(mixed $addon): string
    {
        if (! $addon) {
            return '';
        }

        $items = is_array($addon) ? $addon : [$addon];

        return implode('', array_map(
            fn (mixed $item): string => $this->resolveAddonItem($item),
            $items,
        ));
    }

    /**
     * Resolve a single addon item.
     *
     * With `escape` off (the default), the VALUE decides: an item carrying HTML is emitted
     * verbatim — the caller owns the markup (an .input-group-text span, a button, a dropdown…) —
     * while a plain-text item is escaped and wrapped by the driver into the version's text addon,
     * so the common units / currency case needs no boilerplate.
     *
     * With `escape` on, that heuristic is retired and the CALL SITE decides: the item is text,
     * always. A Htmlable item is markup by construction (an explicit HtmlString, a Blade slot),
     * so it keeps the default regime either way — the bypass skips the escaping decision, not
     * the wrapping one, which is why a tag-free Htmlable is still wrapped as a text addon.
     */
    protected function resolveAddonItem(mixed $item): string
    {
        $isMarkup = $item instanceof Htmlable;
        $raw = $isMarkup ? $item->toHtml() : (string) $item;

        if ($raw === '') {
            return '';
        }

        if ($this->escape && ! $isMarkup) {
            return $this->driver->addonText($this->html, $raw);
        }

        return $this->addonContainsHtml($raw)
            ? $raw
            : $this->driver->addonText($this->html, $raw);
    }

    /**
     * Whether an addon value carries HTML markup (an element or comment tag). Detects a tag
     * opening `<` immediately followed by a letter, `!` or `/`, so bare content such as
     * `°C`, `$`, `R&D` or `a < b` is treated as text, not markup.
     */
    protected function addonContainsHtml(string $value): bool
    {
        return preg_match('/<[a-z!\/][^>]*>/i', $value) === 1;
    }
}
