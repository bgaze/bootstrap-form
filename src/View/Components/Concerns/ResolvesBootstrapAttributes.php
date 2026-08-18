<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components\Concerns;

use Bgaze\BootstrapForm\Support\Attributes;
use Bgaze\BootstrapForm\Support\Facades\BF;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Translates a component attribute bag into a BF options array — the ergonomic core of the
 * x-components.
 *
 * Projection rules:
 *  - `label:*`  -> options['label'] (label element attributes, kept verbatim)
 *  - `group:*`  -> options['group'] (group element attributes, kept verbatim)
 *  - `input:*`  -> literal input HTML attribute (prefixed with {@see Attributes::LITERAL_PREFIX}
 *                  so it escapes the settings partition — the x-component equivalent of `~`)
 *  - `option:*` / `optgroup:*` -> options['option_attributes'] / options['optgroup_attributes']
 *                  (child-attribute bags), but ONLY for components that declare the prefix via
 *                  {@see childAttributeGroups()} — so the array bag never leaks as a rendered
 *                  attribute on a component that does not support it.
 *  - `group`    -> false disables the wrapper; an array provides its attributes
 *  - a boolean setting passed as the STRING 'true' / 'false' -> the actual boolean, so the
 *                  Blade idioms `escape="false"` and `:escape="false"` agree
 *                  ({@see booleanSettingKeys()})
 *  - anything else -> input attribute / setting, its key normalized to the snake_case BF
 *                     setting name when it maps to one (so kebab-case is idiomatic), otherwise
 *                     kept verbatim (HTML attributes such as data-*, aria-* stay untouched).
 */
trait ResolvesBootstrapAttributes
{
    /**
     * @return array<string, mixed>
     */
    protected function bootstrapOptions(): array
    {
        $options = [];
        $label = [];
        $group = [];
        $childGroups = [];
        $groupDisabled = false;

        foreach ($this->attributes->getAttributes() as $key => $value) {
            if (str_starts_with($key, 'label:')) {
                $label[substr($key, 6)] = $value;
            } elseif (str_starts_with($key, 'group:')) {
                $group[substr($key, 6)] = $value;
            } elseif (str_starts_with($key, 'input:')) {
                $options[Attributes::LITERAL_PREFIX.substr($key, 6)] = $value;
            } elseif (($childGroup = $this->matchChildAttributeGroup($key)) !== null) {
                $childGroups[$childGroup[0]][$childGroup[1]] = $value;
            } elseif ($key === 'group') {
                if ($value === false || $value === 'false') {
                    $groupDisabled = true;
                } elseif (is_array($value)) {
                    $group = array_merge($group, $value);
                }
            } else {
                $setting = $this->normalizeSettingKey($key);
                $options[$setting] = $this->normalizeBooleanValue($setting, $value);
            }
        }

        foreach ($childGroups as $target => $attributes) {
            $options[$target] = $attributes;
        }

        if ($label !== []) {
            $options['label'] = $label;
        }

        if ($groupDisabled) {
            $options['group'] = false;
        } elseif ($group !== []) {
            $options['group'] = $group;
        }

        return $options;
    }

    /**
     * Normalize a kebab/camel attribute key to its snake_case BF setting name when it maps
     * to a known setting; otherwise keep it verbatim (HTML attributes stay untouched).
     */
    protected function normalizeSettingKey(string $key): string
    {
        $canonical = Str::snake(Str::camel($key));

        return in_array($canonical, $this->settingKeys(), true) ? $canonical : $key;
    }

    /**
     * Read a boolean setting given as a Blade attribute string.
     *
     * `escape="false"` passes the STRING 'false', which is truthy — the classic Blade footgun,
     * silently enabling what the author meant to disable. Normalizing it here makes
     * `escape="false"` and `:escape="false"` agree.
     */
    protected function normalizeBooleanValue(string $key, mixed $value): mixed
    {
        if (! is_string($value) || ! in_array($key, $this->booleanSettingKeys(), true)) {
            return $value;
        }

        return match ($value) {
            'true' => true,
            'false' => false,
            default => $value,
        };
    }

    /**
     * The settings that only ever hold a boolean.
     *
     * Deliberately excludes the settings that default to false but legitimately accept a string
     * (`help`, `success`, `prepend`, `append`): `help="false"` must stay the text "false". `group`
     * has its own handling above.
     *
     * @return array<int, string>
     */
    protected function booleanSettingKeys(): array
    {
        return [
            'escape',
            'switch',
            'inline',
            'custom',
            'show_all_errors',
            'show_valid_feedback',
            'disable_errors',
        ];
    }

    /**
     * The recognized BF setting names: the builder's inheritable settings (SSOT) plus the
     * per-input multi-word settings that are casing-sensitive.
     *
     * @return array<int, string>
     */
    protected function settingKeys(): array
    {
        return [...BF::settings()->keys()->all(), 'disable_errors'];
    }

    /**
     * The child-attribute prefixes this component supports, as `prefix => options key`.
     * Empty by default so `option:` / `optgroup:` are only recognized by choice components
     * ({@see Select}, {@see Choice}) and never leak elsewhere.
     *
     * @return array<string, string>
     */
    protected function childAttributeGroups(): array
    {
        return [];
    }

    /**
     * Match a `prefix:attr` key against the supported child-attribute prefixes.
     *
     * @return array{0: string, 1: string}|null [options key, attribute name], or null
     */
    protected function matchChildAttributeGroup(string $key): ?array
    {
        foreach ($this->childAttributeGroups() as $prefix => $target) {
            $needle = $prefix.':';

            if (str_starts_with($key, $needle)) {
                return [$target, substr($key, strlen($needle))];
            }
        }

        return null;
    }

    /**
     * The rendered content of a named slot, or null when absent/empty.
     *
     * Read from __laravel_slots (the actual slots) rather than $data[$name], so a slot never
     * collides with a public property of the same name (e.g. the label prop).
     *
     * Returned as a HtmlString: a slot is markup by construction, so it must keep the raw regime
     * even under `escape => true` — the author wrote that markup in the template on purpose.
     *
     * @param  array<string, mixed>  $data
     */
    protected function namedSlot(array $data, string $name): ?HtmlString
    {
        $slots = $data['__laravel_slots'] ?? [];

        if (! isset($slots[$name])) {
            return null;
        }

        $content = trim((string) $slots[$name]);

        return $content !== '' ? new HtmlString($content) : null;
    }

    /**
     * Resolve the label text: a <x-slot:label> overrides the label attribute/prop.
     *
     * @param  array<string, mixed>  $data
     */
    protected function resolveLabel(array $data, mixed $label): mixed
    {
        return $this->namedSlot($data, 'label') ?? $label;
    }

    /**
     * Fold <x-slot:prepend> / <x-slot:append> into the options as rich input-group addons.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    protected function withAddonSlots(array $data, array $options): array
    {
        foreach (['prepend', 'append'] as $addon) {
            $slot = $this->namedSlot($data, $addon);

            if ($slot !== null) {
                $options[$addon] = $slot;
            }
        }

        return $options;
    }
}
