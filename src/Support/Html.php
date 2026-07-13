<?php

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\HtmlString;

/**
 * Stateless HTML primitive: turns an attribute array into a string and builds tags.
 *
 * Single source of truth for attribute serialization — the exact ordering and escaping
 * rules here are what the rendered markup (and its characterization oracle) depend on.
 * Behavior ported verbatim from the former Collective HtmlBuilder (tag/attributes/link).
 */
class Html
{
    /**
     * The URL generator instance (used by link()).
     *
     * @var UrlGenerator|null
     */
    protected $url;

    public function __construct(UrlGenerator $url = null)
    {
        $this->url = $url;
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array  $attributes
     * @return string
     */
    public function attributes($attributes)
    {
        $html = [];

        foreach ((array) $attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            if (!is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return string|null
     */
    protected function attributeElement($key, $value)
    {
        // For numeric keys we assume a boolean attribute where the presence of the
        // attribute represents a true value (e.g. "required").
        if (is_numeric($key)) {
            return $value;
        }

        // Treat boolean attributes as HTML properties.
        if (is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }

        if (is_array($value) && $key === 'class') {
            return 'class="' . implode(' ', $value) . '"';
        }

        if (!is_null($value)) {
            return $key . '="' . e($value, false) . '"';
        }

        return null;
    }

    /**
     * Convert an HTML string to entities.
     *
     * @param  string  $value
     * @return string
     */
    public function entities($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Generate an HTML tag.
     *
     * @param  string  $tag
     * @param  mixed  $content
     * @param  array  $attributes
     * @return HtmlString
     */
    public function tag($tag, $content, array $attributes = [])
    {
        $content = is_array($content) ? implode('', $content) : $content;

        return $this->toHtmlString('<' . $tag . $this->attributes($attributes) . '>' . $content . '</' . $tag . '>');
    }

    /**
     * Generate an HTML link.
     *
     * @param  string  $url
     * @param  string  $title
     * @param  array  $attributes
     * @param  bool  $secure
     * @param  bool  $escape
     * @return HtmlString
     */
    public function link($url, $title = null, $attributes = [], $secure = null, $escape = true)
    {
        $url = $this->url->to($url, [], $secure);

        if (is_null($title) || $title === false) {
            $title = $url;
        }

        if ($escape) {
            $title = $this->entities($title);
        }

        return $this->toHtmlString('<a href="' . $this->entities($url) . '"' . $this->attributes($attributes) . '>' . $title . '</a>');
    }

    /**
     * Wrap a raw string into an HtmlString.
     *
     * @param  string  $html
     * @return HtmlString
     */
    protected function toHtmlString($html)
    {
        return new HtmlString($html);
    }
}
