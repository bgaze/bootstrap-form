<?php

namespace Bgaze\BootstrapForm\Support\Html;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Customize Collection class to ease HTML attributes set manipulation.
 *
 * @property ?string $class
 */
class Attributes extends Collection
{
    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->put($key, $value);
    }

    public function addClass(string|array $class): static
    {
        $classes = $this->class ?? '';

        foreach (Arr::wrap($class) as $k => $v) {
            if (is_int($k)) {
                $classes .= ' ' . $v;
            } elseif ($v) {
                $classes .= ' ' . $k;
            }
        }

        $this->class = $classes;

        return $this;
    }

    public function toArray(): array
    {
        $array = [];

        foreach (parent::toArray() as $key => $value) {
            if (str_starts_with($key, '~')) {
                $key = substr($key, 1);
            }
            $array[$key] = $value;
        }

        return $array;
    }

    public function render(): string
    {
        return collect($this->toArray())
            ->map(function ($value, $key) {
                if ($value === false) {
                    return null;
                }

                if ($key === 'class') {
                    $value = collect(preg_split('/\s+/', $value))->filter()->unique()->join(' ');
                }

                if (empty($value) || $value === true) {
                    return in_array($key, ['class', 'action', 'value']) ? null : $key;
                }

                if (str_contains($value, '"')) {
                    return sprintf("%s='%s'", $key, $value);
                }

                return sprintf('%s="%s"', $key, $value);
            })
            ->filter()
            ->join(' ');
    }

    public function __toString(): string
    {
        return $this->render();
    }

}
