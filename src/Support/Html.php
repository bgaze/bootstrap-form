<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\HtmlString;

/**
 * Stateless HTML primitive: turns an attribute array into a string and builds tags.
 *
 * Single source of truth for attribute serialization — the exact ordering and escaping
 * rules here are what the rendered markup (and its characterization oracle) depend on.
 */
class Html
{
    public function __construct(protected readonly UrlGenerator $url) {}

    /**
     * Build an HTML attribute string from an array (leading space included when non-empty).
     */
    public function attributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            // Skip nulls (absent) and empty strings (e.g. a disabled boolean attribute),
            // so a dropped attribute never leaves a stray double space in the output.
            if (! is_null($element) && $element !== '') {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    protected function attributeElement(int|string $key, mixed $value): ?string
    {
        // Numeric keys are bare boolean attributes (e.g. "required").
        if (is_numeric($key)) {
            return (string) $value;
        }

        // Boolean values are HTML properties (present when true), except "value".
        if (is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }

        if (is_array($value) && $key === 'class') {
            return 'class="'.implode(' ', $value).'"';
        }

        if (! is_null($value)) {
            return $key.'="'.e($value, false).'"';
        }

        return null;
    }

    public function entities(string $value): string
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    public function tag(string $tag, mixed $content, array $attributes = []): HtmlString
    {
        $content = is_array($content) ? implode('', $content) : $content;

        return $this->toHtmlString('<'.$tag.$this->attributes($attributes).'>'.$content.'</'.$tag.'>');
    }

    public function link(string $url, string|false|null $title = null, array $attributes = [], ?bool $secure = null, bool $escape = true): HtmlString
    {
        $url = $this->url->to($url, [], $secure);

        if (is_null($title) || $title === false) {
            $title = $url;
        }

        if ($escape) {
            $title = $this->entities($title);
        }

        return $this->toHtmlString('<a href="'.$this->entities($url).'"'.$this->attributes($attributes).'>'.$title.'</a>');
    }

    protected function toHtmlString(string $html): HtmlString
    {
        return new HtmlString($html);
    }
}
