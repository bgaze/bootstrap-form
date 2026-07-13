<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support;

use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

/**
 * Renders low-level form elements and the form open/close markup.
 *
 * Decomposed successor of the Collective FormBuilder: attribute serialization is
 * delegated to {@see Html}, value/checked/selected binding to {@see FieldValue}, and
 * per-form state to {@see FormContext}. The string assembly (notably the
 * array_merge($options, compact('type','value','id')) ordering) is preserved so the
 * rendered markup stays byte-for-byte identical.
 */
class FormElements
{
    /**
     * Reserved form-open attributes (consumed, not rendered as-is).
     */
    private const RESERVED = ['method', 'url', 'route', 'action', 'files'];

    /**
     * Form methods spoofed via a hidden _method field.
     */
    private const SPOOFED_METHODS = ['DELETE', 'PATCH', 'PUT'];

    /**
     * Input types whose value is never auto-filled.
     */
    private const SKIP_VALUE_TYPES = ['file', 'password', 'checkbox', 'radio'];

    public function __construct(
        protected readonly Html $html,
        protected readonly FieldValue $value,
        protected readonly FormContext $context,
    ) {
    }

    ### FORM OPEN / CLOSE ######################################################

    public function open(array $options = []): HtmlString
    {
        $method = Arr::get($options, 'method', 'post');

        $attributes = [
            'method' => $this->getMethod($method),
            'action' => $this->getAction($options),
            'accept-charset' => 'UTF-8',
        ];

        $append = $this->getAppendage($method);

        if (isset($options['files']) && $options['files']) {
            $options['enctype'] = 'multipart/form-data';
        }

        $attributes = array_merge($attributes, Arr::except($options, self::RESERVED));

        return $this->toHtmlString('<form' . $this->html->attributes($attributes) . '>' . $append);
    }

    public function model(mixed $model, array $options = []): HtmlString
    {
        $this->context->setModel($model);

        return $this->open($options);
    }

    public function close(): HtmlString
    {
        $this->context->reset();

        return $this->toHtmlString('</form>');
    }

    public function token(): HtmlString
    {
        $token = !empty($this->context->csrfToken()) ? $this->context->csrfToken() : $this->context->session()->token();

        return $this->hidden('_token', $token);
    }

    ### LABEL ##################################################################

    public function label(string|false|null $name, mixed $value = null, array $options = [], bool $escape_html = true): HtmlString
    {
        $options = $this->html->attributes($options);

        $value = $this->formatLabel((string) $name, $value);

        if ($escape_html) {
            $value = $this->html->entities($value);
        }

        // A disabled/empty target (input id === false) yields a label with no "for".
        $for = ($name === false || $name === null || $name === '') ? '' : ' for="' . $name . '"';

        return $this->toHtmlString('<label' . $for . $options . '>' . $value . '</label>');
    }

    protected function formatLabel(string $name, mixed $value): string
    {
        return $value ?: ucwords(str_replace('_', ' ', $name));
    }

    ### INPUTS #################################################################

    public function input(string $type, ?string $name, mixed $value = null, array $options = []): HtmlString
    {
        $this->value->setType($type);

        if (!isset($options['name'])) {
            $options['name'] = $name;
        }

        $id = $options['id'] ?? null;

        if (!in_array($type, self::SKIP_VALUE_TYPES)) {
            $value = $this->value->value($name, $value);
        }

        // type/value/id are appended after user options (id keeps its position when
        // already present): this fixes the rendered attribute order.
        $options = array_merge($options, compact('type', 'value', 'id'));

        return $this->toHtmlString('<input' . $this->html->attributes($options) . '>');
    }

    public function text(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return $this->input('text', $name, $value, $options);
    }

    public function password(string $name, array $options = []): HtmlString
    {
        return $this->input('password', $name, '', $options);
    }

    public function range(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return $this->input('range', $name, $value, $options);
    }

    public function hidden(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return $this->input('hidden', $name, $value, $options);
    }

    public function email(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return $this->input('email', $name, $value, $options);
    }

    public function tel(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return $this->input('tel', $name, $value, $options);
    }

    public function number(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return $this->input('number', $name, $value, $options);
    }

