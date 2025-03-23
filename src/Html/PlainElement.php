<?php

namespace Bgaze\BootstrapForm\Html;

use ArrayAccess;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PlainElement extends HtmlElement
{
    protected bool $opened = false;

    protected Collection $content;

    public function __construct(string $tag, ArrayAccess|array $attributes = [])
    {
        parent::__construct($tag, $attributes);

        $this->content = new Collection();
    }

    public function isOpened(): bool
    {
        return $this->opened;
    }

    public function open(): string
    {
        $this->opened = true;

        return sprintf('<%s>', trim(sprintf('%s %s', $this->tag, $this->attributes)));
    }

    public function content(): string
    {
        return $this->content->map(fn($v) => (string)$v)->join('');
    }

    public function close(): string
    {
        $this->opened = false;

        return sprintf('</%s>', $this->tag);
    }

    public function toHtml(): string
    {
        return $this->open() . $this->content() . $this->close();
    }

    public function append($content): PlainElement
    {
        foreach (Arr::wrap($content) as $item) {
            $this->content->push($item);
        }

        return $this;
    }

    public function prepend($content): PlainElement
    {
        foreach (array_reverse(Arr::wrap($content)) as $item) {
            $this->content->prepend($item);
        }

        return $this;
    }

}
