<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Single source of truth for splitting a raw options array into the two disjoint sets
 * the package handles: package "settings" (consumed as configuration) and HTML
 * "attributes" (rendered on the element). The special `group` key belongs to neither —
 * it is handled on its own by the caller.
 *
 * This is where the {@see Attributes::LITERAL_PREFIX} ('~') escape takes effect: a
 * `~`-prefixed key never matches a setting name, so it is not consumed as a setting and
 * falls through to the attributes set (the prefix is stripped later, at render time).
 */
class Options
{
    public static function settings(array $raw, Collection|array $settingKeys): array
    {
        return Arr::except(Arr::only($raw, self::keys($settingKeys)), ['group']);
    }

    public static function attributes(array $raw, Collection|array $settingKeys): array
    {
        return Arr::except($raw, [...self::keys($settingKeys), 'group']);
    }

    protected static function keys(Collection|array $settingKeys): array
    {
        return $settingKeys instanceof Collection ? $settingKeys->all() : $settingKeys;
    }
}
