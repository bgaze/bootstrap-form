<!--
Sources: src/Support/Options.php, src/Support/Attributes.php, src/Support/Input.php,
         src/View/Components/Concerns/ResolvesBootstrapAttributes.php
Goldens: tests/golden/b4/text.id_explicit.html, tests/golden/b4/text.id_false.html,
         tests/golden/b4/check.option_id_override.html
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

---

## The `~` literal escape

To force a key onto the element **even though its name collides with a setting**, prefix it with `~`
(`Attributes::LITERAL_PREFIX`). The prefix is stripped at render time only.

```php
// Without ~, `size` is read as the Bootstrap control-size setting (sm|lg).
BF::text('code', 'Code', null, ['~size' => '10']);   // → <input … size="10">
```

The x-component equivalent is the **`input:`** prefix (see [components.md](components.md)):

```blade
<x-bf::text name="code" input:size="10"/>
```

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
- **`group => [...]`** → HTML attributes merged onto the wrapper (the form group class is always added;
  the id defaults to `{id}-group`). Application-wide defaults come from the `group` config key.

```php
BF::text('q', 'Search', null, ['group' => false]);            // bare, no wrapper
BF::text('q', 'Search', null, ['group' => ['class' => 'mb-4']]);
```

In x-components: `group="false"` disables it, `group:class="mb-4"` sets attributes.

---

## `custom` (Bootstrap 4)

`custom => true` opts a select / range / file / checkable into Bootstrap 4's custom-styled controls.
It is a recognized setting in **both** versions (so it is never emitted as an attribute) but a **no-op
in Bootstrap 5**, where custom controls were merged into the defaults. See [bootstrap5.md](bootstrap5.md).
