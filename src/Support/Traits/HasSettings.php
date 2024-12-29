<?php

namespace Bgaze\BootstrapForm\Support\Traits;

use Illuminate\Support\Collection;

trait HasSettings
{
    /**
     * The settings repository.
     */
    protected Collection $settings;

    /**
     * Bind any undefined property to the settings repository.
     */
    public function __get(string $key)
    {
        return $this->settings->get($key);
    }

    /**
     * Put any undefined property into the settings repository.
     */
    public function __set(string $key, $value)
    {
        $this->settings->put($key, $value);
    }

    /**
     * Flatten arrayed field names.
     */
    protected function flattenName(string $name, string $separator): string
    {
        return trim(str_replace(['[', ']'], [$separator, ''], $name), $separator);
    }
}
