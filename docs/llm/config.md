<!--
Sources: src/config/config.php, src/BootstrapFormServiceProvider.php, src/BootstrapForm.php
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
| `bootstrap_version` | `4` \| `5` = `5` | Selects the version driver (markup vocabulary). Default `5`; set `4` for legacy Bootstrap 4 (fully supported). Overridable per form / per field. See [bootstrap5.md](bootstrap5.md). |
| `layout` | `vertical` \| `horizontal` \| `inline` \| `floating` = `vertical` | Default form layout. See [layouts.md](layouts.md). |
| `group` | `array` = `[]` | Application-wide default HTML attributes for the form-group wrapper. A `class` declared here **replaces** `group_class`; `false` drops the wrapper entirely. |
| `show_all_errors` | `bool` = `false` | Render all of a field's error messages instead of only the first. See [model-binding.md](model-binding.md). |
| `show_valid_feedback` | `bool` = `false` | After a failed submit, mark error-free fields valid (`is-valid`); a per-field `success` message then renders a `valid-feedback`. |
| `required_mark` | `string` \| `false` = `' *'` | Mark appended to the label of any field carrying the HTML `required` attribute. HTML accepted verbatim; `false` disables. See below. |

The `bootstrap_version` and `layout` values, plus `custom`, `show_all_errors`, `show_valid_feedback`,
`required_mark` and the version-section keys below, are **inheritable settings**: a form default
cascades to its fields, and each field may override. The one exception is `group_class`, which is
set in config only (see below).

### `required_mark`

The mark is appended to the **label** of any field whose HTML `required` attribute is set (in any
form: `['required' => true]`, `['required' => 'required']`, or the bare `['required']`). Notes:

- **HTML is accepted verbatim** — spacing/markup lives in the value itself, which is why the default
  already includes a leading space (`' *'`). Example value: `' <span class="text-danger">*</span>'`.
- **`false` disables** the mark (globally, per form, or per field).
- **Smart on collections** — on `checkboxes` / `radios` the mark is applied to the **global** label
  only, never to the individual choice labels (see [choice-fields.md](choice-fields.md)).
- It is a **setting**, not an HTML attribute (never rendered on the element); see
  [options-and-attributes.md](options-and-attributes.md).

```php
// Global default ' *' — the field has the required attribute, so the label gets the mark:
BF::text('email', 'Email', null, ['required' => true]);
// <label for="email" class="form-label">Email *</label> … <input required …>

// HTML mark, per field:
BF::text('email', 'Email', null, ['required' => true, 'required_mark' => ' <span class="text-danger">*</span>']);

// Disable per form (or per field):
BF::open(['required_mark' => false]);
```

---

## Version sections — `bootstrap4` / `bootstrap5`

Layout-level, app-tunable options applied for the active version. Component classes (`form-control`,
`form-check`, …) are **driver code, not configurable** — only these live in config.

| Key | `bootstrap4` default | `bootstrap5` default | Effect |
|---|---|---|---|
| `custom` | `false` | *(n/a — no-op)* | Use Bootstrap 4 custom-styled controls by default. |
| `group_class` | `form-group` | `mb-3` | **Default** class(es) on every form-group wrapper (`false` for none). Config-level only. See below. |
| `left_class` | `col-lg-2 col-xl-3` | `col-lg-2 col-xl-3` | Horizontal: label column width. |
| `right_class` | `col-lg-10 col-xl-9` | `col-lg-10 col-xl-9` | Horizontal: control column width. |
| `pull_right` | `hidden-md-down col-lg-2 col-xl-3` | `d-none d-lg-block col-lg-2 col-xl-3` | Horizontal: spacer column for label-less fields (`false` to disable). |
| `lspace` | `mr-2` | `me-2` | Inline: label→field spacing. |
| `hspace` | `mr-3` | `me-3` | Inline: between-group horizontal spacing. |
| `vspace` | `my-1` | `my-1` | Inline: between-group vertical spacing. |

The `bootstrap5` section uses the `-e`/`-s` spacing suffixes (`me-*`, `ms-*`) in place of Bootstrap 4's
`-r`/`-l`. `custom` is intentionally absent from `bootstrap5` (no-op there) but stays a recognized
setting so it is never emitted as an HTML attribute.

### `group_class`

The **default** class(es) on the form-group wrapper of every field. Unlike the other version-section
keys it is set **here only** — there is a single way to override it at a call site, and it is the
group's own attribute bag:

```php
// App-wide default: config/bootstrap_form.php → 'bootstrap5' => ['group_class' => 'mb-4', …]

BF::open(['group' => ['class' => 'mb-4']]);                        // this form and all its fields
BF::text('login', null, null, ['group' => ['class' => 'mb-0']]);   // this field
BF::text('login', null, null, ['group' => ['class' => false]]);    // <div id="login-group">
```

A supplied class **replaces** the default rather than adding to it, and also takes over the inline
spacing — see the class-ownership rule in
[options-and-attributes.md](options-and-attributes.md). Driver classes always survive:

| Option | Result |
|---|---|
| *(nothing)* | `<div id="x-group" class="mb-3">` |
| `group => ['class' => 'mb-4']` | `<div class="mb-4" id="x-group">` |
| `group => ['class' => false]` | `<div id="x-group">` |
| `group => false` | **no wrapper at all** — the control is rendered bare |
| `group => ['class' => 'mb-4']`, horizontal | `<div class="mb-4 row" id="x-group">` — `row` is driver-owned |

### Partial version sections

Laravel's `mergeConfigFrom()` merges only the **top level** of a config file, so a published
`config/bootstrap_form.php` **replaces** the whole `bootstrap4` / `bootstrap5` array. The package
defaults act as a **floor** under each section: a key absent from the published section falls back
to its packaged default rather than to `null`. A config file published before a key existed keeps
rendering correctly — only the keys you actually declare override the package.

> Migrating a published v2 config to v3+: move the layout options (`custom`, `left_class`,
> `right_class`, `pull_right`, `lspace`, `hspace`, `vspace`) under the `bootstrap4` (and/or
> `bootstrap5`) section, or republish with `--force`.
