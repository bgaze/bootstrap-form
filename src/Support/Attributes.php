<?php

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Support\Collection;

/**
 * Customize Collection class to ease HTML attributes set manipulation.
 */
class Attributes extends Collection
{

    /**
     * Allow direct access to attributes.
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Allow to directly set attributes.
     * 
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->put($key, $value);
    }

    /**
     * Append HTML class to a set of HTML attribute.
     * 
     * @param string $class
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
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];

        foreach (parent::toArray() as $key => $value) {
            if (substr($key, 0, 1) === '~') {
                $key = substr($key, 1);
            }
            $array[$key] = $value;
        }

        return $array;
    }
}
