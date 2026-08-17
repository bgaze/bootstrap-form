# bgaze/bootstrap-form

Bootstrap 4/5 forms builder for Laravel 12+. Composer **package** (library, not an app): builds forms via a
`BF` facade, Blade directives and Blade x-components, rendering HTML through its **own owned renderer** (no
third-party form/HTML dependency). Renders **Bootstrap 5 by default**; Bootstrap 4 is **fully supported for
backward compatibility** (`bootstrap_version` config, or per form/field). Public open-source (GitHub /
Packagist).

> **This file is public.** It lives in a public repository and ships inside every consumer's `vendor/`
> directory. Keep it self-contained: repo-relative paths and public URLs only — no absolute or machine-local
> paths, no personal tooling, no internal process references. Anything a reader outside this repository
> cannot reach does not belong here.

## Stack

| Item      | Value                                                                                                       |
|-----------|-------------------------------------------------------------------------------------------------------------|
| Language  | PHP ≥ 8.2 (`declare(strict_types=1)`, native types)                                                          |
| Framework | Laravel 12 or 13 (illuminate/* `^12.0 \|\| ^13.0`)                                                           |
| Autoload  | PSR-4 `Bgaze\BootstrapForm\` → `src/`                                                                        |
| Tests     | PHPUnit 11 + Orchestra Testbench (`^10.0 \|\| ^11.0`) — byte-exact HTML characterization suite in `tests/`    |
| Quality   | PHPStan level 5 + Larastan (`phpstan.neon`, `src` only) · Pint, `laravel` preset                              |

## Versioning & releases

- `master` carries the **current major** (v4). The `v1` / `v2` / `v3` branches are the older-major maintenance
  lines; there is no `v4` branch — v4 work lands on `master`.
- **Tag only after the GitHub Actions pipeline is green** (phpunit matrix + PHPStan + Pint).
- **Published tags are immutable** — never force-move one; cut a new patch instead.
- **Every tag gets a matching GitHub Release** with human-readable notes (`gh release create`), the newest
  marked `--latest`. Packagist consumes the tag; the Release is the public changelog and the watcher
  notification.
- v4 dropped the historical `laravelcollective/html` dependency in favor of the internal, iso-rendering
  HTML/form layer described below.

## Architecture

- `src/BootstrapFormServiceProvider.php` — registers the `BF` facade, the Blade directives and the `bf`
  x-component namespace; publishes `src/config/config.php`.
- `src/BootstrapForm.php` — builder entry point, backing the `BF` facade (`Bgaze\BootstrapForm\Support\Facades\BF`).
  Exposes the owned units via `html()` / `elements()` / `fieldValue()` / `context()`.
- `src/Inputs/` — field types (Text, Check, CheckChoice, File, Range, Select).
- `src/Support/` — **owned HTML/form layer** (successor of the collective-html dependency):
  - `Html` — stateless attribute/tag serialization primitive (SSOT of attribute order & escaping).
  - `FieldValue` — value binding resolver (old input, model, checked/selected state).
  - `FormContext` — per-form binding state (bound model, CSRF token, url/view/session services).
  - `FormElements` — element & form-open renderer, composing `Html` + `FieldValue` + `FormContext`.
  - `Options` — SSOT partitioning raw options into settings vs HTML attributes (+ the `~` literal escape).
  - `Attributes` — ordered attribute value object; `~` (`LITERAL_PREFIX`) emits an HTML attribute whose name
    collides with a setting. Plus `Input` and traits `HasAddons` / `HasSettings`.
- `src/View/Components/` — the x-components. They **delegate to the facade**, so their output is byte-identical
  to it; the projection rules (`label:`, `group:`, `input:`, `option:`, `optgroup:`, boolean shortcuts) live in
  `Concerns/`.
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

## Working on this package

**The loop — characterization first.** Write or adjust the expected string, then the code. The suite asserts the
*exact* rendered HTML, so a markup decision that is not expressed as an expectation is not specified: it is a
coincidence that the next refactor is free to break. This is a small personal package — stay pragmatic about
ceremony — but the suite is the non-negotiable safety net, because not every case can be exercised by hand.

**Definition of done.** One change = **code + tests/goldens + the mapped `docs/llm/` files, in the same
commit.** A public-surface change without its doc entry, or a markup change without its updated expectation, is
an *incomplete* change rather than a smaller one. Which tests and which docs:

- new logic → a unit test; new/changed markup → the golden **and** the explicit-string characterizations that
  cover it; a new x-component or projection rule → its parity/guard test in `tests/Component*Test.php`.
- which doc files a change must touch → see § Documentation (the `Sources:` headers are the map).

**Commands.**

| Purpose | Command |
|---|---|
| Run the suite | `vendor/bin/phpunit` |
| Narrow it while iterating | `vendor/bin/phpunit --filter <pattern>` |
| Regenerate goldens (deliberate act) | `UPDATE_GOLDEN=1 vendor/bin/phpunit` — **never** for `tests/golden/b4/` |
| Code style | `vendor/bin/pint` (`vendor/bin/pint --test` to check only) |
| Static analysis | `vendor/bin/phpstan analyse` |

**CI runs those same three gates** (`.github/workflows/tests.yml`): phpunit across PHP 8.2–8.4 × Testbench
10/11, plus `pint --test` and `phpstan analyse`. It fires on every branch and every pull request, so a red gate
locally is a red pipeline — and a red pipeline blocks a tag (§ Versioning & releases).

**Where code goes.** A Bootstrap class literal only ever lives in a driver (§ Bootstrap version). A
rendered-markup decision only ever lives in `src/Support/` (§ Architecture). An `Input` subclass composes both;
it does not restate either.

**Contributions** are welcome as pull requests on GitHub, alongside issues and feedback.

## Documentation

`docs/llm/` is an **LLM-optimized usage guide** and the **single source of truth (SSOT)** for the package's
public behavior. It ships with the package (present in the consumer's `vendor/`) and is versioned with the code,
so it changes in the **same commit** as the behavior it documents.

The public site (`https://packages.bgaze.fr/bootstrap-form`) is the **human-facing documentation**, maintained
separately and downstream — it is never edited from this repository. When a change here implies a site update,
**report it** instead of letting the two drift silently. Earlier majors are archived at
`https://packages.bgaze.fr/bootstrap-form/v3`.

- **Structure — hub + on-demand spokes** (progressive disclosure: read the hub, load a spoke only when the task
  touches its area):
  - `docs/llm/index.md` — the **hub**: two mandatory detection steps (resolved config + syntax in use), the
    universal field model (`name, label, value, options`; settings-vs-attributes partition; `id` policy), the
    three iso-rendering syntaxes, the full field catalog, the resolution cascade, and the load-on-demand index.
  - `docs/llm/<area>.md` — **spokes** (`choice-fields`, `layouts`, `input-groups`, `model-binding`,
    `options-and-attributes`, `components`, `bootstrap5`, `config`).
  - `llms.txt` (repo root, llmstxt.org format) — the **discovery breadcrumb**: points an LLM to the hub +
    spokes. It mirrors the hub's spoke index, so it is updated alongside it (below).
- **Style** — dense, exact, deterministic. Examples must be **byte-accurate** (lifted from / checked against
  `tests/golden/` — root = transverse, `b4/` = B4, `b5/` = B5 default). Default syntax in examples:
  **x-components** in Blade, the **`BF` facade** in PHP.
- **No-divergence law — docs travel with the code, in the SAME commit.** Any change to the public surface or
  rendered behavior updates the mapped doc file(s) in the same commit. Divergence between docs and code is a
  defect, same discipline as tests/goldens.
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
- The `tests/` suite is a **characterization oracle**: it asserts the exact rendered HTML, including a
  **golden snapshot** captured as the iso reference (three directories — see § Bootstrap version). Any intended
  markup change must update the expected strings / goldens in the same commit; an **unintended diff there is a
  regression**, not a fixture to refresh.
- **There is no application to click.** No staging, no `.env`, nothing to open in a browser: the suite is the
  only oracle a change has, which is why it is written first.
- `mergeConfigFrom()` merges only the **top level** of a config file, so a published `config/bootstrap_form.php`
  replaces a whole `bootstrap4` / `bootstrap5` section. The package defaults act as a **floor** under each
  section — keep that behavior in mind when adding a version-section key.

## On-Demand Resources

| Resource               | Path / URL                                                     | When                                       |
|------------------------|----------------------------------------------------------------|--------------------------------------------|
| Usage docs (SSOT)      | `docs/llm/index.md` (hub) + `docs/llm/*.md` (spokes)           | Building forms / public API — start here   |
| Human documentation    | https://packages.bgaze.fr/bootstrap-form                       | The public site for the current major      |
| Archived docs (v2/v3)  | https://packages.bgaze.fr/bootstrap-form/v3                    | Questions about an older major             |
| PhpStorm setup gist    | https://gist.github.com/bgaze/1f559782c85511dc2671cdb6b453f0c6  | Blade directive highlighting               |
