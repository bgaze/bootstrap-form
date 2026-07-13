<?php

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * A small ordered HTML attribute bag.
 *
 * Deliberately NOT a Collection subclass: it exposes only the handful of operations
 * the package needs, and insertion order is significant (it drives the rendered
 * attribute order the characterization oracle depends on).
 *
 * The LITERAL_PREFIX escape hatch lets a caller emit an HTML attribute whose name
 * collides with an internal setting (e.g. `size`): a key written `~size` bypasses the
 * settings filter and the prefix is stripped at render time (toArray) to emit `size`.
 * Only render-time conversion (toArray) strips it; raw access (all) keeps it, so the
 * escape survives intermediate merges (e.g. building a checkbox collection's children).
 */
class Attributes implements Arrayable
{
    /**
     * Prefix marking a literal HTML attribute that must escape the settings filter.
     */
    const LITERAL_PREFIX = '~';

    /**
     * @var array
     */
    protected $items = [];

    public function __construct($items = [])
    {
        $this->items = $this->getArrayableItems($items);
    }

    /**
     * @param  mixed  $items
     * @return static
     */
    public static function make($items = [])
    {
        return new static($items);
    }

    public function __get($key)
    {
        return $this->items[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * Append one or more classes, de-duplicating while preserving order.
     *
     * @param  string  $class
     * @return $this
     */
    public function addClass($class)
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
     * Return a new bag without the given keys (raw keys, order preserved).
     *
     * @param  mixed  $keys
     * @return static
     */
    public function except($keys)
    {
        $keys = $keys instanceof Collection ? $keys->all() : (array) $keys;

        return new static(Arr::except($this->items, $keys));
    }

    /**
     * Return a new bag merged with the given items (existing keys keep their position).
     *
     * @param  mixed  $items
     * @return static
     */
    public function merge($items)
    {
        return new static(array_merge($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Raw items, with the literal prefix left intact (for intermediate merges).
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Render-ready attributes: the literal prefix is stripped here (and only here).
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];

        foreach ($this->items as $key => $value) {
            if (is_string($key) && substr($key, 0, 1) === self::LITERAL_PREFIX) {
                $key = substr($key, 1);
            }

            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * Normalize any supported input into a plain items array (prefix left intact).
     *
     * @param  mixed  $items
     * @return array
     */
    protected function getArrayableItems($items)
    {
        if (is_array($items)) {
            return $items;
        }

        if ($items instanceof self) {
            return $items->all();
        }

        if ($items instanceof Collection) {
            return $items->all();
        }

        if ($items instanceof Arrayable) {
            return $items->toArray();
        }

        return (array) $items;
    }
}
