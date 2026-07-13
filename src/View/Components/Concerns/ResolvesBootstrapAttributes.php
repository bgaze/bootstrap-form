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
        $groupDisabled = false;

        foreach ($this->attributes->getAttributes() as $key => $value) {
            if (str_starts_with($key, 'label:')) {
                $label[substr($key, 6)] = $value;
            } elseif (str_starts_with($key, 'group:')) {
                $group[substr($key, 6)] = $value;
            } elseif (str_starts_with($key, 'input:')) {
                $options[Attributes::LITERAL_PREFIX.substr($key, 6)] = $value;
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
}
