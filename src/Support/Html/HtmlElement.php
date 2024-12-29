<?php

namespace Bgaze\BootstrapForm\Support\Html;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use JsonSerializable;
use Stringable;
use Traversable;

class HtmlElement implements Htmlable, Stringable, Renderable
{
    const SELF_CLOSING = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link',
        'meta', 'param', 'source', 'track', 'wbr'
    ];

    protected string $tag;
    protected bool $is_self_closing;

    protected Attributes $attributes;

    protected Collection $content;

    public function __construct(string $tag, array $attributes = [])
    {
        $this->tag = Str::lower($tag);

        $this->is_self_closing = in_array($this->tag, self::SELF_CLOSING);

        $this->attributes = new Attributes($attributes);

        $this->content = new Collection();
    }

    // RENDER

    public function open(): string
    {
        return sprintf(
            $this->is_self_closing ? '<%s/>' : '<%s>',
            trim(sprintf('%s %s', $this->tag, $this->attributes))
        );
    }

    public function content(): string
    {
        return $this->is_self_closing ? '' : $this->content->map(fn($v) => (string)$v)->join('');
    }

    public function close(): string
    {
        return $this->is_self_closing ? '' : sprintf('</%s>', $this->tag);
    }

    public function toHtml(): string
    {
        return $this->open() . $this->content() . $this->close();
    }

    public function render(): string
    {
        return $this->toHtml();
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    // ATTRIBUTES

    public function attributes(array $attributes): HtmlElement
    {
        $this->attributes = $this->attributes->merge($attributes);

        return $this;
    }

    public function attribute(string $key, $value): HtmlElement
    {
        $this->attributes->put($key, $value);

        return $this;
    }

    public function addClass(string|array $class): HtmlElement
    {
        $this->attributes->addClass($class);

        return $this;
    }

    // CONTENT

    public function append($content): HtmlElement
    {
        foreach (Arr::wrap($content) as $item) {
            $this->content->push($item);
        }

        return $this;
    }

    public function appendTo(HtmlElement $parent): HtmlElement
    {
        $parent->append($this);

        return $this;
    }

    public function prepend($content): HtmlElement
    {
        foreach (array_reverse(Arr::wrap($content)) as $item) {
            $this->content->prepend($item);
        }

        return $this;
    }

    public function prependTo(HtmlElement $parent): HtmlElement
    {
        $parent->prepend($this);

        return $this;
    }

}