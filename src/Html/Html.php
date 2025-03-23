<?php

namespace Bgaze\BootstrapForm\Html;

use ArrayAccess;
use Illuminate\Support\Str;

/**
 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element
 *
 * @method static VoidElement base(ArrayAccess|array $attributes = [])
 * @method static VoidElement link(ArrayAccess|array $attributes = [])
 * @method static VoidElement meta(ArrayAccess|array $attributes = [])
 * @method static VoidElement hr(ArrayAccess|array $attributes = [])
 * @method static VoidElement br(ArrayAccess|array $attributes = [])
 * @method static VoidElement wbr(ArrayAccess|array $attributes = [])
 * @method static VoidElement area(ArrayAccess|array $attributes = [])
 * @method static VoidElement img(ArrayAccess|array $attributes = [])
 * @method static VoidElement track(ArrayAccess|array $attributes = [])
 * @method static VoidElement embed(ArrayAccess|array $attributes = [])
 * @method static VoidElement source(ArrayAccess|array $attributes = [])
 * @method static VoidElement col(ArrayAccess|array $attributes = [])
 * @method static VoidElement input(ArrayAccess|array $attributes = [])
 *
 * @method static PlainElement html(ArrayAccess|array $attributes = [])
 * @method static PlainElement abbr(ArrayAccess|array $attributes = [])
 * @method static PlainElement head(ArrayAccess|array $attributes = [])
 * @method static PlainElement style(ArrayAccess|array $attributes = [])
 * @method static PlainElement title(ArrayAccess|array $attributes = [])
 * @method static PlainElement body(ArrayAccess|array $attributes = [])
 * @method static PlainElement address(ArrayAccess|array $attributes = [])
 * @method static PlainElement article(ArrayAccess|array $attributes = [])
 * @method static PlainElement aside(ArrayAccess|array $attributes = [])
 * @method static PlainElement footer(ArrayAccess|array $attributes = [])
 * @method static PlainElement header(ArrayAccess|array $attributes = [])
 * @method static PlainElement h1(ArrayAccess|array $attributes = [])
 * @method static PlainElement h2(ArrayAccess|array $attributes = [])
 * @method static PlainElement h3(ArrayAccess|array $attributes = [])
 * @method static PlainElement h4(ArrayAccess|array $attributes = [])
 * @method static PlainElement h5(ArrayAccess|array $attributes = [])
 * @method static PlainElement h6(ArrayAccess|array $attributes = [])
 * @method static PlainElement hgroup(ArrayAccess|array $attributes = [])
 * @method static PlainElement main(ArrayAccess|array $attributes = [])
 * @method static PlainElement nav(ArrayAccess|array $attributes = [])
 * @method static PlainElement section(ArrayAccess|array $attributes = [])
 * @method static PlainElement search(ArrayAccess|array $attributes = [])
 * @method static PlainElement blockquote(ArrayAccess|array $attributes = [])
 * @method static PlainElement dd(ArrayAccess|array $attributes = [])
 * @method static PlainElement div(ArrayAccess|array $attributes = [])
 * @method static PlainElement dl(ArrayAccess|array $attributes = [])
 * @method static PlainElement dt(ArrayAccess|array $attributes = [])
 * @method static PlainElement figcaption(ArrayAccess|array $attributes = [])
 * @method static PlainElement figure(ArrayAccess|array $attributes = [])
 * @method static PlainElement li(ArrayAccess|array $attributes = [])
 * @method static PlainElement menu(ArrayAccess|array $attributes = [])
 * @method static PlainElement ol(ArrayAccess|array $attributes = [])
 * @method static PlainElement p(ArrayAccess|array $attributes = [])
 * @method static PlainElement pre(ArrayAccess|array $attributes = [])
 * @method static PlainElement ul(ArrayAccess|array $attributes = [])
 * @method static PlainElement a(ArrayAccess|array $attributes = [])
 * @method static PlainElement b(ArrayAccess|array $attributes = [])
 * @method static PlainElement bdi(ArrayAccess|array $attributes = [])
 * @method static PlainElement bdo(ArrayAccess|array $attributes = [])
 * @method static PlainElement cite(ArrayAccess|array $attributes = [])
 * @method static PlainElement code(ArrayAccess|array $attributes = [])
 * @method static PlainElement data(ArrayAccess|array $attributes = [])
 * @method static PlainElement dfn(ArrayAccess|array $attributes = [])
 * @method static PlainElement em(ArrayAccess|array $attributes = [])
 * @method static PlainElement i(ArrayAccess|array $attributes = [])
 * @method static PlainElement kbd(ArrayAccess|array $attributes = [])
 * @method static PlainElement mark(ArrayAccess|array $attributes = [])
 * @method static PlainElement q(ArrayAccess|array $attributes = [])
 * @method static PlainElement rp(ArrayAccess|array $attributes = [])
 * @method static PlainElement rt(ArrayAccess|array $attributes = [])
 * @method static PlainElement ruby(ArrayAccess|array $attributes = [])
 * @method static PlainElement s(ArrayAccess|array $attributes = [])
 * @method static PlainElement samp(ArrayAccess|array $attributes = [])
 * @method static PlainElement small(ArrayAccess|array $attributes = [])
 * @method static PlainElement span(ArrayAccess|array $attributes = [])
 * @method static PlainElement strong(ArrayAccess|array $attributes = [])
 * @method static PlainElement sub(ArrayAccess|array $attributes = [])
 * @method static PlainElement sup(ArrayAccess|array $attributes = [])
 * @method static PlainElement time(ArrayAccess|array $attributes = [])
 * @method static PlainElement u(ArrayAccess|array $attributes = [])
 * @method static PlainElement var(ArrayAccess|array $attributes = [])
 * @method static PlainElement audio(ArrayAccess|array $attributes = [])
 * @method static PlainElement map(ArrayAccess|array $attributes = [])
 * @method static PlainElement video(ArrayAccess|array $attributes = [])
 * @method static PlainElement fencedframe(ArrayAccess|array $attributes = [])
 * @method static PlainElement iframe(ArrayAccess|array $attributes = [])
 * @method static PlainElement object(ArrayAccess|array $attributes = [])
 * @method static PlainElement picture(ArrayAccess|array $attributes = [])
 * @method static PlainElement portal(ArrayAccess|array $attributes = [])
 * @method static PlainElement svg(ArrayAccess|array $attributes = [])
 * @method static PlainElement math(ArrayAccess|array $attributes = [])
 * @method static PlainElement canvas(ArrayAccess|array $attributes = [])
 * @method static PlainElement noscript(ArrayAccess|array $attributes = [])
 * @method static PlainElement script(ArrayAccess|array $attributes = [])
 * @method static PlainElement del(ArrayAccess|array $attributes = [])
 * @method static PlainElement ins(ArrayAccess|array $attributes = [])
 * @method static PlainElement caption(ArrayAccess|array $attributes = [])
 * @method static PlainElement colgroup(ArrayAccess|array $attributes = [])
 * @method static PlainElement table(ArrayAccess|array $attributes = [])
 * @method static PlainElement tbody(ArrayAccess|array $attributes = [])
 * @method static PlainElement td(ArrayAccess|array $attributes = [])
 * @method static PlainElement tfoot(ArrayAccess|array $attributes = [])
 * @method static PlainElement th(ArrayAccess|array $attributes = [])
 * @method static PlainElement thead(ArrayAccess|array $attributes = [])
 * @method static PlainElement tr(ArrayAccess|array $attributes = [])
 * @method static PlainElement button(ArrayAccess|array $attributes = [])
 * @method static PlainElement datalist(ArrayAccess|array $attributes = [])
 * @method static PlainElement fieldset(ArrayAccess|array $attributes = [])
 * @method static PlainElement form(ArrayAccess|array $attributes = [])
 * @method static PlainElement label(ArrayAccess|array $attributes = [])
 * @method static PlainElement legend(ArrayAccess|array $attributes = [])
 * @method static PlainElement meter(ArrayAccess|array $attributes = [])
 * @method static PlainElement optgroup(ArrayAccess|array $attributes = [])
 * @method static PlainElement option(ArrayAccess|array $attributes = [])
 * @method static PlainElement output(ArrayAccess|array $attributes = [])
 * @method static PlainElement progress(ArrayAccess|array $attributes = [])
 * @method static PlainElement select(ArrayAccess|array $attributes = [])
 * @method static PlainElement textarea(ArrayAccess|array $attributes = [])
 * @method static PlainElement details(ArrayAccess|array $attributes = [])
 * @method static PlainElement dialog(ArrayAccess|array $attributes = [])
 * @method static PlainElement summary(ArrayAccess|array $attributes = [])
 * @method static PlainElement slot(ArrayAccess|array $attributes = [])
 * @method static PlainElement template(ArrayAccess|array $attributes = [])
 */
class Html
{

    /** @see https://developer.mozilla.org/en-US/docs/Glossary/Void_element */
    const voidElements = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input',
        'link', 'meta', 'param', 'source', 'track', 'wbr'
    ];

    public static function make(string $tag, ArrayAccess|array $attributes = []): HtmlElement
    {
        return in_array(Str::kebab($tag), self::voidElements)
            ? new VoidElement($tag, $attributes)
            : new PlainElement($tag, $attributes);
    }

    public static function doctype(): string
    {
        return '<!DOCTYPE html>';
    }

    public static function __callStatic($method, $parameters): HtmlElement
    {
        return self::make($method, $parameters[0] ?? []);
    }

}
