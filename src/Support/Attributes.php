<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Contracts\Support\Arrayable;

/**
 * A small ordered HTML attribute bag.
 *
 * Deliberately not a Collection subclass: it exposes only the operations the package
 * needs, and insertion order is significant (it drives the rendered attribute order the
 * characterization oracle depends on).
 *
 * The LITERAL_PREFIX escape hatch lets a caller emit an HTML attribute whose name
 * collides with an internal setting (e.g. `size`): a key written `~size` bypasses the
 * settings filter and the prefix is stripped at render time (toArray) to emit `size`.
 * Only toArray strips it; all() keeps it raw so the escape survives intermediate merges
 * (e.g. building a checkbox collection's children).
 *
 * @implements Arrayable<string, mixed>
 */
class Attributes implements Arrayable
{
    /**
     * Prefix marking a literal HTML attribute that must escape the settings filter.
     */
    public const LITERAL_PREFIX = '~';

    public function __construct(protected array $items = [])
    {
    }

    public static function make(array $items = []): static
    {
        return new static($items);
    }

    public function __get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->items[$key] = $value;
    }

    /**
     * Append one or more classes, de-duplicating while preserving order.
     */
    public function addClass(string $class): static
    {
        if (empty(trim($class))) {
            return $this;
        }

        if (is_null($this->class)) {
            $this->class = $class;

            return $this;
        }

        $classes = preg_split('/\s+/', trim($this->class . ' ' . $class));
        $this->class = implode(' ', array_unique($classes));

        return $this;
    }

    /**
     * Merge the given items (existing keys keep their position); returns a new bag.
     */
    public function merge(array $items): static
    {
        return new static(array_merge($this->items, $items));
    }

    /**
     * Raw items, with the literal prefix left intact (for intermediate merges).
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Render-ready attributes: the literal prefix is stripped here (and only here).
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->items as $key => $value) {
            if (is_string($key) && str_starts_with($key, self::LITERAL_PREFIX)) {
                $key = substr($key, 1);
            }

            $array[$key] = $value;
        }

        return $array;
    }
}
