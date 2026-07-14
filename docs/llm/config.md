<!--
Sources: src/config/config.php, src/BootstrapFormServiceProvider.php
Keep in sync in the SAME commit as any change to the files above (see CLAUDE.md § Documentation).
-->

# Configuration reference

Publish the config file to customize defaults:

```shell
php artisan vendor:publish --provider="Bgaze\BootstrapForm\BootstrapFormServiceProvider"
```

It lands at `config/bootstrap_form.php`. **When present it overrides the package defaults** — always
read it before assuming a value (see the [hub](index.md) §1.1).

---

## Root keys

| Key | Type / default | Effect |
|---|---|---|
| `blade_directives` | `bool` = `true` | Register the `@open`, `@text`, … Blade directives. |
| `components` | `bool` = `true` | Register the `bf` x-component namespace (`<x-bf::text/>`, …). Facade & directives stay available regardless. |
| `bootstrap_version` | `4` \| `5` = `4` | Selects the version driver (markup vocabulary). Overridable per form / per field. See [bootstrap5.md](bootstrap5.md). |
| `layout` | `vertical` \| `horizontal` \| `inline` \| `floating` = `vertical` | Default form layout. See [layouts.md](layouts.md). |
| `group` | `array` = `[]` | Application-wide default HTML attributes for the form-group wrapper. The group class is always added. |
| `show_all_errors` | `bool` = `false` | Render all of a field's error messages instead of only the first. See [model-binding.md](model-binding.md). |
| `show_valid_feedback` | `bool` = `false` | After a failed submit, mark error-free fields valid (`is-valid`); a per-field `success` message then renders a `valid-feedback`. |

The `bootstrap_version` and `layout` values, plus `custom`, `show_all_errors`, `show_valid_feedback`
and the version-section keys below, are **inheritable settings**: a form default cascades to its
fields, and each field may override.

---

## Version sections — `bootstrap4` / `bootstrap5`

Layout-level, app-tunable options applied for the active version. Component classes (`form-control`,
`form-check`, …) are **driver code, not configurable** — only these live in config.

| Key | `bootstrap4` default | `bootstrap5` default | Effect |
|---|---|---|---|
| `custom` | `false` | *(n/a — no-op)* | Use Bootstrap 4 custom-styled controls by default. |
| `left_class` | `col-lg-2 col-xl-3` | `col-lg-2 col-xl-3` | Horizontal: label column width. |
| `right_class` | `col-lg-10 col-xl-9` | `col-lg-10 col-xl-9` | Horizontal: control column width. |
| `pull_right` | `hidden-md-down col-lg-2 col-xl-3` | `d-none d-lg-block col-lg-2 col-xl-3` | Horizontal: spacer column for label-less fields (`false` to disable). |
| `lspace` | `mr-2` | `me-2` | Inline: label→field spacing. |
| `hspace` | `mr-3` | `me-3` | Inline: between-group horizontal spacing. |
| `vspace` | `my-1` | `my-1` | Inline: between-group vertical spacing. |

The `bootstrap5` section uses the `-e`/`-s` spacing suffixes (`me-*`, `ms-*`) in place of Bootstrap 4's
`-r`/`-l`. `custom` is intentionally absent from `bootstrap5` (no-op there) but stays a recognized
setting so it is never emitted as an HTML attribute.

> Migrating a published v2 config to v3+: move the layout options (`custom`, `left_class`,
> `right_class`, `pull_right`, `lspace`, `hspace`, `vspace`) under the `bootstrap4` (and/or
> `bootstrap5`) section, or republish with `--force`.
