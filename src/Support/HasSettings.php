<?php

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Support\Collection;

trait HasSettings
{
    protected Collection $settings;

    public function __get(string $key)
    {
        return $this->settings->get($key);
    }

    public function __set(string $key, $value)
    {
        $this->settings->put($key, $value);
    }

    protected function flattenName(string $name, string $separator): string
    {
        return trim(str_replace(['[', ']'], [$separator, ''], $name), $separator);
    }

}
