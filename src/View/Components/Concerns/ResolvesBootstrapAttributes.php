<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components\Concerns;

use Bgaze\BootstrapForm\Support\Attributes;
use Bgaze\BootstrapForm\Support\Facades\BF;
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
                $options[$this->normalizeSettingKey($key)] = $value;
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
     * @return array{0: string, 1: string}|null  [options key, attribute name], or null
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
     * @param  array<string, mixed>  $data
     */
    protected function namedSlot(array $data, string $name): ?string
    {
        $slots = $data['__laravel_slots'] ?? [];

        if (! isset($slots[$name])) {
            return null;
        }

        $content = trim((string) $slots[$name]);

        return $content !== '' ? $content : null;
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
