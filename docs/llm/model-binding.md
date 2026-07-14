<!--
Sources: src/BootstrapForm.php (initForm/initModelForm, open, RESERVED), src/Support/FormElements.php
         (open/model/token/getAction/getAppendage), src/Support/FieldValue.php, src/Support/FormContext.php,
         src/Support/Input.php (getErrors/setValidState/setAriaAttributes/validFeedback)
Goldens: tests/golden/model.*.html, tests/golden/old.*.html, tests/golden/error.*.html,
         tests/golden/valid.*.html
Keep in sync in the SAME commit as any change to the files above (see CLAUDE.md § Documentation).
-->

# Form opening, model binding, old input & validation

How a form is opened, how field values are resolved, and how errors / valid feedback render.

---

## Opening a form

`BF::open(options)` renders the `<form>` tag. Pick the action with **one** of `url`, `route`,
`action` (mutually exclusive; `route`/`action` accept a name/`Controller@method` string or a
`[name, ...params]` array):

```php
BF::open(['url' => '/foo']);
BF::open(['route' => 'users.store']);
BF::open(['route' => ['users.update', $user]]);
```
```html
<form method="POST" action="http://localhost/foo" accept-charset="UTF-8" role="form">…
```

- **Method** defaults to `POST`; `method => 'get'` renders GET. `PUT`/`PATCH`/`DELETE` are spoofed via
  a hidden `_method` field.
- A **CSRF token** hidden field is appended automatically for every non-GET form.
- **`files => true`** adds `enctype="multipart/form-data"`.
- Any other option is a form setting (inherited by fields — see hub §4) or an HTML attribute on
  `<form>` (`novalidate`, `id`, `class`, `data-*`, …). `role="form"` is always present.

---

## Model binding

Bind an Eloquent model with `model`, and choose the create/edit endpoint with `store` / `update`:

```blade
<x-bf::form :model="$user" store="users.store"> … </x-bf::form>   {{-- new record --}}
<x-bf::form :model="$user" update="users.update"> … </x-bf::form> {{-- existing record --}}
```

- If the model **exists** and `update` is set → method `PUT`, and the model's route key is appended to
  the route/action automatically.
- If the model **does not exist** and `store` is set → method `POST`.
- `store` / `update` accept the same name / `Controller@method` / array forms as `route` / `action`.
- `model`, `url`, `route`, `action`, `store`, `update` are **reserved** — consumed by the form, never
  inherited by fields.

Bound field values are then read from the model automatically:

```html
<!-- <x-bf::text name="login"/> with a bound model whose login = "jdoe" -->
<div id="login-group" class="form-group"><label for="login">Login</label><div><input id="login" class="form-control" name="login" type="text" value="jdoe"></div></div>
```

**Value resolution order** (per field): old input → *(empty-string rule, below)* → explicit `value`
argument → bound model attribute → `null`. Names are transformed for lookup (`user[email]` →
`user.email`). If the model defines `getFormValue($key)`, it is used instead of `data_get`.

> **Empty-string rule:** when the app runs the `ConvertEmptyStringsToNull` middleware (Laravel
> default), a failed submit repopulates an untouched field as empty rather than from the model — so a
> cleared field stays cleared.

---

## Old input repopulation

After a failed validation redirect, fields repopulate from the flashed old input (taking precedence
over the model), including `select` selection and `checkbox`/`radio` checked state:

```html
<!-- old input login = "old" -->
<input id="login" class="form-control" name="login" type="text" value="old">
<!-- old input accept present -->
<input id="accept" class="form-check-input" checked="checked" name="accept" type="checkbox" value="1">
```

Checkables that never resolve a value fall back to the `checked` argument.

---

## Error display

Fields read the session `errors` bag named by the **`error_bag`** setting (default `'default'`). A
field with an error gets `is-invalid` on the control **and** its group, an `aria-invalid="true"`, and
an `invalid-feedback` message wired via `aria-describedby`:

```html
<div id="login-group" class="is-invalid form-group"><label for="login">Login</label><div><input id="login" class="form-control is-invalid" aria-describedby="login-error" aria-invalid="true" name="login" type="text"><div class="invalid-feedback" id="login-error">The login field is required.</div></div></div>
```

- **`show_all_errors => true`** renders all messages for the field instead of only the first.
- **`error_bag => 'name'`** targets a named bag (e.g. multiple forms on a page).
- **Choice collections** render one `invalid-feedback d-block` at the collection level (not per child).

---

## Valid feedback (opt-in)

With **`show_valid_feedback => true`**, after a submit that produced an error bag, fields that carry no
error of their own are marked valid (`is-valid`, `aria-invalid="false"`). A per-field **`success`**
message renders a `valid-feedback`:

```html
<!-- show_valid_feedback + success => 'Looks good!' -->
<input id="login" class="form-control is-valid" aria-describedby="login-valid" aria-invalid="false" name="login" type="text"><div class="valid-feedback" id="login-valid">Looks good!</div>
```

Valid and invalid states are mutually exclusive (a field is only marked valid when it has no error).
