<!--
Sources: src/BootstrapForm.php (vertical/horizontal/inline/floating/open, buttonOption),
         src/Support/Input.php (group/leftGroupColumn/rightGroupColumn),
         src/Inputs/CheckInput.php (leftGroupColumn), src/Support/Drivers/*.php (formLayoutClass,
         labelClass, colFormLabelClass, floatingGroup, supportsFloating), src/config/config.php
Goldens: tests/golden/b5/layout.*.html, tests/golden/b5/text.html, tests/golden/b5/float.*.html (default),
         tests/golden/b4/layout.*.html, tests/golden/b4/float.degrades.html (B4)
Keep in sync in the SAME commit as any change to the files above (see CLAUDE.md § Documentation).
-->

# Form layouts — vertical, horizontal, inline, floating

The layout is a form-level setting (inherited by fields). Set it via the config `layout` key, an
`open` option, a shortcut method, or an `<x-bf::form>` attribute:

```blade
<x-bf::form url="/x" horizontal> … </x-bf::form>   {{-- boolean shortcut --}}
<x-bf::form url="/x" layout="inline"> … </x-bf::form>
```
```php
BF::horizontal(['url' => '/x']);   // === BF::open(['url' => '/x', 'layout' => 'horizontal'])
```

Column widths and spacing are **app-tunable** per Bootstrap version (config `bootstrap4` / `bootstrap5`
sections — see [config.md](config.md)); component classes are fixed.

---

## Vertical (default)

Label above the control. The form carries no layout class; each field is a form group
(`mb-3` in B5, the default; `form-group` in B4) with the control wrapped in a bare `<div>`.

```html
<div id="field-group" class="mb-3"><label for="field" class="form-label">Field</label><div><input id="field" class="form-control" name="field" type="text"></div></div>
```

---

## Horizontal

Label and control on one grid row. The form gets no layout class (B5 default — the grid does the work;
B4 legacy adds `form-horizontal`). Each group gets `mb-3 row` (B5) / `form-group row` (B4); the label
gets `col-form-label` + `left_class`; the control column gets `col`.

```html
<div id="login-group" class="mb-3 row"><label for="login" class="col-form-label col-lg-2 col-xl-3">Login</label><div class="col"><input id="login" class="form-control" name="login" type="text"></div></div>
```

- **`left_class`** / **`right_class`** — the label / control column widths (config defaults
  `col-lg-2 col-xl-3` / `col-lg-10 col-xl-9`).
- **`pull_right`** — for label-less fields (checkboxes, radios, `label => false`), render an empty
  left column so the control stays aligned. A checkbox in horizontal layout:

  ```html
  <div id="accept-group" class="mb-3 row"><div class="col-lg-2 col-xl-3"></div><div class="col"><div class="form-check">…</div></div></div>
  ```
  Set `pull_right => false` to drop the spacer.

---

## Inline

All groups flow on one line. The form gets no layout class (B5 default, best-effort; B4 legacy adds
`form-inline`). Groups get the form-group class + the horizontal/vertical spacing utilities; labels get
the label class + the label spacing:

```html
<div id="login-group" class="mb-3 me-3 my-1"><label for="login" class="form-label me-2">Login</label><div><input id="login" class="form-control" name="login" type="text"></div></div>
```

- **`lspace`** — space between a label and its field (on the label). B5 `me-2`, B4 `mr-2`.
- **`hspace`** — horizontal space between groups. B5 `me-3`, B4 `mr-3`.
- **`vspace`** — vertical space between groups. `my-1`. Set any to `false` to disable.

> Bootstrap 5 reworked inline forms; this layout is **best-effort** there and may need extra markup on
> your side. Vertical and horizontal are fully supported in both versions.

---

## Floating (Bootstrap 5)

Floating labels: the control comes first, the label after, wrapped in `.form-floating`. Text-like
controls get an injected `placeholder=" "` (required by the CSS); `select` does not.

```blade
<x-bf::form url="/x" floating>
    <x-bf::text name="email" label="Email address"/>
</x-bf::form>
```
```html
<div id="email-group" class="mb-3"><div><div class="form-floating"><input id="email" class="form-control" placeholder=" " name="email" type="text"><label for="email">Email address</label></div></div></div>
```

- **Floatable fields:** text-like inputs, `textarea`, `select`. Non-floatable fields (checkable,
  file, range, hidden) render normally within a floating form.
- **Bootstrap 4 has no floating layout** — it **degrades to vertical**:

  ```html
  <div id="email-group" class="form-group"><label for="email">Email address</label><div><input id="email" class="form-control" name="email" type="text"></div></div>
  ```
- Addons still work — the `.form-floating` block nests inside `.input-group` (see [input-groups.md](input-groups.md)).
