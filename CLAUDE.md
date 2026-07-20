# bgaze/bootstrap-form

Bootstrap 4/5 forms builder for Laravel 12+. Composer **package** (library, not an app): builds forms via a
`BF` facade and Blade directives, rendering HTML through its **own owned renderer** (no third-party form/HTML
dependency). Renders **Bootstrap 5 by default**; Bootstrap 4 is **fully supported for backward compatibility**
(`bootstrap_version` config, or per form/field). Public open-source (GitHub / Packagist).

> **v4 is released** (`v4.0.x` on Packagist). The v4 major dropped the historical `bgaze/laravel-collective-html`
> dependency in favor of an internal, iso-rendering HTML/form layer. There is **no `v4` git branch**: v4
> development lands directly on `master` (the `v1`/`v2`/`v3` branches are the older-major maintenance lines).
> **Release discipline**: tag a version **only after the GitHub Actions pipeline is green** (phpunit matrix +
> PHPStan + Pint); published tags are immutable — never force-move one, cut a new patch instead.

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

- `config/config.php`: `bootstrap_version` (4 | 5, **default 5**) selects the driver; layout-level, app-tunable
  options live under version sections `bootstrap4` / `bootstrap5`. Component classes are native/fixed (driver code),
  not configurable.
- Resolution: global default ← per-form override (`BF::open(['bootstrap_version' => 4])`) ← per-field override. A
  per-field override switches the driver (component classes); layout settings stay inherited from the form.
- **B4 is frozen** (compatibility only); **B5 is the default** and where new work happens. `custom` is a Bootstrap 4
  concept (native vs custom controls) and is a **no-op in Bootstrap 5** (styles unified); it stays a recognized
  setting in both versions so it is never emitted as an HTML attribute.
- Bootstrap 5 inline forms are **best-effort** (B5 reworked inline layout); vertical and horizontal are fully supported.
- **Tests / goldens** are split by concern: `tests/golden/` root = version-agnostic snapshots
  (`GoldenSnapshotCommonTest`), `tests/golden/b4/` = frozen B4 baseline (`GoldenSnapshotB4Test`),
  `tests/golden/b5/` = B5 default (`GoldenSnapshotB5Test`). B4-asserting suites pin the version via
  `Bootstrap4TestCase` — **never regenerate the B4 goldens**. `VersionOverrideTest` (neutral `TestCase`)
  asserts the default is B5.

## Documentation

The `docs/` directory is an **LLM-optimized usage guide** and the **single source of truth (SSOT)** for the
package's public behavior. It ships with the package (present in the consumer's `vendor/`) and is versioned with
the code. The public site (`https://packages.bgaze.fr/bootstrap-form`) is a **downstream render** of these files
(to be regenerated post-v4); until then, `docs/` is authoritative — the README is stale during v4.

- **Structure — hub + on-demand spokes** (progressive disclosure, mirroring the CG On-Demand-Resources pattern):
  - `docs/llm/index.md` — the **hub / socle commun**: two mandatory detection steps (resolved config + syntax in
    use), the universal field model (`name, label, value, options`; settings-vs-attributes partition; `id` policy),
    the three iso-rendering syntaxes, the full field catalog, the resolution cascade, and the load-on-demand index.
  - `docs/llm/<area>.md` — **spokes** loaded only when a task touches them (`choice-fields`, `layouts`,
    `input-groups`, `model-binding`, `options-and-attributes`, `components`, `bootstrap5`, `config`).
  - `llms.txt` (repo root, llmstxt.org format) — the **discovery breadcrumb**: points an LLM to the hub +
    spokes. It mirrors the hub's spoke index, so it is updated alongside it (below).
- **Style** — dense, exact, deterministic. Examples must be **byte-accurate** (lifted from / checked against
  `tests/golden/` — root = transverse, `b4/` = B4, `b5/` = B5 default). Default syntax in examples:
  **x-components** in Blade, the **`BF` facade** in PHP.
- **No-divergence law — docs travel with the code, in the SAME commit.** Any change to the public surface or
  rendered behavior updates the mapped doc file(s) in the same commit. Divergence between docs and code is a defect,
  same discipline as tests/goldens.
- **Mapping mechanism — self-maintaining `Sources:` headers.** Every doc file opens with an HTML-comment header
  listing the `src/` files (and goldens) it documents. To find which docs a change must touch:
  `grep -rl "src/Inputs/SelectInput.php" docs/llm/`. Keep the header current when a doc starts/stops covering a file.
- **What to update for common changes:** new/changed `BF` public method → hub catalog + the relevant spoke;
  new x-component or projection rule → `components.md` (+ hub catalog); new/changed setting or config key →
  `config.md` + hub §4 + any spoke that reads it; driver/markup delta → `bootstrap5.md` + affected spoke +
  goldens; adding or removing a spoke → the hub index (`docs/llm/index.md` §7) **and** root `llms.txt`. New
  public surface without a doc entry is an incomplete change.

## Pitfalls

- HTML rendering is **internalized** (owned `src/Support/` layer) — there is no `laravelcollective/html` or fork
  dependency. Composer requires the concrete `illuminate/*` components used at runtime (`support`, `database`,
  `routing`, `session`, `view`, `http`).
- **TDD-leaning; tests travel with the change.** Favor a test-driven approach: write/adjust the corresponding tests
  as part of the change and commit them in the same commit — unit tests for new logic, golden fixtures, explicit-string
  characterizations, x-component parity/guard. As a personal project, stay pragmatic about ceremony, but tests are the
  non-negotiable safety net: not every case can be exercised by hand, so the suite is what guarantees a change does not
  silently break the rest.
- The `tests/` suite is a **characterization oracle**: it asserts the exact rendered HTML, including a
  **golden snapshot** captured as the iso reference — organized as `tests/golden/` (root = version-agnostic),
  `tests/golden/b4/` (frozen B4 baseline) and `tests/golden/b5/` (B5 default). Any intended markup change must
  update the expected strings / goldens in the same commit; an unintended diff there is a regression. Regenerate
  goldens deliberately with `UPDATE_GOLDEN=1 vendor/bin/phpunit` (never for the frozen B4 baseline).
- **Docs travel with the change.** `docs/` is the SSOT for public behavior/usage; any change to the public surface
  updates the mapped doc file(s) **in the same commit** (find them via the `Sources:` headers — see § Documentation).
  Docs/code divergence is a defect, same as a stale golden.
- GitHub/Packagist package: no application, no staging / `.env`. CI runs the suite via GitHub Actions
  (`.github/workflows/tests.yml`, matrix PHP × Testbench). Contribute via PRs on GitHub.

## On-Demand Resources

| Resource               | Path / URL                                                     | When                                     |
|------------------------|----------------------------------------------------------------|------------------------------------------|
| Usage docs (SSOT)      | `docs/llm/index.md` (hub) + `docs/llm/*.md` (spokes)           | Building forms / public API — start here |
| Full package docs site | https://packages.bgaze.fr/bootstrap-form                       | Public render (stale during v4)          |
| PhpStorm setup gist    | https://gist.github.com/bgaze/1f559782c85511dc2671cdb6b453f0c6  | Blade directive highlighting             |
