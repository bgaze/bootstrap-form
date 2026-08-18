<!--
Sources: src/Support/Options.php, src/Support/Attributes.php, src/Support/Input.php,
         src/Support/Html.php (content), src/Support/Traits/HasAddons.php,
         src/View/Components/Concerns/ResolvesBootstrapAttributes.php
Goldens: tests/golden/b4/text.id_explicit.html, tests/golden/b4/text.id_false.html,
         tests/golden/b4/check.option_id_override.html
Tests:   tests/ConfigurableClassesTest.php (class ownership, control vs group),
         tests/SettingsPartitionTest.php (both directions of the partition, `tag`, `~size`)
         tests/SettingsTableCoverageTest.php (this file's reference table vs the code),
         tests/EscapeSettingTest.php (the `escape` regime), tests/RawContentTest.php (the default)
Keep in sync in the SAME commit as any change to the files above (see CLAUDE.md § Documentation).
-->

# Options & attributes — the partition, `~`, `id`, `label`, `group`

Every field takes one `options` associative array. This file explains exactly how it is split and the
escape hatches. See the [hub](index.md) for the overview.

---

## The settings / attributes partition

`Options` splits the raw array into **two disjoint sets**, keyed on the field's **known setting names**:

- **Settings** — keys that match a setting the field understands (see the hub §4 list, plus each
  field's specific settings). Consumed as configuration; **never rendered**.
- **HTML attributes** — every other key. Rendered on the control verbatim: `placeholder`, `required`,
  `min`, `max`, `step`, `autocomplete`, `class`, `data-*`, `aria-*`, ...

```php
BF::text('login', 'Login', null, [
    'help'        => 'Your username', // setting  → <small class="form-text">
    'placeholder' => 'jdoe',          // attribute → placeholder="jdoe"
    'required'    => true,            // attribute → valueless `required`
]);
```

**Attribute value rules:** `true` → a **valueless** attribute (`required`); `false` / `null` → the
attribute is **omitted**; any other scalar → `name="value"`. `class` is merge-aware (deduplicated,
order preserved).

> **`required` also drives the label mark.** While `required` is rendered as an attribute, its presence
> additionally appends the `required_mark` **setting** (default `' *'`) to the label. The mark itself is
> a setting (never rendered as an attribute) and accepts HTML / `false`; see [config.md](config.md). On
> `checkboxes` / `radios` it marks the global label only — see [choice-fields.md](choice-fields.md).

---

## The `~` literal escape

To force a key onto the element **even though its name collides with a setting**, prefix it with `~`
(`Attributes::LITERAL_PREFIX`). The prefix is stripped at render time only.

```php
// Without ~, `size` is read as the Bootstrap control-size setting (sm|lg).
BF::text('code', 'Code', null, ['~size' => '10']);   // → <input … size="10">
```

> **`tag` is the one recognized name you can never use.** It is consumed as a setting on text-like
> and checkable fields, then overwritten by the builder — so `['tag' => 'email']` neither changes the
> input type nor reaches the element. There is no way to render an HTML attribute named `tag`.

The x-component equivalent is the **`input:`** prefix (see [components.md](components.md)):

```blade
<x-bf::text name="code" input:size="10"/>
```

---

## Content escaping — the `escape` setting

The **content sinks** — the field `label`, `help`, `success` and the `prepend` / `append` addons — emit
their value as raw HTML, because injecting markup into a label or an addon is a real need.
`escape => true` makes them escape it instead. Default `false`, so nothing changes for an existing
application; it is an **inherited setting** (config → per form → per field).

```php
BF::text('q', '<b>Bold</b> & co');
// <label for="q" class="form-label"><b>Bold</b> & co</label>

BF::text('q', '<b>Bold</b> & co', null, ['escape' => true]);
// <label for="q" class="form-label">&lt;b&gt;Bold&lt;/b&gt; &amp; co</label>
```

| Sink | `escape => false` (default) | `escape => true` |
|---|---|---|
| `label`, `help`, `success` | raw | escaped |
| `prepend` / `append` | raw **when the value carries a tag**, otherwise escaped and wrapped in `.input-group-text` | always escaped and wrapped — the heuristic is retired |

**Addons are where it earns the most.** By default the escaping decision is taken by the *value*, so
the same call site is escaped for `°C` and raw the day its value happens to contain a tag. With
`escape` the decision belongs to the call site. Full truth table: [input-groups.md](input-groups.md).

**Per-value opt-out — pass a `Htmlable`.** An `HtmlString` (or any `Htmlable`) is markup by
construction and is never escaped, whatever the setting says:

```php
use Illuminate\Support\HtmlString;

BF::text('amt', 'Amt', null, [
    'escape' => true,
    'prepend' => new HtmlString('<button type="button" class="btn btn-outline-secondary">Go</button>'),
]);
```

That is what keeps the x-component **slots** working under a global `escape => true`: a slot is markup
the author wrote in the template, so it is handed over as an `HtmlString` (see
[components.md](components.md)). The bypass skips the **escaping** decision, not the **wrapping** one —
a tag-free `Htmlable` addon is still wrapped as a text addon, exactly like a tag-free string.

**Three things it does not cover:**

- **`required_mark` is never escaped.** It comes from the configuration, it is developer-authored, and
  emitting its HTML verbatim is a documented feature — so it is appended *after* the label has been
  escaped.
- **Validation error messages** keep their own raw path.
- **The standalone `BF::label()`** has its own per-call knob (a fourth argument, default `false`), not
  this setting.

Escaping uses `htmlspecialchars` with `ENT_QUOTES` and **no double-encoding** — the same policy as HTML
attributes, `textarea` content and `select` options, so an already-encoded entity survives a round trip
instead of becoming `&amp;amp;`.

**Not a substitute for escaping at the application boundary.** Content coming from user input, the
database or translation files should be escaped before it reaches a sink, whether or not this setting
is on.

---

## `id` policy

- **Auto-generated** from the name when none is given: the name is flattened by replacing `.`, `[`,
  `]` with `-` (`user[email]` → `user-email`).
- **`id => false`** disables the attribute entirely (the label then renders with no `for`).
- **Explicit `id`** is kept, in its original attribute position.
- Derived ids: group wrapper `{id}-group`, help `{id}-help`, error `{id}-error`, valid feedback
  `{id}-valid`. When the field has no id, none of these are emitted.

```html
<!-- id="login" explicit -->  <input id="login" …>
<!-- id => false -->          <input …> (no id; <label> has no for)
```

For choice collections, each child id is `{name}-{value}`; a per-item `id` (advanced choice form)
overrides it. See [choice-fields.md](choice-fields.md).

---

## `label`

- Omitted / `null` / empty → auto-generated from the name in Title Case.
- A **string** → custom text.
- **`false`** → no label rendered.
- An **array** → HTML attributes for the `<label>` element (in x-components: the `label:` prefix).

```php
BF::text('q', false);                        // no label
BF::text('q', 'Search', null, ['label' => ['class' => 'fw-bold']]);
```

---

## `group`

The form group is the `<div>` wrapper around label + control + feedback.

- **`group => false`** → render the control **without** the wrapper.
- **`group => [...]`** → HTML attributes for the wrapper (the id defaults to `{id}-group`).
  Application-wide defaults come from the `group` config key. Setting a `class` here takes over the
  wrapper's styling — see the rule below.

```php
BF::text('q', 'Search', null, ['group' => false]);            // bare, no wrapper
BF::text('q', 'Search', null, ['group' => ['class' => 'mb-4']]);
```

In x-components: `group="false"` disables it, `group:class="mb-4"` sets attributes.

---

## Class ownership — one channel per element

> **Supply a class for an element and you own its styling: the package then adds only the classes
> its version driver requires. Every config-sourced class is skipped.**

There is exactly **one** way to style each element — its own attribute bag — and it replaces the
**config-sourced** classes, never the driver's. So `false` means *none of mine*, not *no class*:

| Call | Rendered |
|---|---|
| `BF::text('q', 'Search')` | `<input id="q" class="form-control" …>` |
| `['class' => 'custom']` | `<input class="custom form-control" …>` — no config class applies to a control, so yours is added |
| `['class' => false]` | `<input class="form-control" …>` — **the driver class survives** |
| `['group' => ['class' => false]]` | `<div id="q-group">` — empty, because `group_class` is config-sourced |
| `['group' => false]` | no wrapper at all |

A `false` class only empties the attribute on an element whose classes are **all** config-sourced —
the group wrapper, outside the horizontal layout. A control always carries a driver class, so it
always keeps one.

| Element | Your attribute | Always added (driver) | Skipped (config) |
|---|---|---|---|
| Form group | `group => ['class']` / `group:class` | `row` (horizontal), `is-invalid` / `is-valid` | `group_class`, `hspace`, `vspace` |
| Label | `label => ['class']` / `label:class` | `form-label`, `col-form-label`, `form-check-label`, `pt-0` | `left_class`, `lspace` |
| Control | `class` | `form-control`, `form-select`, `form-check-input`, size, state | *(none apply)* |

```php
BF::text('q', 'Search');                                          // <div id="q-group" class="mb-3">
BF::text('q', 'Search', null, ['group' => ['class' => 'mb-4']]);  // <div class="mb-4" id="q-group">
BF::text('q', 'Search', null, ['group' => ['class' => false]]);   // <div id="q-group">
```

The driver classes always survive, so a styled element never stops being the right Bootstrap
component. Everything else is yours — including the horizontal column width, which comes from the
`left_class` config key:

```php
// Horizontal layout — restate the width when you style the label:
BF::text('q', 'Search', null, ['label' => ['class' => 'fw-bold col-lg-2 col-xl-3']]);
```

**Set the defaults in config, not at call sites.** `group_class` (version sections) is the class
every group gets; `BF::open(['group' => ['class' => 'mb-4']])` covers a whole form, since `group` is
inherited by its fields. See [config.md](config.md).

---

## Reference — every setting, and the test that pins it

The **anchor** column names the golden or test that characterizes the setting's observable effect.
An **empty anchor is a defect**: it marks a behaviour this guide claims and nothing verifies.

### Inherited — set on the form (or in config), overridable per field

| Setting | Default | Accepted | Applies to | Anchor |
|---|---|---|---|---|
| `layout` | `vertical` | `vertical`\|`horizontal`\|`inline`\|`floating` | form + every field | `tests/golden/b5/layout.horizontal.html` |
| `bootstrap_version` | `5` | `4`\|`5` | form + every field | `tests/VersionOverrideTest.php` |
| `custom` | `false` | bool | select / file / range / checkable, **B4 only** | `tests/golden/b4/select.custom.html` |
| `error_bag` | `'default'` | string | form + every field | `tests/ErrorSettingsTest.php` |
| `show_all_errors` | `false` | bool | form + every field | `tests/ErrorSettingsTest.php` |
| `show_valid_feedback` | `false` | bool | form + every field | `tests/golden/b5/valid.text_success.html` |
| `required_mark` | `' *'` | string\|`false` | form + every field | `tests/RequiredMarkTest.php` |
| `escape` | `false` | bool | form + every field | `tests/EscapeSettingTest.php` |
| `group` | `[]` | array\|`false` | form + every field | `tests/ConfigurableClassesTest.php` |
| `group_class` | `mb-3` (B4 `form-group`) | string\|`false` | **config only** — override by styling the group | `tests/ConfigurableClassesTest.php` |
| `left_class` | `col-lg-2 col-xl-3` | string | horizontal | `tests/golden/b5/layout.horizontal.html` |
| `right_class` | `col-lg-10 col-xl-9` | string | horizontal, label-less field | `tests/LayoutTest.php` |
| `pull_right` | `d-none d-lg-block col-lg-2 col-xl-3` | string\|`false` | horizontal, label-less field | `tests/LayoutTest.php` |
| `lspace` | `me-2` (B4 `mr-2`) | string\|`false` | inline | `tests/golden/b5/layout.inline.html` |
| `hspace` | `me-3` (B4 `mr-3`) | string\|`false` | inline | `tests/golden/b5/layout.inline.html` |
| `vspace` | `my-1` | string\|`false` | inline | `tests/golden/b5/layout.inline.html` |

### Per field

| Setting | Default | Accepted | Applies to | Anchor |
|---|---|---|---|---|
| `label` | auto from the name | string\|`false`\|array | every field | `tests/golden/b5/text.html` |
| `help` | `false` | string (**raw HTML**) | every field | `tests/golden/b5/text.help.html` |
| `success` | `false` | string (**raw HTML**) | every field | `tests/golden/b5/valid.text_success.html` |
| `size` | `null` | `'sm'`\|`'lg'` | text-like, select | `tests/golden/b4/text.size_sm.html` |
| `prepend` / `append` | `false` | string\|array (**raw when it carries a tag**) | text-like, select, file | `tests/golden/b5/text.prepend_append.html` |
| `choices` | `[]` | iterable | select, checkboxes, radios | `tests/golden/b5/select.native.html` |
| `option_attributes` | `[]` | array | select, checkboxes, radios | `tests/golden/b5/checkboxes.option_attributes.html` |
| `optgroup_attributes` | `[]` | array | select | `tests/SelectInputTest.php` |
| `checked` | `null` | scalar\|array | checkbox, radio, checkboxes, radios | `tests/golden/b5/radios.checked.html` |
| `inline` | `false` | bool | checkable | `tests/golden/b4/check.inline.html` |
| `switch` | `false` | bool | checkbox, checkboxes | `tests/golden/b5/checkbox.switch.html` |
| `disable_errors` | `false` | bool | checkbox, radio | `tests/FeedbackPlacementTest.php` |
| `text` | `'Choose file'` | string | file, **B4 `custom` only** | `tests/FileInputTest.php` |
| `button` | `null` | string | file, **B4 `custom` only** | `tests/FileInputTest.php` |
| `tag` | per field | — | text-like, checkable — **always overwritten, never usable** | `tests/SettingsPartitionTest.php` |

### Form-only, never inherited

| Setting | Accepted | Anchor |
|---|---|---|
| `model` | Eloquent model | `tests/golden/b5/model.text.html` |
| `url` / `route` / `action` | string\|array (exactly one of the three) | `tests/FormTest.php` |
| `store` / `update` | route name, `Controller@method`, or array | `tests/FormOptionsTest.php` |
| `files` | bool — adds `enctype="multipart/form-data"` | `tests/FormOptionsTest.php` |
| `method` | string — `PUT`/`PATCH`/`DELETE` are spoofed | `tests/FormTest.php` |

> **Not a setting, but intercepted:** `placeholder` on a `select` is passed as an attribute and
> consumed at render, where it becomes a leading blank pre-selected option instead of being emitted.
> See [choice-fields.md](choice-fields.md).

---

## `custom` (Bootstrap 4)

`custom => true` opts a select / range / file / checkable into Bootstrap 4's custom-styled controls.
It is a recognized setting in **both** versions (so it is never emitted as an attribute) but a **no-op
in Bootstrap 5**, where custom controls were merged into the defaults. See [bootstrap5.md](bootstrap5.md).
