<!--
Sources: src/BootstrapForm.php, src/BootstrapFormServiceProvider.php, src/config/config.php,
         src/Support/Input.php, src/Support/Options.php, src/Support/Attributes.php,
         src/View/Components/*.php
Goldens: tests/golden/b5/text.html (default), tests/golden/b4/*.html (B4 baseline)
Keep in sync in the SAME commit as any change to the files above (see CLAUDE.md § Documentation).
-->

# bootstrap-form — LLM usage guide (hub)

`bgaze/bootstrap-form` renders **Bootstrap 4/5** form markup for Laravel 12+ from a single field
description, through **three interchangeable syntaxes that produce byte-identical HTML**. It owns its
HTML layer (no `laravelcollective/html`). **Bootstrap 5 is the default; Bootstrap 4 is fully supported
for backward compatibility.**

**How to use this guide:** read this file in full — it is enough to build the large majority of forms.
Load a spoke from the [on-demand index](#on-demand-index) only when a task needs that specific area.

---

## 1. Before writing any form — two detection steps (mandatory)

Never assume defaults. Establish the project's actual conventions first.

### 1.1 Resolved configuration

- If **`config/bootstrap_form.php`** exists in the app, **it wins** — read it.
- Otherwise defaults come from `vendor/bgaze/bootstrap-form/src/config/config.php`.
- Values that change the markup you must emit: `bootstrap_version` (`4`|`5`), `layout`
  (`vertical`|`horizontal`|`inline`|`floating`), `custom` (Bootstrap 4 only), the `bootstrap4` /
  `bootstrap5` layout sections (`left_class`, `right_class`, `pull_right`, `lspace`, `hspace`,
  `vspace`), `show_all_errors`, `show_valid_feedback`, `required_mark`, `blade_directives`,
  `components`.
- Full reference: **[config.md](config.md)**.

### 1.2 Syntax in use

Detect the dominant syntax in the project's Blade views and **match it**:

| Syntax          | Detect with grep | Where it fits             |
|-----------------|------------------|---------------------------|
| x-components    | `<x-bf::`        | Blade templates           |
| Blade directives| `@text(` `@open(`| Blade templates (legacy)  |
| `BF` facade     | `BF::`           | PHP (controllers, helpers)|

**Default when greenfield / no signal:** **x-components** in Blade, the **`BF` facade** in PHP.
Directives stay supported but are not the syntax to introduce into a new codebase.

---

## 2. The three syntaxes (iso-rendering)

All three below render **exactly** (Bootstrap 5, the default):

```html
<div id="field-group" class="mb-3"><label for="field" class="form-label">Field</label><div><input id="field" class="form-control" name="field" type="text"></div></div>
```

```blade
{{-- x-components — the Blade default --}}
<x-bf::form url="/x">
    <x-bf::text name="field"/>
    <x-bf::submit>Save</x-bf::submit>
</x-bf::form>
```
```blade
{{-- Blade directives --}}
@open(['url' => '/x'])
@text('field')
@submit('Save')
@close
```
```php
// BF facade — PHP context
echo BF::open(['url' => '/x']);
echo BF::text('field');
echo BF::submit('Save');
echo BF::close();
```

x-components delegate to the facade internally, so parity is guaranteed. Details, slots and the
attribute-projection rules: **[components.md](components.md)**.

---

## 3. The universal field model

Almost every field shares the signature **`(name, label, value, options)`** (deviations are in the
[catalog](#5-field-catalog)).

- **name** — required. Array names (`user[email]`) are supported; the `id` is derived by flattening
  (`user-email`), the error lookup uses dotted form (`user.email`).
- **label** — omitted/`null` → auto-generated from the name in Title Case (`Field`). `false` → no
  label. A string → custom text. An array → label element HTML attributes (in x-components use the
  `label:` prefix).
- **value** — the field value; **overridden at render time** by old input then model binding
  (see **[model-binding.md](model-binding.md)**).
- **options** — one associative array split into **two disjoint sets**:
  - **settings** — keys matching a known package setting (§4) are consumed as configuration and
    **never rendered**.
  - **HTML attributes** — every other key is rendered on the control (`placeholder`, `required`,
    `min`, `data-*`, `class`, ...). Boolean `true` renders a valueless attribute (`required`). Setting
    `required` also appends the `required_mark` to the label (default `' *'`; see [config.md](config.md)).
  - **`~` escape** — prefix a key with `~` to force it onto the element even when its name collides
    with a setting: `'~size' => '10'` renders `size="10"` instead of being read as the Bootstrap
    control-size setting. In x-components the equivalent is the `input:` prefix (`input:size="10"`).
    Full partition rules: **[options-and-attributes.md](options-and-attributes.md)**.

**`id` policy:** auto-generated from the name unless you pass one; `id => false` disables it; the
group wrapper id is `{id}-group`.

---

## 4. Settings (recognized option keys — consumed, never rendered)

Anything **not** in this list is treated as an HTML attribute.

- **Inherited from the form** (form default cascades to its fields): `layout`, `bootstrap_version`,
  `custom`, `error_bag`, `show_all_errors`, `show_valid_feedback`, `required_mark`, `left_class`,
  `right_class`, `pull_right`, `lspace`, `hspace`, `vspace`, `group`.
- **Per field, all types:** `label`, `help`, `success`.
- **Per field, text-like inputs only** (text/email/url/tel/number/date/time/datetime-local/month/
  week/search/color/textarea/password): `size` (`'sm'`|`'lg'`), `prepend`, `append`.
- **Form-only, reserved** (never inherited by fields): `model`, `url`, `route`, `action`, `store`,
  `update`.

**Resolution cascade** (each level overrides the previous): global config (default `5`) → per-form
(`BF::open(['bootstrap_version' => 4])`) → per-field (`['bootstrap_version' => 4]` in a field's
options). A per-field version override switches the driver (component classes); layout settings stay
inherited from the form.

---

## 5. Field catalog

Signature columns omit the trailing `options = []`. x-component tag is under the `bf` namespace
(`<x-bf::TAG>`). Directive is `@TAG`; facade is `BF::method`.

### Form open / close

| x-component | facade / directive | Signature | Notes |
|---|---|---|---|
| `<x-bf::form>` … `</x-bf::form>` | `BF::open` / `@open` … `@close` | `open(options)` | Wraps its slot; auto-closes. |
| `layout=` or boolean `vertical`/`horizontal`/`inline`/`floating` attr | `BF::vertical`/`horizontal`/`inline`/`floating` | `(options)` | Layout shortcuts. See **[layouts.md](layouts.md)**. |

Form open options: one of `url` / `route` / `action`, or `model` + `store`/`update` for model
binding (**[model-binding.md](model-binding.md)**), plus any inherited setting and HTML attributes
(`method`, `novalidate`, `enctype`, ...).

### Text-like inputs — `(name, label, value)`

`text`, `email` (name defaults to `email`), `url`, `tel`, `number`, `date`, `time`,
`datetimeLocal` (tag `<x-bf::datetime-local>`), `month`, `week`, `search`, `color`, `textarea`.

`password` — `(name, label)` only (no value).

### Choice inputs

| x-component | facade / directive | Signature |
|---|---|---|
| `<x-bf::select :choices :selected>` | `BF::select` / `@select` | `(name, label, choices, selected)` |
| `<x-bf::checkboxes :choices :checked>` | `BF::checkboxes` / `@checkboxes` | `(name, label, choices, checked)` |
| `<x-bf::radios :choices :checked>` | `BF::radios` / `@radios` | `(name, label, choices, checked)` |

`choices` accept a rich grammar (optgroups, per-option attributes) and any `iterable` — `array`,
`Collection` (e.g. `Model::pluck('name', 'id')`), or a generator. See **[choice-fields.md](choice-fields.md)**.

### Single checkable — `(name, label, value, checked)`

| x-component | facade / directive | value default |
|---|---|---|
| `<x-bf::checkbox>` | `BF::checkbox` / `@checkbox` | `1` |
| `<x-bf::radio>` | `BF::radio` / `@radio` | `null` |

`switch => true` renders a switch; `inline => true` inlines it. See **[choice-fields.md](choice-fields.md)**.

### Other inputs

| x-component | facade / directive | Signature | Notes |
|---|---|---|---|
| `<x-bf::file>` | `BF::file` / `@file` | `(name, label)` | `multiple`, `custom`. |
| `<x-bf::range>` | `BF::range` / `@range` | `(name, label, value)` | `min`/`max`/`step`, `custom`. |
| `<x-bf::hidden>` | `BF::hidden` / `@hidden` | `(name, value)` | No label/group. |

### Buttons & elements

| x-component | facade / directive | Signature | Notes |
|---|---|---|---|
| `<x-bf::submit>` | `BF::submit` / `@submit` | `(value, options)` | Text via attr or slot. `primary`. |
| `<x-bf::reset>` | `BF::reset` / `@reset` | `(value, options)` | `danger`. |
| `<x-bf::button>` | `BF::button` / `@button` | `(value, options)` | `primary`. |
| `<x-bf::link>` | `BF::link` / `@link` | `(url, title, options)` | Renders a button-styled `<a>`. |
| `<x-bf::label>` | `BF::label` / `@label` | `(name, value, options)` | Standalone label. |

For buttons/link/label the `options` second arg may be a Bootstrap variant string (`'success'`,
`'outline-primary'`, ...) or an options array.

---

## 6. Cookbook — the common 80%

```blade
{{-- Help text — renders <small class="form-text"> wired via aria-describedby --}}
<x-bf::text name="login" help="Your username"/>

{{-- Plain HTML attributes fall through to the control --}}
<x-bf::email name="email" placeholder="you@example.com" required autofocus/>
<x-bf::number name="qty" min="1" max="99" step="1"/>

{{-- Control sizing (text-like) --}}
<x-bf::text name="code" size="sm"/>

{{-- Input-group addons: plain text auto-wraps in .input-group-text; pass HTML (a button, an icon) to bypass — see input-groups.md --}}
<x-bf::text name="amount" prepend="$" append=".00"/>

{{-- Select with a plain value => label map --}}
<x-bf::select name="role" :choices="['admin' => 'Admin', 'editor' => 'Editor']" selected="editor"/>

{{-- Checkbox as a switch, inline --}}
<x-bf::checkbox name="accept" label="Accept terms" switch inline/>

{{-- No label / custom label attributes --}}
<x-bf::text name="q" :label="false"/>
<x-bf::text name="q" label="Search" label:class="fw-bold"/>
```

Facade equivalent of the addon example (PHP context):

```php
echo BF::text('amount', 'Amount', null, ['prepend' => '$', 'append' => '.00']);
```

---

## 7. On-demand index

Load a spoke only when the task touches its area. All paths are relative to this file.

| Spoke | Load when you need… |
|---|---|
| [choice-fields.md](choice-fields.md) | select / checkboxes / radios, the `choices` grammar, optgroups, per-option attributes, switches. |
| [layouts.md](layouts.md) | vertical / horizontal / inline / floating layouts and their column/spacing options. |
| [input-groups.md](input-groups.md) | prepend/append addons (options & slots), help text, control sizing. |
| [model-binding.md](model-binding.md) | binding an Eloquent model, `store`/`update`, old input, error display, valid feedback, `error_bag`. |
| [options-and-attributes.md](options-and-attributes.md) | the settings-vs-attributes partition, the `~` / `input:` escape, `id` policy, `custom`. |
| [components.md](components.md) | x-component specifics: tags, attribute projection (kebab→setting, `label:`/`group:`/`input:`/`option:`/`optgroup:`), boolean shortcuts, slots. |
| [bootstrap5.md](bootstrap5.md) | Bootstrap 4↔5 differences, version override, floating layout, `custom` no-op, inline caveat. |
| [config.md](config.md) | the full configuration key reference. |
