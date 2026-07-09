# bgaze/bootstrap-form

Bootstrap 4/5 forms builder for Laravel 6+. Composer **package** (library, not an app): wraps the
`bgaze/laravel-collective-html` fork to build forms via a `BF` facade and Blade directives. Renders
**Bootstrap 4 by default**; Bootstrap 5 is **opt-in** (`bootstrap_version` config, or per form/field).
Public open-source (GitHub / Packagist).

## Stack

| Item      | Value                                                       |
|-----------|-------------------------------------------------------------|
| Language  | PHP ≥ 7.2.5                                                  |
| Framework | Laravel 6+ (illuminate/support, illuminate/database ≥ 6.0)  |
| Autoload  | PSR-4 `Bgaze\BootstrapForm\` → `src/`                        |
| Tests     | PHPUnit 11 + Orchestra Testbench — byte-exact HTML characterization suite in `tests/` (`vendor/bin/phpunit`) |

## Architecture

- `src/BootstrapFormServiceProvider.php` — registers the `BF` facade + Blade directives; publishes `src/config/config.php`.
- `src/BootstrapForm.php` — builder entry point, backing the `BF` facade (`Bgaze\BootstrapForm\Support\Facades\BF`).
- `src/Inputs/` — field types (Text, Check, CheckChoice, File, Range, Select).
- `src/Support/` — `Input`, `Attributes`, traits `HasAddons` / `HasSettings`.
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

- HTML rendering depends on the **fork** `bgaze/laravel-collective-html` (not `laravelcollective/html`), pinned for
  Laravel 11+ compatibility. Keep it in sync when bumping the supported Laravel range.
- The `tests/` suite is a **characterization oracle**: it asserts the exact rendered HTML. Any intended markup change
  must update the expected strings in the same commit; an unintended diff there is a regression.
- GitHub/Packagist package: no application, no staging / `.env`. CI runs the suite via GitHub Actions
  (`.github/workflows/tests.yml`, matrix PHP × Testbench). Contribute via PRs on GitHub.

## On-Demand Resources

| Resource            | Path / URL                                                     | When                         |
|---------------------|----------------------------------------------------------------|------------------------------|
| Full package docs   | https://packages.bgaze.fr/bootstrap-form                       | Usage / API reference        |
| PhpStorm setup gist | https://gist.github.com/bgaze/1f559782c85511dc2671cdb6b453f0c6  | Blade directive highlighting |
