# bgaze/bootstrap-form

Bootstrap 4/5 forms builder for Laravel 12+. Composer **package** (library, not an app): builds forms via a
`BF` facade and Blade directives, rendering HTML through its **own owned renderer** (no third-party form/HTML
dependency). Renders **Bootstrap 4 by default**; Bootstrap 5 is **opt-in** (`bootstrap_version` config, or per
form/field). Public open-source (GitHub / Packagist).

> Branch `v4` (work in progress): the historical `bgaze/laravel-collective-html` dependency has been removed in
> favor of an internal, iso-rendering HTML/form layer. Targets a **major version bump** (also hosting upcoming
> features); not tagged/released until validated.

## Stack

| Item      | Value                                                       |
|-----------|-------------------------------------------------------------|
| Language  | PHP ≥ 8.2 (`declare(strict_types=1)`, native types)          |
| Framework | Laravel 12 (illuminate/* `^12.0`)                            |
| Autoload  | PSR-4 `Bgaze\BootstrapForm\` → `src/`                        |
| Tests     | PHPUnit 11 + Orchestra Testbench — byte-exact HTML characterization suite in `tests/` (`vendor/bin/phpunit`) |

## Architecture

- `src/BootstrapFormServiceProvider.php` — registers the `BF` facade + Blade directives; publishes `src/config/config.php`.
- `src/BootstrapForm.php` — builder entry point, backing the `BF` facade (`Bgaze\BootstrapForm\Support\Facades\BF`).
  Exposes the owned units via `html()` / `elements()` / `fieldValue()` / `context()` (the historical
  `htmlBuilder()` / `formBuilder()` accessors are gone).
- `src/Inputs/` — field types (Text, Check, CheckChoice, File, Range, Select).
- `src/Support/` — **owned HTML/form layer** (successor of the collective-html dependency):
  - `Html` — stateless attribute/tag serialization primitive (SSOT of attribute order & escaping).
  - `FieldValue` — value binding resolver (old input, model, checked/selected state).
  - `FormContext` — per-form binding state (bound model, CSRF token, url/view/session services).
  - `FormElements` — element & form-open renderer, composing `Html` + `FieldValue` + `FormContext`.
  - `Options` — SSOT partitioning raw options into settings vs HTML attributes (+ the `~` literal escape).
  - `Attributes` — ordered attribute value object; `~` (`LITERAL_PREFIX`) emits an HTML attribute whose name
    collides with a setting. Plus `Input` and traits `HasAddons` / `HasSettings`.
- `src/Support/Drivers/` — **version drivers**: `VersionDriver` (abstract, shared tokens) + `Bootstrap4Driver` /
  `Bootstrap5Driver` (version deltas) + `DriverManager` (resolves by version). All Bootstrap component classes and
  the structural divergences (input-group, custom-file, check/switch) live here — **no Bootstrap class literal exists
  outside a driver**. `Input` subclasses consume the driver.

## Bootstrap version

- `config/config.php`: `bootstrap_version` (4 | 5, default 4) selects the driver; layout-level, app-tunable options
  live under version sections `bootstrap4` / `bootstrap5`. Component classes are native/fixed (driver code), not
  configurable.
- Resolution: global default ← per-form override (`BF::open(['bootstrap_version' => 5])`) ← per-field override. A
  per-field override switches the driver (component classes); layout settings stay inherited from the form.
- `custom` is a Bootstrap 4 concept (native vs custom controls) and is a **no-op in Bootstrap 5** (styles unified).
  It stays a recognized setting in both versions so it is never emitted as an HTML attribute.
- Bootstrap 5 inline forms are **best-effort** (B5 reworked inline layout); vertical and horizontal are fully supported.

## Pitfalls

- HTML rendering is **internalized** (owned `src/Support/` layer) — there is no `laravelcollective/html` or fork
  dependency. Composer requires the concrete `illuminate/*` components used at runtime (`support`, `database`,
  `routing`, `session`, `view`, `http`).
- The `tests/` suite is a **characterization oracle**: it asserts the exact rendered HTML, including a 53-fixture
  **golden snapshot** (`tests/golden/*.html`) captured as the iso reference. Any intended markup change must update the
  expected strings / goldens in the same commit; an unintended diff there is a regression. Regenerate goldens
  deliberately with `UPDATE_GOLDEN=1 vendor/bin/phpunit`.
- GitHub/Packagist package: no application, no staging / `.env`. CI runs the suite via GitHub Actions
  (`.github/workflows/tests.yml`, matrix PHP × Testbench). Contribute via PRs on GitHub.

## On-Demand Resources

| Resource            | Path / URL                                                     | When                         |
|---------------------|----------------------------------------------------------------|------------------------------|
| Full package docs   | https://packages.bgaze.fr/bootstrap-form                       | Usage / API reference        |
| PhpStorm setup gist | https://gist.github.com/bgaze/1f559782c85511dc2671cdb6b453f0c6  | Blade directive highlighting |
