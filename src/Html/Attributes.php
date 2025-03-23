<?php

namespace Bgaze\BootstrapForm\Html;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Stringable;

class Attributes implements ArrayAccess, Arrayable, Renderable, Stringable
{

    /** @see https://html.spec.whatwg.org/multipage/indices.html#attributes-3 */
    const booleanAttributes = [
        'allowfullscreen', 'alpha', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer',
        'disabled', 'formnovalidate', 'inert', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule',
        'novalidate', 'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected', 'shadowrootclonable',
        'shadowrootdelegatesfocus', 'shadowrootserializable',
    ];


    protected array $attributes = [];

    protected array $classes = [];

    public function __construct(array|ArrayAccess $attributes = [])
    {
        $this->merge($attributes);
    }

    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public static function make(array|ArrayAccess $attributes = []): static
    {
        return new static($attributes);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $offset = Str::kebab($offset);

        if ($offset === 'class') {
            return implode(' ', $this->classes);
        }

        return $this->attributes[Str::kebab($offset)] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $offset = Str::kebab($offset);

        if ($value === false) {
            $this->offsetUnset($offset);
        } elseif ($offset === 'class') {
            $this->classes = self::normalizeClass($value);
        } elseif ($value === true || $value === null) {
            $this->attributes[$offset] = '';
        } else {
            $this->attributes[$offset] = trim((string)$value);
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        $offset = Str::kebab($offset);

        if ($offset === 'class') {
            return !empty($this->classes);
        }

        return isset($this->attributes[Str::kebab($offset)]);
    }

    public function offsetUnset(mixed $offset): void
    {
        $offset = Str::kebab($offset);

        if ($offset === 'class') {
            $this->classes = [];
        } elseif (isset($this->attributes[$offset])) {
            unset($this->attributes[$offset]);
        }
    }

    public function merge(array|ArrayAccess $attributes): static
    {
        foreach ($attributes as $key => $value) {
            if (is_int($key)) {
                $this->offsetSet($value, '');
            } else {
                $this->offsetSet($key, $value);
            }
        }

        return $this;
    }

    public function get(string $key)
    {
        return $this->offsetGet($key);
    }

    public function set(string $key, $value): static
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    public function has(string $key): bool
    {
        return $this->offsetExists($key);
    }

    public function toArray(): array
    {
        $classes = !empty($this->classes) ? ['class' => implode(' ', $this->classes)] : [];

        return $classes + $this->attributes;
    }

    public function render(): string
    {
        return collect($this->toArray())
            ->map(function ($value, $key) {
                if (empty($value) && in_array($key, self::booleanAttributes)) {
                    return $key;
                }

                if (str_contains($value, '"')) {
                    return sprintf("%s='%s'", $key, $value);
                }

                return sprintf('%s="%s"', $key, $value);
            })
            ->join(' ');
    }

    public function addClass(...$classes): static
    {
        $this->classes = self::normalizeClass($this->classes, ...$classes);

        return $this;
    }

    public static function normalizeClass(...$inputs): array
    {
        $normalized = collect();

        foreach ($inputs as $input) {
            if (!is_array($input)) {
                $input = preg_split('/\s+/', (string)$input);
            }

            foreach ($input as $k => $v) {
                if (is_int($k)) {
                    $key = trim($v);
                    $value = true;
                } else {
                    $key = trim($k);
                    $value = !!$v;
                }

                if (!empty($key)) {
                    $normalized->put($key, $value);
                }
            }
        }

        return $normalized->filter()->keys()->all();
    }

}
