<?php

namespace Bgaze\BootstrapForm\Support\Traits;

use Illuminate\Support\Collection;

trait HasSettings {

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
    public function __get($key) {
        return $this->settings->get($key);
    }

    /**
     * Put any undefined property into the settings repository.
     * 
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value) {
        $this->settings->put($key, $value);
    }

}
