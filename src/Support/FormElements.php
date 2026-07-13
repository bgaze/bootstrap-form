<?php

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
 * array_merge($options, compact('type','value','id')) ordering) is ported verbatim so
 * the rendered markup stays byte-for-byte identical.
 */
class FormElements
{
    /**
     * @var Html
     */
    protected $html;

    /**
     * @var FieldValue
     */
    protected $value;

    /**
     * @var FormContext
     */
    protected $context;

    /**
     * Reserved form-open attributes (consumed, not rendered as-is).
     *
     * @var array
     */
    protected $reserved = ['method', 'url', 'route', 'action', 'files'];

    /**
     * Form methods spoofed via a hidden _method field.
     *
     * @var array
     */
    protected $spoofedMethods = ['DELETE', 'PATCH', 'PUT'];

    /**
     * Input types whose value is never auto-filled.
     *
     * @var array
     */
    protected $skipValueTypes = ['file', 'password', 'checkbox', 'radio'];

    public function __construct(Html $html, FieldValue $value, FormContext $context)
    {
        $this->html = $html;
        $this->value = $value;
        $this->context = $context;
    }

    ### FORM OPEN / CLOSE ######################################################

    /**
     * Open up a new HTML form.
     *
     * @param  array  $options
     * @return HtmlString
     */
    public function open(array $options = [])
    {
        $method = Arr::get($options, 'method', 'post');

        $attributes['method'] = $this->getMethod($method);
        $attributes['action'] = $this->getAction($options);
        $attributes['accept-charset'] = 'UTF-8';

        $append = $this->getAppendage($method);

        if (isset($options['files']) && $options['files']) {
            $options['enctype'] = 'multipart/form-data';
        }

        $attributes = array_merge($attributes, Arr::except($options, $this->reserved));

        $attributes = $this->html->attributes($attributes);

        return $this->toHtmlString('<form' . $attributes . '>' . $append);
    }

    /**
     * Open a model-bound form.
     *
     * @param  mixed  $model
     * @param  array  $options
     * @return HtmlString
     */
    public function model($model, array $options = [])
    {
        $this->context->setModel($model);

        return $this->open($options);
    }

    /**
     * Close the current form.
     *
     * @return HtmlString
     */
    public function close()
    {
        $this->context->reset();

        return $this->toHtmlString('</form>');
    }

    /**
     * Generate a hidden field with the current CSRF token.
     *
     * @return HtmlString
     */
    public function token()
    {
        $token = !empty($this->context->csrfToken()) ? $this->context->csrfToken() : $this->context->session()->token();

        return $this->hidden('_token', $token);
    }

    ### LABEL ##################################################################

    /**
     * Create a form label element.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array  $options
     * @param  bool  $escape_html
     * @return HtmlString
     */
    public function label($name, $value = null, $options = [], $escape_html = true)
    {
        $options = $this->html->attributes($options);

        $value = $this->formatLabel($name, $value);

        if ($escape_html) {
            $value = $this->html->entities($value);
        }

        return $this->toHtmlString('<label for="' . $name . '"' . $options . '>' . $value . '</label>');
    }

    /**
     * Format the label value.
     */
    protected function formatLabel($name, $value)
    {
        return $value ?: ucwords(str_replace('_', ' ', $name));
    }

    ### INPUTS #################################################################

    /**
     * Create a form input field.
     *
     * @param  string  $type
     * @param  string  $name
     * @param  string  $value
     * @param  array  $options
     * @return HtmlString
     */
    public function input($type, $name, $value = null, $options = [])
    {
        $this->value->setType($type);

        if (!isset($options['name'])) {
            $options['name'] = $name;
        }

        $id = $this->getIdAttribute($name, $options);

        if (!in_array($type, $this->skipValueTypes)) {
            $value = $this->value->value($name, $value);
        }

        $merge = compact('type', 'value', 'id');

        $options = array_merge($options, $merge);

        return $this->toHtmlString('<input' . $this->html->attributes($options) . '>');
    }

