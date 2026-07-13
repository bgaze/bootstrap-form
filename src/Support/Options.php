<?php

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Single source of truth for splitting a raw options array into the two disjoint sets
 * the package handles: package "settings" (consumed as configuration) and HTML
 * "attributes" (rendered on the element). The special `group` key is never part of
 * either — it is handled on its own by the caller.
 *
 * This is where the {@see Attributes::LITERAL_PREFIX} ('~') escape takes effect: a
 * `~`-prefixed key never matches a setting name, so it is not consumed as a setting and
 * falls through to the attributes set (the prefix is stripped later, at render time).
 */
class Options
{
    /**
     * The raw options that are known settings (excluding the special `group`).
     *
     * @param  array  $raw
     * @param  Collection|array  $settingKeys
     * @return array
     */
    public static function settings(array $raw, $settingKeys)
    {
        return Arr::except(Arr::only($raw, self::keys($settingKeys)), ['group']);
    }

    /**
     * The raw options that are HTML attributes (anything that is neither a setting nor
     * `group`). `~`-prefixed keys land here by design.
     *
     * @param  array  $raw
     * @param  Collection|array  $settingKeys
     * @return array
     */
    public static function attributes(array $raw, $settingKeys)
    {
        return Arr::except($raw, array_merge(self::keys($settingKeys), ['group']));
    }

    /**
     * @param  Collection|array  $settingKeys
     * @return array
     */
    protected static function keys($settingKeys)
    {
        return $settingKeys instanceof Collection ? $settingKeys->all() : (array) $settingKeys;
    }
}
