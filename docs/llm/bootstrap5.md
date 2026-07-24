<!--
Sources: src/Support/Drivers/VersionDriver.php, src/Support/Drivers/Bootstrap4Driver.php,
         src/Support/Drivers/Bootstrap5Driver.php, src/Support/Drivers/DriverManager.php,
         src/BootstrapForm.php (resetForm/initForm version resolution, driver()), src/config/config.php
Goldens: tests/golden/b5/*.html
Keep in sync in the SAME commit as any change to the files above (see CLAUDE.md § Documentation).
-->

# Bootstrap 4 ↔ 5

**Bootstrap 5 is the default.** Bootstrap 4 is frozen but fully supported for backward compatibility —
opt into it globally, per form or per field. A **version driver** owns the class vocabulary and the
structural deltas — no Bootstrap class literal exists outside a driver.

---

## Selecting the version

The default is Bootstrap 5. To render Bootstrap 4, opt in at one of three levels, each overriding the
previous (**cascade**):

1. **Global** — config `bootstrap_version` (`4` | `5`, default `5`).
2. **Per form** — `BF::open(['bootstrap_version' => 4])` / `<x-bf::form bootstrap-version="4">`.
3. **Per field** — `['bootstrap_version' => 4]` in a field's options.

A per-field override switches the **driver** (component classes) for that field; layout settings
(`left_class`, spacing, …) stay inherited from the form.

```php
config(['bootstrap_form.bootstrap_version' => 4]); // app-wide (opt into legacy B4)
BF::open(['url' => '/x', 'bootstrap_version' => 4]); // this form
BF::text('login', null, null, ['bootstrap_version' => 4]); // this field
```

---

## What changes (B4 → B5)

| Area | Bootstrap 4 | Bootstrap 5 |
|---|---|---|
| Form group | `form-group` | `mb-3` |
| Label | *(no class)* | `form-label` |
| Select | `form-control` (or `custom-select`) | `form-select` |
| Range | `form-control-range` (or `custom-range`) | `form-range` |
| Checkbox/radio | `form-check` / `custom-control custom-{checkbox,radio}` | `form-check` |
| Switch | `custom-control custom-switch` | `form-check form-switch` + `role="switch"` |
| Input group addons | each nested in an `.input-group-prepend` / `.input-group-append` div | addons are direct children (no wrappers) |
| File | native or `custom-file` markup | `form-control` |
| `custom` option | native vs custom controls | **no-op** (styles unified) |
| Floating labels | not supported → degrades to vertical | supported (`.form-floating`) |
| Horizontal layout class | `form-horizontal` on `<form>` | none (grid classes carry it) |
| Inline layout | `form-inline` | best-effort (B5 reworked inline) |
| Inline spacing suffix | `mr-*` / `ml-*` | `me-*` / `ms-*` |

Representative Bootstrap 5 output:

```html
<!-- text -->   <div id="login-group" class="mb-3"><label for="login" class="form-label">Login</label><div><input id="login" class="form-control" name="login" type="text"></div></div>
<!-- select --> <select id="sel" class="form-select" name="sel">…</select>
<!-- range -->  <input id="vol" class="form-range" name="vol" type="range">
<!-- switch --> <div class="form-check form-switch"><input id="accept" class="form-check-input" role="switch" name="accept" type="checkbox" value="1"><label …>Accept</label></div>
<!-- file -->   <input id="doc" class="form-control" name="doc" type="file">
```

---

## Notes

- **`custom` is a recognized no-op in B5** — it stays a known setting (never emitted as an HTML
  attribute) but changes nothing. See [options-and-attributes.md](options-and-attributes.md).
- **Floating** is B5-only; on B4 the `floating` layout degrades to vertical. See [layouts.md](layouts.md).
- **Inline** forms are best-effort on B5 and may need extra markup; vertical and horizontal are fully
  supported on both versions.
- Component classes are fixed driver code — only the layout-level options in the `bootstrap4` /
  `bootstrap5` config sections are tunable. See [config.md](config.md).
