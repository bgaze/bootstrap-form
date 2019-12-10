<?php

namespace Bgaze\BootstrapForm\Support\Traits;

use Illuminate\Support\Collection;

trait HasSettings
{

    /**
     * The settings repository.
     * 
     * @var Collection 
     */
    protected $settings;

    /**
     * Bind any undefined property to the settings repository.
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->settings->get($key);
    }

    /**
     * Put any undefined property into the settings repository.
     * 
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        $this->settings->put($key, $value);
    }

    /**
     * Flatten arrayed field names to work with the validator, including removing "[]",
     * and converting nested arrays like "foo[bar][baz]" to "foo.bar.baz".
     *
     * @return string
     */
    protected function flattenName($name, $separator)
    {
        return preg_replace_callback("/\[(.*)\\]/U", function ($matches) use ($separator) {
            if (!empty($matches[1]) || $matches[1] === '0') {
                return $separator . $matches[1];
            }
        }, $name);
    }
}
