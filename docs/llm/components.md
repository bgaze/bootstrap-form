<!--
Sources: src/View/Components/*.php, src/View/Components/Concerns/ResolvesBootstrapAttributes.php,
         src/BootstrapFormServiceProvider.php (componentNamespace)
Goldens: tests/ComponentIntegrationTest.php and other tests/Component*Test.php parity checks
Keep in sync in the SAME commit as any change to the files above (see CLAUDE.md § Documentation).
-->

# Blade x-components (`<x-bf::…>`)

The x-components are the **default Blade syntax**. They **delegate to the `BF` facade**, so their
output is **byte-identical** to the facade and directives. Enabled by default (config `components`);
they register under the `bf` namespace.

---

## Tags

`<x-bf::form>` (wraps its fields as the default slot, auto-closes) plus one tag per field/element:

`text`, `email`, `url`, `tel`, `number`, `date`, `time`, `datetime-local`, `month`, `week`, `search`,
`color`, `textarea`, `password`, `select`, `file`, `range`, `hidden`, `checkbox`, `checkboxes`,
`radio`, `radios`, `label`, `submit`, `reset`, `button`, `link`.

> Note the kebab-case tag for `datetimeLocal`: **`<x-bf::datetime-local>`**.

Constructor arguments map to attributes; pass PHP values with Blade's `:` binding
(`:choices`, `:selected`, `:checked`, `:value`, `:label`, `:model`):

```blade
<x-bf::form :model="$user" update="users.update" horizontal>
    <x-bf::text name="name"/>
    <x-bf::select name="role" :choices="$roles" :selected="$user->role"/>
    <x-bf::submit>Save</x-bf::submit>
</x-bf::form>
```

`<x-bf::link>` takes `href` (+ `title` or slot); `<x-bf::label>` takes `name` (or the HTML idiom
`for`) + `value`/slot.

---

## Attribute projection

The attribute bag is translated into a `BF` options array by these rules:

| Attribute pattern | Becomes |
|---|---|
| `label:*` | a `<label>` HTML attribute (`label:class="fw-bold"`) |
| `group:*` | a form-group wrapper attribute (`group:class="mb-4"`) |
| `group` | `false` disables the wrapper; an array sets its attributes |
| `input:*` | a **literal** input HTML attribute — the x-component equivalent of the `~` escape (`input:size="10"`) |
| `option:*` / `optgroup:*` | blanket child attributes — **only** on `<x-bf::select>`, `<x-bf::checkboxes>`, `<x-bf::radios>` |
| a known setting name (kebab/camel) | normalized to the snake_case setting (`error-bag` → `error_bag`, `show-all-errors` → `show_all_errors`) |
| anything else | an HTML attribute on the control, verbatim (`data-*`, `aria-*`, `placeholder`, `required`, …) |

Boolean attributes follow Blade: a bare attribute (`required`, `switch`, `inline`, `multiple`) passes
`true`. See [options-and-attributes.md](options-and-attributes.md) for the underlying partition and the
`~` / `input:` escape, and [choice-fields.md](choice-fields.md) for `option:` / `optgroup:`.

---

## Slots

| Slot | On | Effect |
|---|---|---|
| default slot | `submit` / `reset` / `button` / `link` / `label` | the button/link/label text (alternative to the `value` attribute) |
| default slot | `form` | the form fields (wrapped between open and close) |
| `<x-slot:label>` | any field | the label (overrides the `label` attribute) |
| `<x-slot:prepend>` / `<x-slot:append>` | text-like / select | input-group addons (see [input-groups.md](input-groups.md)) |

```blade
<x-bf::submit>Log in</x-bf::submit>
<x-bf::text name="amount">
    <x-slot:prepend>$</x-slot:prepend>
</x-bf::text>
```

---

## Layout shortcuts on `<x-bf::form>`

Boolean attributes `vertical` / `horizontal` / `inline` / `floating` are sugar for `layout="…"` and
never leak onto the `<form>` tag:

```blade
<x-bf::form url="/x" horizontal>   {{-- === layout="horizontal" --}}
```

See [layouts.md](layouts.md).

---

## Parity contract

Because every component delegates to `BF`, `<x-bf::text name="field"/>` renders exactly
`BF::text('field')`. The `tests/Component*Test.php` suite enforces this parity — any new component or
projection rule must ship with its parity/guard test in the same commit (see CLAUDE.md § Pitfalls).
