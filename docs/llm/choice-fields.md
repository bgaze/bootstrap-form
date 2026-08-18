<!--
Sources: src/Inputs/SelectInput.php, src/Inputs/CheckInput.php, src/Inputs/CheckChoice.php,
         src/Support/ChoiceList.php, src/Support/Input.php, src/View/Components/Select.php,
         src/View/Components/Choice.php,
         src/View/Components/Checkboxes.php, src/View/Components/Radios.php,
         src/View/Components/Checkbox.php, src/View/Components/Radio.php
Goldens: tests/golden/b5/*.html (default), tests/golden/b4/select.*.html, tests/golden/b4/check.*.html,
         tests/golden/b4/error.check_help.html, tests/golden/b4/valid.check_success.html (B4)
Keep in sync in the SAME commit as any change to the files above (see CLAUDE.md § Documentation).
-->

# Choice fields — select, checkboxes, radios

Covers `select`, `checkboxes`, `radios` (multi-choice) and the single `checkbox` / `radio`, plus the
shared **`choices` grammar** (`ChoiceList`). See the [hub](index.md) for the field model and options
partition.

---

## `select`

`BF::select(name, label = null, choices = [], selected = null, options = [])`
→ `<x-bf::select name label :choices :selected/>`

```blade
<x-bf::select name="sel" :choices="['a' => 'A', 'b' => 'B', 'c' => 'C']" selected="b"/>
```
```html
<div id="sel-group" class="mb-3"><label for="sel" class="form-label">Sel</label><div><select id="sel" class="form-select" name="sel"><option value="a">A</option><option value="b" selected="selected">B</option><option value="c">C</option></select></div></div>
```

Select-specific settings: `choices`, `selected` (the `value` arg), `custom` (bool), `size`
(`'sm'`|`'lg'`), `option_attributes`, `optgroup_attributes`, plus `prepend`/`append` (input-group
addons). Anything else is an HTML attribute on `<select>` (`multiple`, `required`, `data-*`, ...).

- **`selected`** — a scalar or array; each matching option gets `selected="selected"`. For multiple
  selection add the `multiple` HTML attribute and pass an array.
- **`placeholder`** — passed as an attribute but **intercepted at render**: instead of being emitted
  on `<select>`, it becomes a leading blank, pre-selected option.

  ```blade
  <x-bf::select name="sel" :choices="['a' => 'A', 'b' => 'B', 'c' => 'C']" placeholder="Pick"/>
  ```
  ```html
  <div id="sel-group" class="mb-3"><label for="sel" class="form-label">Sel</label><div><select id="sel" class="form-select" name="sel"><option selected="selected" value="">Pick</option><option value="a">A</option><option value="b">B</option><option value="c">C</option></select></div></div>
  ```
- **`custom`** (Bootstrap 4 legacy) → in B4, `class="custom-select"` instead of `form-control`. No-op in
  Bootstrap 5 (the default), which always renders `form-select`. See [bootstrap5.md](bootstrap5.md).
- **`size`** → adds `form-control-lg` / `custom-select-lg` (B4) or `form-select-lg` (B5).
- Floating layout wraps `<select>` + label in `.form-floating` (B5 only); no placeholder is injected.

### The `choices` grammar (`ChoiceList`)

**Accepted input:** `choices` may be an `array`, a `Collection`, or **any `iterable`** (generator,
`ArrayIterator`, …) — pass `Model::pluck('name', 'id')` straight through. It is normalized to an array
once, at the boundary; a `Collection` is coerced via `toArray()`, so a Collection whose optgroup values
are themselves Collections is flattened in the same pass. The nested `options` of an advanced optgroup is
likewise any `iterable`.

Five entry forms, freely mixed. Parsing is **strict** — an ambiguous/incomplete descriptor throws
`InvalidArgumentException`.

| Form | Syntax | Renders |
|---|---|---|
| Simple option | `'a' => 'A'` | `<option value="a">A</option>` |
| Simple optgroup | `'G1' => ['a' => 'A', 'b' => 'B']` (string key, array value) | `<optgroup label="G1">…</optgroup>` |
| Advanced option | `['value' => 'b', 'label' => 'B', 'data-x' => 'y', 'disabled' => true]` (numeric key, ignored) | `<option value="b" data-x="y" disabled>B</option>` |
| Advanced optgroup | `['label' => 'Group', 'options' => [...], 'class' => 'grp']` (the `options` key discriminates; **root-only**) | `<optgroup label="Group" class="grp">…</optgroup>` |

Rules:
- An advanced option **must** define both `value` and `label`; the remaining keys are HTML attributes.
- An advanced optgroup **must** define `label` and an iterable `options`; other keys are `<optgroup>` attributes.
- Optgroups are **root-only** — nesting an optgroup inside an optgroup throws.
- A bare (numeric-keyed) array that is neither an advanced option nor optgroup throws.

Optgroup example:

```blade
<x-bf::select name="sel" :choices="['G1' => ['a' => 'A', 'b' => 'B'], 'G2' => ['c' => 'C']]"/>
```
```html
<div id="sel-group" class="mb-3"><label for="sel" class="form-label">Sel</label><div><select id="sel" class="form-select" name="sel"><optgroup label="G1"><option value="a">A</option><option value="b">B</option></optgroup><optgroup label="G2"><option value="c">C</option></optgroup></select></div></div>
```

### Blanket child attributes

`option_attributes` / `optgroup_attributes` apply to **every** option / optgroup; per-item (advanced-form)
attributes win over them.

```php
BF::select('sel', 'Sel', ['a' => 'A', 'b' => 'B', 'c' => 'C'], null, ['option_attributes' => ['class' => 'opt']]);
// <option value="a" class="opt">A</option><option value="b" class="opt">B</option>…
```

In x-components use the **`option:`** / **`optgroup:`** attribute prefixes (only `<x-bf::select>` and the
checkable collections recognize them):

```blade
<x-bf::select name="sel" :choices="['a' => 'A', 'b' => 'B']" option:class="opt"/>
```

---

## Single `checkbox` / `radio`

`BF::checkbox(name, label = null, value = 1, checked = null, options = [])`
`BF::radio(name, label = null, value = null, checked = null, options = [])`

```blade
<x-bf::checkbox name="accept" label="Accept"/>
```
```html
<div id="accept-group" class="mb-3"><div><div class="form-check"><input id="accept" class="form-check-input" name="accept" type="checkbox" value="1"><label for="accept" class="form-check-label">Accept</label></div></div></div>
```

Checkable-specific settings: `checked` (bool, the arg), `inline` (bool), `custom` (bool, B4),
`switch` (bool, checkbox only).

- **`switch`** → renders a switch (B5 default: `form-check form-switch` + `role="switch"`; B4 legacy:
  `custom-control custom-switch`, forcing `custom = true`). Ignored for radios.

  ```blade
  <x-bf::checkbox name="accept" label="Accept" switch/>
  ```
  ```html
  <div class="form-check form-switch"><input id="accept" class="form-check-input" role="switch" name="accept" type="checkbox" value="1"><label for="accept" class="form-check-label">Accept</label></div>
  ```
- **`inline`** → adds `form-check-inline` (or `custom-control-inline` in B4 when `custom`).
- **`custom`** (Bootstrap 4 legacy) → in B4, `custom-control custom-checkbox` / `custom-radio`. No-op in B5 (the default).
- **`value`** — the submitted value when checked (checkbox defaults to `1`, radio to `null`).
- **`label => false`** removes the label (a custom control keeps an empty `<label>` for its markup).
- **`help` / feedback placement** — the validation feedback goes **inside** the `.form-check` wrapper (a
  sibling of the input, which is what Bootstrap's `.is-invalid ~ .invalid-feedback` rule needs, so no
  `d-block`); the `help` text goes **after** the wrapper, full width, like every other field type. Each
  block is emitted exactly once. See [model-binding.md](model-binding.md).

  ```blade
  <x-bf::checkbox name="accept" label="Accept" help="Some help"/>
  ```
  ```html
  <div id="accept-group" class="mb-3"><div><div class="form-check"><input id="accept" class="form-check-input" aria-describedby="accept-help" name="accept" type="checkbox" value="1"><label for="accept" class="form-check-label">Accept</label></div><small id="accept-help" class="form-text">Some help</small></div></div>
  ```

---

## Checkable collections — `checkboxes` / `radios`

`BF::checkboxes(name, label = null, choices = [], checked = null, options = [])`
`BF::radios(name, label = null, choices = [], checked = null, options = [])`
→ `<x-bf::checkboxes name label :choices :checked/>` (same for `radios`)

```blade
<x-bf::radios name="gender" :choices="['m' => 'Male', 'f' => 'Female']" checked="f"/>
```
```html
<div id="gender-group" class="mb-3"><label for="gender" class="form-label">Gender</label><div><div class="form-check"><input id="gender-m" class="form-check-input" name="gender" type="radio" value="m"><label for="gender-m" class="form-check-label">Male</label></div><div class="form-check"><input id="gender-f" class="form-check-input" checked="checked" name="gender" type="radio" value="f"><label for="gender-f" class="form-check-label">Female</label></div></div></div>
```

- **`choices`** — the same grammar as `select`, **minus optgroups** (an `options` key or nested group
  throws). Advanced options work: `['value' => 'editor', 'label' => 'Editor', 'data-x' => 'y']`.
- **`checked`** — a scalar or array of checked values (matched by `in_array`), so `checkboxes` binds a
  multi-value field naturally.
- **Child `id`** — auto-generated as `{name}-{value}` (`roles-admin`); a per-item `id` in the advanced
  form overrides it.
- **`inline`** propagates to every child.
- **`option_attributes`** applies to every child (per-item attributes win). In x-components use the
  `option:` prefix.

  ```blade
  <x-bf::checkboxes name="roles" :choices="['admin' => 'Admin', 'editor' => 'Editor']" option:data-g="1"/>
  ```
  ```html
  <div class="form-check"><input id="roles-admin" data-g="1" class="form-check-input" name="roles" type="checkbox" value="admin"><label …>Admin</label></div>…
  ```
- **Validation feedback** is rendered **once at the collection level** (children carry
  `disable_errors`), and always as a block (`invalid-feedback d-block`). The collection has no single
  control, so no `aria-describedby`/`aria-invalid` is wired onto a child. See [model-binding.md](model-binding.md).
- **`required` mark** — when the collection carries the `required` attribute, the `required_mark`
  (default `' *'`) is appended to the **global** label only, never to the individual choice labels
  (the children are rendered with `required_mark => false`). A standalone `checkbox` / `radio` gets the
  mark on its own label. See [config.md](config.md).

  ```blade
  <x-bf::checkboxes name="roles" label="Roles" :choices="['admin' => 'Admin', 'editor' => 'Editor']" required/>
  ```
  ```html
  <div id="roles-group" class="mb-3"><label for="roles" class="form-label">Roles *</label><div><div class="form-check"><input required id="roles-admin" class="form-check-input" name="roles" type="checkbox" value="admin"><label for="roles-admin" class="form-check-label">Admin</label></div><div class="form-check"><input required id="roles-editor" class="form-check-input" name="roles" type="checkbox" value="editor"><label for="roles-editor" class="form-check-label">Editor</label></div></div></div>
  ```

- **`escape` propagates to the children** — the collection passes its settings down to the generated
  controls, so `escape => true` escapes the individual choice labels along with the global one. Nothing
  to declare per child. See [options-and-attributes.md](options-and-attributes.md).

- **Horizontal layout — global label alignment.** In the `horizontal` layout the collection's global
  label gets `pt-0` so it lines up with the first choice rather than with the column. It is
  driver-owned (an alignment requirement of that layout), so it survives a `label:class` of your own
  — unlike the column width, which you then take over. It never touches the individual choice labels
  (`form-check-label`). See [options-and-attributes.md](options-and-attributes.md).

  ```html
  <div id="tags-group" class="mb-3 row"><label for="tags" class="pt-0 col-form-label col-lg-2 col-xl-3">Tags</label><div class="col"><div class="form-check"><input id="tags-a" class="form-check-input" name="tags" type="checkbox" value="a"><label for="tags-a" class="form-check-label">A</label></div><div class="form-check"><input id="tags-b" class="form-check-input" name="tags" type="checkbox" value="b"><label for="tags-b" class="form-check-label">B</label></div></div></div>
  ```
