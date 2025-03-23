<?php

namespace Bgaze\BootstrapForm\Html;

use ArrayAccess;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Stringable;

abstract class HtmlElement implements Htmlable, Renderable, Stringable
{
    protected string $tag;

    protected Attributes $attributes;

    public function __construct(string $tag, ArrayAccess|array $attributes = [])
    {
        $this->tag = Str::kebab($tag);
        $this->attributes = ($attributes instanceof Attributes) ? $attributes : new Attributes($attributes);
    }

    abstract public function toHtml(): string;

    public function render(): string
    {
        return $this->toHtml();
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    public function attributes(array|Attributes $attributes): HtmlElement
    {
        $this->attributes->merge($attributes);

        return $this;
    }

    public function attribute(string $key, $value): HtmlElement
    {
        $this->attributes->set($key, $value);

        return $this;
    }

    public function addClass(string|array $class): HtmlElement
    {
        $this->attributes->addClass($class);

        return $this;
    }

    public function appendTo(PlainElement $parent): HtmlElement
    {
        $parent->append($this);

        return $this;
    }

    public function prependTo(PlainElement $parent): HtmlElement
    {
        $parent->prepend($this);

        return $this;
    }
}
