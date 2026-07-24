<!--
Sources: src/Support/Traits/HasAddons.php, src/Support/Drivers/VersionDriver.php (addonText),
         src/Support/Drivers/Bootstrap4Driver.php, src/Support/Drivers/Bootstrap5Driver.php,
         src/Support/Input.php (help)
Goldens: tests/golden/b5/text.prepend_append.html, tests/golden/b5/text.help.html (default),
         tests/golden/b5/float.addon.html, tests/golden/b4/text.prepend_append.html (B4),
         tests/golden/b4/error.help_describedby.html
Keep in sync in the SAME commit as any change to the files above (see CLAUDE.md § Documentation).
-->

# Input groups, addons, help text & sizing

Applies to the **text-like inputs and `select`** (the fields using `HasAddons`). See the [hub](index.md).

---

## Prepend / append addons

`prepend` and `append` accept a **string** or an **array** (each item resolved independently).
Providing either wraps the control in a Bootstrap **input group**.

**Text vs HTML is auto-detected per item** (both Bootstrap versions):

- a value **without an HTML tag** is treated as plain text: it is **escaped** and wrapped in an
  `.input-group-text` span for you — the common units / currency / symbol case;
- a value **containing an HTML tag** (`<span>`, `<button>`, `<i>`, `<!-- -->`, …) is emitted
  **verbatim, unescaped** — you own the markup, so buttons, dropdowns or a hand-written
  `.input-group-text` pass through untouched.

Detection keys on a tag opening (`<` immediately followed by a letter, `!` or `/`), so bare content
such as `°C`, `$`, `R&D` or `a < b` stays text.

```blade
<x-bf::text name="amount" prepend="$" append=".00"/>
```
```html
<div id="amount-group" class="mb-3"><label for="amount" class="form-label">Amount</label><div><div class="input-group"><span class="input-group-text">$</span><input id="amount" class="form-control" name="amount" type="text"><span class="input-group-text">.00</span></div></div></div>
```

Facade / slot forms (the slot content is resolved the same way — text is wrapped, HTML passes through):

```php
echo BF::text('amount', 'Amount', null, ['prepend' => '$', 'append' => '.00']);
```
```blade
<x-bf::text name="amount">
    <x-slot:prepend>$</x-slot:prepend>
    <x-slot:append>.00</x-slot:append>
</x-bf::text>
```

To force a non-text addon (a button, an icon, custom markup), pass HTML — it is not wrapped:

```blade
<x-bf::text name="search" append='<button type="button" class="btn btn-outline-secondary">Go</button>'/>
```

**Version difference:** the text-vs-HTML detection and the `.input-group-text` span are identical
across versions; only the **placement** differs. **Bootstrap 5** (above) sets addons as **direct
children** of `.input-group`. **Bootstrap 4 (legacy)** nests each in an `.input-group-prepend` /
`.input-group-append` div (see [bootstrap5.md](bootstrap5.md)):

```html
<!-- Bootstrap 4 (legacy) -->
<div id="amount-group" class="form-group"><label for="amount">Amount</label><div><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input id="amount" class="form-control" name="amount" type="text"><div class="input-group-append"><span class="input-group-text">.00</span></div></div></div></div>
```

Validation feedback is forced to display as a block (`invalid-feedback d-block`) inside an input group.

---

## Control sizing

`size => 'sm' | 'lg'` sizes the control (and its input group):

- text-like → `form-control-sm` / `-lg`
- select → `form-control-{sm,lg}` (or `custom-select-{sm,lg}` when `custom`, B4) / `form-select-{sm,lg}` (B5)
- an input group carrying a size also gets `input-group-sm` / `-lg`

```blade
<x-bf::text name="code" size="sm"/>   {{-- <input … class="form-control form-control-sm"> --}}
```

> `size` on a **`textarea`** means something else: `cols x rows` (e.g. `size="30x5"`), matching the
> historical Collective behavior.

---

## Help text

`help => '...'` renders a `<small class="form-text">` after the control, wired to it for screen readers
via `aria-describedby`:

```blade
<x-bf::text name="login" help="Some help"/>
```
```html
<div id="login-group" class="mb-3"><label for="login" class="form-label">Login</label><div><input id="login" class="form-control" aria-describedby="login-help" name="login" type="text"><small id="login-help" class="form-text">Some help</small></div></div>
```

When both an error and help are present, both ids are referenced
(`aria-describedby="login-error login-help"`). See [model-binding.md](model-binding.md) for error rendering.

---

## Addons + floating layout

In the floating layout (Bootstrap 5), the `.form-floating` block nests **inside** the input group:

```html
<div class="input-group"><span class="input-group-text">$</span><div class="form-floating"><input id="amount" class="form-control" placeholder=" " name="amount" type="text"><label for="amount">Amount</label></div></div>
```
