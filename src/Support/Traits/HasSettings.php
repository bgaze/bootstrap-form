<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support\Traits;

use Illuminate\Support\Collection;

trait HasSettings
{
    protected Collection $settings;

    /**
     * Bind any undefined property to the settings repository.
     */
    public function __get(string $key): mixed
    {
        return $this->settings->get($key);
    }

    /**
     * Put any undefined property into the settings repository.
     */
    public function __set(string $key, mixed $value): void
    {
        $this->settings->put($key, $value);
    }

    /**
     * Flatten arrayed field names for the validator: drop "[]" and turn "foo[bar][baz]"
     * into "foo.bar.baz" using the given separator.
     */
    protected function flattenName(string $name, string $separator): string
    {
        return preg_replace_callback("/\[(.*)\\]/U", function ($matches) use ($separator) {
            if (!empty($matches[1]) || $matches[1] === '0') {
                return $separator . $matches[1];
            }
        }, $name);
    }
}
