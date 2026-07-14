<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support;

use InvalidArgumentException;

/**
 * Single source of truth for the rich `choices` grammar shared by select and checkable
 * collection fields.
 *
 * A choice entry is either the simple `value => label` form, or an "advanced" array form
 * that carries per-child HTML attributes inline:
 *
 *  - option:   ['value' => ..., 'label' => ..., ...attributes]   (key ignored)
 *  - optgroup: ['label' => ..., 'options' => [...], ...attributes] (root-only, key ignored)
 *
 * The `options` key discriminates an optgroup (it is not a valid `<option>` attribute).
 * Parsing is strict: any ambiguous or incomplete descriptor throws.
 *
 * The parser normalizes the grammar back onto the flat shape the low-level renderer already
 * consumes ({@see FormElements::select()} `$list` / `$optionsAttributes` / `$optgroupsAttributes`),
 * so no markup logic is duplicated here — only structure + validation + attribute merging.
 */
class ChoiceList
{
    /**
     * Normalize rich select choices into the ($list, $optionsAttributes, $optgroupsAttributes)
     * triple consumed by {@see FormElements::select()}.
     *
     * The "all options" / "all optgroups" attribute bags are merged under each per-item entry
     * (the item wins).
     *
     * @param  array<array-key, mixed>  $choices
     * @param  array<string, mixed>  $optionAttributes   applied to every <option>
     * @param  array<string, mixed>  $optgroupAttributes  applied to every <optgroup>
     * @return array{0: array<array-key, mixed>, 1: array<array-key, mixed>, 2: array<array-key, mixed>}
     */
    public static function select(array $choices, array $optionAttributes = [], array $optgroupAttributes = []): array
    {
        $list = [];
        $options = [];
        $optgroups = [];

        foreach ($choices as $key => $item) {
            // Advanced optgroup: an array carrying an `options` key (root-only).
            if (is_array($item) && array_key_exists('options', $item)) {
                [$label, $groupAttributes, $inner] = self::parseAdvancedOptgroup($item);
                [$list[$label], $groupOptions] = self::parseGroupOptions($inner, $optionAttributes);
                self::put($options, $label, $groupOptions);
                self::put($optgroups, $label, array_merge($optgroupAttributes, $groupAttributes));

                continue;
            }

            // Advanced option: an array carrying value/label (key ignored).
            if (is_array($item) && (array_key_exists('value', $item) || array_key_exists('label', $item))) {
                [$value, $label, $attributes] = self::parseAdvancedOption($item);
                $list[$value] = $label;
                self::put($options, $value, array_merge($optionAttributes, $attributes));

                continue;
            }

            // Simple optgroup: a string-keyed array of options.
            if (is_array($item)) {
                if (! is_string($key)) {
                    throw new InvalidArgumentException('Ambiguous choice entry: a bare array must be an advanced descriptor (with value/label or options), or a string-keyed optgroup.');
                }

                [$list[$key], $groupOptions] = self::parseGroupOptions($item, $optionAttributes);
                self::put($options, $key, $groupOptions);
                self::put($optgroups, $key, $optgroupAttributes);

                continue;
            }

            // Simple option.
            $list[$key] = $item;
            self::put($options, $key, $optionAttributes);
        }

        return [$list, $options, $optgroups];
    }

    /**
     * Normalize rich checkable choices into an ordered list of children. Optgroups and
     * nesting are unsupported (checkables have no such structure) and throw.
     *
     * @param  array<array-key, mixed>  $choices
     * @param  array<string, mixed>  $optionAttributes  applied to every child
     * @return array<int, array{value: mixed, label: mixed, attributes: array<string, mixed>}>
     */
    public static function checkables(array $choices, array $optionAttributes = []): array
    {
        $result = [];

        foreach ($choices as $key => $item) {
            if (is_array($item)) {
                if (array_key_exists('options', $item)) {
                    throw new InvalidArgumentException('Checkable choices do not support optgroups.');
                }

                [$value, $label, $attributes] = self::parseAdvancedOption($item);
                $result[] = ['value' => $value, 'label' => $label, 'attributes' => array_merge($optionAttributes, $attributes)];

                continue;
            }

            $result[] = ['value' => $key, 'label' => $item, 'attributes' => $optionAttributes];
        }

        return $result;
    }

    /**
     * Parse the options nested inside an optgroup (options only — no nesting).
     *
     * @param  array<array-key, mixed>  $items
     * @param  array<string, mixed>  $optionAttributes
     * @return array{0: array<array-key, mixed>, 1: array<array-key, mixed>}
     */
    protected static function parseGroupOptions(array $items, array $optionAttributes): array
    {
        $list = [];
        $options = [];

        foreach ($items as $key => $item) {
            if (is_array($item)) {
                if (array_key_exists('options', $item)) {
                    throw new InvalidArgumentException('Nested optgroups are not supported: optgroups must be at the root of the choices.');
                }

                [$value, $label, $attributes] = self::parseAdvancedOption($item);
                $list[$value] = $label;
                self::put($options, $value, array_merge($optionAttributes, $attributes));

                continue;
            }

            $list[$key] = $item;
            self::put($options, $key, $optionAttributes);
        }

        return [$list, $options];
    }

    /**
     * Split an advanced option descriptor into [value, label, attributes]. `value` and `label`
     * are mandatory; the remaining keys are HTML attributes.
     *
     * @param  array<string, mixed>  $item
     * @return array{0: mixed, 1: mixed, 2: array<string, mixed>}
     */
    protected static function parseAdvancedOption(array $item): array
    {
        if (! array_key_exists('value', $item) || ! array_key_exists('label', $item)) {
            throw new InvalidArgumentException("An advanced choice provided as an array must define both 'value' and 'label'.");
        }

        $value = $item['value'];
        $label = $item['label'];
        unset($item['value'], $item['label']);

        return [$value, $label, $item];
    }

    /**
     * Split an advanced optgroup descriptor into [label, attributes, options]. `label` is
     * mandatory and `options` must be an array; the remaining keys are HTML attributes.
     *
     * @param  array<string, mixed>  $item
     * @return array{0: mixed, 1: array<string, mixed>, 2: array<array-key, mixed>}
     */
    protected static function parseAdvancedOptgroup(array $item): array
    {
        if (! array_key_exists('label', $item)) {
            throw new InvalidArgumentException("An advanced optgroup must define a 'label'.");
        }

        if (! is_array($item['options'])) {
            throw new InvalidArgumentException("An advanced optgroup 'options' must be an array.");
        }

        $label = $item['label'];
        $options = $item['options'];
        unset($item['label'], $item['options']);

        return [$label, $item, $options];
    }

    /**
     * Store an attribute bag under a key only when it is non-empty, keeping the normalized
     * attribute maps free of empty entries.
     *
     * @param  array<array-key, mixed>  $bag
     * @param  array<array-key, mixed>  $attributes
     */
    protected static function put(array &$bag, mixed $key, array $attributes): void
    {
        if ($attributes !== []) {
            $bag[$key] = $attributes;
        }
    }
}