    public function date(string $name, mixed $value = null, array $options = []): HtmlString
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d');
        }

        return $this->input('date', $name, $value, $options);
    }

    public function time(string $name, mixed $value = null, array $options = []): HtmlString
    {
        if ($value instanceof DateTime) {
            $value = $value->format('H:i');
        }

        return $this->input('time', $name, $value, $options);
    }

    public function url(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return $this->input('url', $name, $value, $options);
    }

    public function color(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return $this->input('color', $name, $value, $options);
    }

    public function file(string $name, array $options = []): HtmlString
    {
        return $this->input('file', $name, null, $options);
    }

    public function textarea(string $name, mixed $value = null, array $options = []): HtmlString
    {
        $this->value->setType('textarea');

        if (!isset($options['name'])) {
            $options['name'] = $name;
        }

        $options = $this->setTextAreaSize($options);

        $options['id'] = $options['id'] ?? null;

        $value = (string) $this->value->value($name, $value);

        unset($options['size']);

        return $this->toHtmlString('<textarea' . $this->html->attributes($options) . '>' . e($value, false) . '</textarea>');
    }

    protected function setTextAreaSize(array $options): array
    {
        if (isset($options['size'])) {
            $segments = explode('x', $options['size']);

            return array_merge($options, ['cols' => $segments[0], 'rows' => $segments[1]]);
        }

        $cols = Arr::get($options, 'cols', 50);
        $rows = Arr::get($options, 'rows', 10);

        return array_merge($options, compact('cols', 'rows'));
    }

    ### SELECT #################################################################

    public function select(
        string $name,
        array $list = [],
        mixed $selected = null,
        array $selectAttributes = [],
        array $optionsAttributes = [],
        array $optgroupsAttributes = [],
    ): HtmlString {
        $this->value->setType('select');

        $selected = $this->value->value($name, $selected);

        $selectAttributes['id'] = $selectAttributes['id'] ?? null;

        if (!isset($selectAttributes['name'])) {
            $selectAttributes['name'] = $name;
        }

        $html = [];

        if (isset($selectAttributes['placeholder'])) {
            $html[] = $this->placeholderOption($selectAttributes['placeholder'], $selected);
            unset($selectAttributes['placeholder']);
        }

        foreach ($list as $value => $display) {
            $html[] = $this->getSelectOption(
                $display,
                $value,
                $selected,
                $optionsAttributes[$value] ?? [],
                $optgroupsAttributes[$value] ?? [],
            );
        }

        $selectAttributes = $this->html->attributes($selectAttributes);

        return $this->toHtmlString("<select{$selectAttributes}>" . implode('', $html) . '</select>');
    }

    public function getSelectOption(mixed $display, mixed $value, mixed $selected, array $attributes = [], array $optgroupAttributes = []): HtmlString
    {
        if (is_iterable($display)) {
            return $this->optionGroup($display, $value, $selected, $optgroupAttributes, $attributes);
        }

        return $this->option($display, $value, $selected, $attributes);
    }

    protected function optionGroup(iterable $list, mixed $label, mixed $selected, array $attributes = [], array $optionsAttributes = [], int $level = 0): HtmlString
    {
        $html = [];
        $space = str_repeat('&nbsp;', $level);

        foreach ($list as $value => $display) {
            $optionAttributes = $optionsAttributes[$value] ?? [];

            if (is_iterable($display)) {
                $html[] = $this->optionGroup($display, $value, $selected, $attributes, $optionAttributes, $level + 5);
            } else {
                $html[] = $this->option($space . $display, $value, $selected, $optionAttributes);
            }
        }

        return $this->toHtmlString('<optgroup label="' . e($space . $label, false) . '"' . $this->html->attributes($attributes) . '>' . implode('', $html) . '</optgroup>');
    }

    protected function option(mixed $display, mixed $value, mixed $selected, array $attributes = []): HtmlString
    {
        $selected = $this->value->selected($value, $selected);

        $options = array_merge(['value' => $value, 'selected' => $selected], $attributes);

        $string = '<option' . $this->html->attributes($options) . '>';

        if ($display !== null) {
            $string .= e($display, false) . '</option>';
        }

        return $this->toHtmlString($string);
    }

    protected function placeholderOption(mixed $display, mixed $selected): HtmlString
    {
        $options = [
            'selected' => $this->value->selected(null, $selected),
            'value' => '',
        ];

        return $this->toHtmlString('<option' . $this->html->attributes($options) . '>' . e($display, false) . '</option>');
    }

    ### CHECKABLES #############################################################

    public function checkbox(string $name, mixed $value = 1, mixed $checked = null, array $options = []): HtmlString
    {
        return $this->checkable('checkbox', $name, $value, $checked, $options);
    }

    public function radio(string $name, mixed $value = null, mixed $checked = null, array $options = []): HtmlString
    {
        return $this->checkable('radio', $name, $value ?? $name, $checked, $options);
    }

    protected function checkable(string $type, string $name, mixed $value, mixed $checked, array $options): HtmlString
    {
        if ($this->value->checked($type, $name, $value, $checked)) {
            $options['checked'] = 'checked';
        }

        return $this->input($type, $name, $value, $options);
    }

    ### BUTTONS ################################################################

    public function reset(mixed $value, array $attributes = []): HtmlString
    {
        return $this->input('reset', null, $value, $attributes);
    }

    public function submit(mixed $value = null, array $options = []): HtmlString
    {
        return $this->input('submit', null, $value, $options);
    }

    public function button(mixed $value = null, array $options = []): HtmlString
    {
        if (!array_key_exists('type', $options)) {
            $options['type'] = 'button';
        }

        return $this->toHtmlString('<button' . $this->html->attributes($options) . '>' . $value . '</button>');
    }

    ### HELPERS ################################################################

    protected function getMethod(string $method): string
    {
        $method = strtoupper($method);

        return $method !== 'GET' ? 'POST' : $method;
    }

    protected function getAction(array $options): string
    {
        return match (true) {
            isset($options['url']) => $this->getUrlAction($options['url']),
            isset($options['route']) => $this->getRouteAction($options['route']),
            isset($options['action']) => $this->getControllerAction($options['action']),
            default => $this->context->url()->current(),
        };
    }

    protected function getUrlAction(mixed $options): string
    {
        if (is_array($options)) {
            return $this->context->url()->to($options[0], array_slice($options, 1));
        }

        return $this->context->url()->to($options);
    }

    protected function getRouteAction(mixed $options): string
    {
        if (is_array($options)) {
            $parameters = array_slice($options, 1);

            if (array_keys($options) === [0, 1]) {
                $parameters = head($parameters);
            }

            return $this->context->url()->route($options[0], $parameters);
        }

        return $this->context->url()->route($options);
    }

    protected function getControllerAction(mixed $options): string
    {
        if (is_array($options)) {
            return $this->context->url()->action($options[0], array_slice($options, 1));
        }

        return $this->context->url()->action($options);
    }

    protected function getAppendage(string $method): string
    {
        $method = strtoupper($method);
        $appendage = '';

        if (in_array($method, self::SPOOFED_METHODS)) {
            $appendage .= $this->hidden('_method', $method);
        }

        if ($method !== 'GET') {
            $appendage .= $this->token();
        }

        return $appendage;
    }

    protected function toHtmlString(string $html): HtmlString
    {
        return new HtmlString($html);
    }
}
