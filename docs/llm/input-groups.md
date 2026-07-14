<!--
Sources: src/Support/Traits/HasAddons.php, src/Support/Drivers/Bootstrap4Driver.php,
         src/Support/Drivers/Bootstrap5Driver.php, src/Support/Input.php (help)
Goldens: tests/golden/text.prepend_append.html, tests/golden/text.help.html,
         tests/golden/float.addon.html, tests/golden/error.help_describedby.html
Keep in sync in the SAME commit as any change to the files above (see CLAUDE.md § Documentation).
-->

# Input groups, addons, help text & sizing

Applies to the **text-like inputs and `select`** (the fields using `HasAddons`). See the [hub](index.md).

---

## Prepend / append addons

`prepend` and `append` accept a **string** (raw HTML/text) or an **array** (concatenated). Providing
either wraps the control in a Bootstrap **input group**.

```blade
<x-bf::text name="amount" prepend="$" append=".00"/>
```
```html
<div id="amount-group" class="form-group"><label for="amount">Amount</label><div><div class="input-group"><div class="input-group-prepend">$</div><input id="amount" class="form-control" name="amount" type="text"><div class="input-group-append">.00</div></div></div></div>
```

Facade / slot forms:

```php
echo BF::text('amount', 'Amount', null, ['prepend' => '$', 'append' => '.00']);
```
```blade
<x-bf::text name="amount">
    <x-slot:prepend>$</x-slot:prepend>
    <x-slot:append>.00</x-slot:append>
</x-bf::text>
```

**Version difference:** Bootstrap 4 wraps each addon in `.input-group-prepend` / `.input-group-append`
divs (above). **Bootstrap 5 dropped those wrappers** — addons sit as direct children of `.input-group`:

```html
<!-- Bootstrap 5 -->
<div class="input-group">$<input …>.00</div>
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
<div id="login-group" class="form-group"><label for="login">Login</label><div><input id="login" class="form-control" aria-describedby="login-help" name="login" type="text"><small id="login-help" class="form-text">Some help</small></div></div>
```

When both an error and help are present, both ids are referenced
(`aria-describedby="login-error login-help"`). See [model-binding.md](model-binding.md) for error rendering.

---

## Addons + floating layout

In the floating layout (Bootstrap 5), the `.form-floating` block nests **inside** the input group:

```html
<div class="input-group">$<div class="form-floating"><input id="amount" class="form-control" placeholder=" " name="amount" type="text"><label for="amount">Amount</label></div></div>
```