    public function text($name, $value = null, $options = [])
    {
        return $this->input('text', $name, $value, $options);
    }

    public function password($name, $options = [])
    {
        return $this->input('password', $name, '', $options);
    }

    public function range($name, $value = null, $options = [])
    {
        return $this->input('range', $name, $value, $options);
    }

    public function hidden($name, $value = null, $options = [])
    {
        return $this->input('hidden', $name, $value, $options);
    }

    public function email($name, $value = null, $options = [])
    {
        return $this->input('email', $name, $value, $options);
    }

    public function tel($name, $value = null, $options = [])
    {
        return $this->input('tel', $name, $value, $options);
    }

    public function number($name, $value = null, $options = [])
    {
        return $this->input('number', $name, $value, $options);
    }

    public function date($name, $value = null, $options = [])
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d');
        }

        return $this->input('date', $name, $value, $options);
    }

    public function time($name, $value = null, $options = [])
    {
        if ($value instanceof DateTime) {
            $value = $value->format('H:i');
        }

        return $this->input('time', $name, $value, $options);
    }

    public function url($name, $value = null, $options = [])
    {
        return $this->input('url', $name, $value, $options);
    }

    public function color($name, $value = null, $options = [])
    {
        return $this->input('color', $name, $value, $options);
    }

    public function file($name, $options = [])
    {
        return $this->input('file', $name, null, $options);
    }

    /**
     * Create a textarea input field.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array  $options
     * @return HtmlString
     */
    public function textarea($name, $value = null, $options = [])
    {
        $this->value->setType('textarea');

        if (!isset($options['name'])) {
            $options['name'] = $name;
        }

        $options = $this->setTextAreaSize($options);

        $options['id'] = $this->getIdAttribute($name, $options);

        $value = (string) $this->value->value($name, $value);

        unset($options['size']);

        $options = $this->html->attributes($options);

        return $this->toHtmlString('<textarea' . $options . '>' . e($value, false) . '</textarea>');
    }

    /**
     * Set the textarea size on the attributes.
     */
    protected function setTextAreaSize($options)
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

    /**
     * Create a select box field.
     *
     * @param  string  $name
     * @param  array  $list
     * @param  string|bool  $selected
     * @param  array  $selectAttributes
     * @param  array  $optionsAttributes
     * @param  array  $optgroupsAttributes
     * @return HtmlString
     */
    public function select(
        $name,
        $list = [],
        $selected = null,
        array $selectAttributes = [],
        array $optionsAttributes = [],
        array $optgroupsAttributes = []
    ) {
        $this->value->setType('select');

        $selected = $this->value->value($name, $selected);

        $selectAttributes['id'] = $this->getIdAttribute($name, $selectAttributes);

        if (!isset($selectAttributes['name'])) {
            $selectAttributes['name'] = $name;
        }

        $html = [];

        if (isset($selectAttributes['placeholder'])) {
            $html[] = $this->placeholderOption($selectAttributes['placeholder'], $selected);
            unset($selectAttributes['placeholder']);
        }

        foreach ($list as $value => $display) {
            $optionAttributes = $optionsAttributes[$value] ?? [];
            $optgroupAttributes = $optgroupsAttributes[$value] ?? [];
            $html[] = $this->getSelectOption($display, $value, $selected, $optionAttributes, $optgroupAttributes);
        }

        $selectAttributes = $this->html->attributes($selectAttributes);

        $list = implode('', $html);

        return $this->toHtmlString("<select{$selectAttributes}>{$list}</select>");
    }

    /**
     * Get the select option for the given value.
     */
    public function getSelectOption($display, $value, $selected, array $attributes = [], array $optgroupAttributes = [])
    {
        if (is_iterable($display)) {
            return $this->optionGroup($display, $value, $selected, $optgroupAttributes, $attributes);
        }

        return $this->option($display, $value, $selected, $attributes);
    }

    /**
     * Create an option group element.
     */
    protected function optionGroup($list, $label, $selected, array $attributes = [], array $optionsAttributes = [], $level = 0)
    {
        $html = [];
        $space = str_repeat("&nbsp;", $level);

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

    /**
     * Create a select element option.
     */
    protected function option($display, $value, $selected, array $attributes = [])
    {
        $selected = $this->value->selected($value, $selected);

        $options = array_merge(['value' => $value, 'selected' => $selected], $attributes);

        $string = '<option' . $this->html->attributes($options) . '>';
        if ($display !== null) {
            $string .= e($display, false) . '</option>';
        }

        return $this->toHtmlString($string);
    }

    /**
     * Create a placeholder select option.
     */
    protected function placeholderOption($display, $selected)
    {
        $selected = $this->value->selected(null, $selected);

        $options = [
            'selected' => $selected,
            'value' => '',
        ];

        return $this->toHtmlString('<option' . $this->html->attributes($options) . '>' . e($display, false) . '</option>');
    }

    ### CHECKABLES #############################################################

    /**
     * Create a checkbox input field.
     */
    public function checkbox($name, $value = 1, $checked = null, $options = [])
    {
        return $this->checkable('checkbox', $name, $value, $checked, $options);
    }

    /**
     * Create a radio button input field.
     */
    public function radio($name, $value = null, $checked = null, $options = [])
    {
        if (is_null($value)) {
            $value = $name;
        }

        return $this->checkable('radio', $name, $value, $checked, $options);
    }

    /**
     * Create a checkable input field.
     */
    protected function checkable($type, $name, $value, $checked, $options)
    {
        $checked = $this->value->checked($type, $name, $value, $checked);

        if ($checked) {
            $options['checked'] = 'checked';
        }

        return $this->input($type, $name, $value, $options);
    }

    ### BUTTONS ################################################################

    public function reset($value, $attributes = [])
    {
        return $this->input('reset', null, $value, $attributes);
    }

    public function submit($value = null, $options = [])
    {
        return $this->input('submit', null, $value, $options);
    }

    public function button($value = null, $options = [])
    {
        if (!array_key_exists('type', $options)) {
            $options['type'] = 'button';
        }

        return $this->toHtmlString('<button' . $this->html->attributes($options) . '>' . $value . '</button>');
    }

    ### HELPERS ################################################################

    /**
     * Get the ID attribute for a field name (inputs always carry an explicit id here).
     */
    protected function getIdAttribute($name, $attributes)
    {
        return $attributes['id'] ?? null;
    }

    protected function getMethod($method)
    {
        $method = strtoupper($method);

        return $method !== 'GET' ? 'POST' : $method;
    }

    protected function getAction(array $options)
    {
        if (isset($options['url'])) {
            return $this->getUrlAction($options['url']);
        }

        if (isset($options['route'])) {
            return $this->getRouteAction($options['route']);
        } elseif (isset($options['action'])) {
            return $this->getControllerAction($options['action']);
        }

        return $this->context->url()->current();
    }

    protected function getUrlAction($options)
    {
        if (is_array($options)) {
            return $this->context->url()->to($options[0], array_slice($options, 1));
        }

        return $this->context->url()->to($options);
    }

    protected function getRouteAction($options)
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

    protected function getControllerAction($options)
    {
        if (is_array($options)) {
            return $this->context->url()->action($options[0], array_slice($options, 1));
        }

        return $this->context->url()->action($options);
    }

    protected function getAppendage($method)
    {
        list($method, $appendage) = [strtoupper($method), ''];

        if (in_array($method, $this->spoofedMethods)) {
            $appendage .= $this->hidden('_method', $method);
        }

        if ($method !== 'GET') {
            $appendage .= $this->token();
        }

        return $appendage;
    }

    protected function toHtmlString($html)
    {
        return new HtmlString($html);
    }
}
