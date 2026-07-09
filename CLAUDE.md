# bgaze/bootstrap-form

Bootstrap 4 forms builder for Laravel 6+. Composer **package** (library, not an app): wraps the
`bgaze/laravel-collective-html` fork to build Bootstrap 4 forms via a `BF` facade and Blade directives.
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
