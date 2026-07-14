# Bootstrap 4 & 5 forms builder for Laravel 12+ <!-- omit in toc -->

[![GitHub license](https://img.shields.io/github/license/bgaze/bootstrap-form)](https://github.com/bgaze/bootstrap-form/blob/master/LICENSE)
![Maintenance](https://img.shields.io/maintenance/yes/2030)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/bgaze/bootstrap-form)
[![GitHub Repo stars](https://img.shields.io/github/stars/bgaze/bootstrap-form?style=flat)](https://github.com/bgaze/bootstrap-form/stargazers)
![Packagist](https://img.shields.io/packagist/dt/bgaze/bootstrap-form)

<p style="text-align:center">
    <img src="./intro.png" alt="Bootstrap forms builder for Laravel">
</p>

This package simplifies Bootstrap forms creation in Laravel applications, rendering all markup through its own
lightweight HTML/form layer (**no third-party form/HTML dependency**).

It renders **Bootstrap 5** markup by default and **fully supports Bootstrap 4** for backward compatibility
(switchable globally, per form, or per field). Model form binding and automatic error display are supported, as
well as most Bootstrap form features: form layouts, custom fields, input groups, and more.

Any contribution or feedback is highly welcomed, please feel free to create a pull request
or [submit a new issue](https://github.com/bgaze/bootstrap-form/issues/new).

## ℹ️ v4 status — functional, documentation in progress

**v4 is functional and tested**, but its full documentation is **still being written**. In the meantime, the
[**LLM usage guide**](docs/llm/index.md) shipped in this repository is the **authoritative, up-to-date reference**
(dense but exact — usable by humans and AI assistants alike). The full documentation site
([packages.bgaze.fr/bootstrap-form](https://packages.bgaze.fr/bootstrap-form)) still reflects **v3** and will be
regenerated for v4.

## ⚠️ Upgrading — v4 has breaking changes

v4 introduces breaking changes over v3 (see [What's new](#whats-new-in-v4) below). New installs get v4 by default.
**To keep using a previous major version, require it explicitly** and refer to its dedicated branch:

| Version | Install | Docs |
|---------|---------|------|
| **v3** (Bootstrap 4 default, B5 opt-in) | `composer require "bgaze/bootstrap-form:^3.0"` | [`v3` branch](https://github.com/bgaze/bootstrap-form/tree/v3) · [site](https://packages.bgaze.fr/bootstrap-form) |
| **v2** (Bootstrap 4 only) | `composer require "bgaze/bootstrap-form:^2.0"` | [`v2` branch](https://github.com/bgaze/bootstrap-form/tree/v2) · [site](https://packages.bgaze.fr/bootstrap-form) |

## Requirements

- PHP **8.2+**
- Laravel **12** or **13**

## Quick start

Install the package using Composer:

```shell
composer require bgaze/bootstrap-form
```

Several configuration options are available; publish the configuration file to customize them:

```shell
php artisan vendor:publish --provider="Bgaze\BootstrapForm\BootstrapFormServiceProvider"
```

Forms can be built through **three interchangeable syntaxes that produce byte-identical HTML**:

```blade
{{-- x-components — the Blade default --}}
<x-bf::form url="/x">
    <x-bf::text name="field"/>
    <x-bf::submit>Save</x-bf::submit>
</x-bf::form>
```

```blade
{{-- Blade directives --}}
@open(['url' => '/x'])
@text('field')
@submit('Save')
@close
```

```php
// BF facade — PHP context
echo BF::open(['url' => '/x']);
echo BF::text('field');
echo BF::submit('Save');
echo BF::close();
```

If you use **PhpStorm**, [this gist](https://gist.github.com/bgaze/1f559782c85511dc2671cdb6b453f0c6) helps you
configure **syntax highlighting** and **live templates** for the package's Blade directives.

## What's new in v4

- **No more third-party dependency.** The historical `laravelcollective/html` dependency is gone, replaced by an
  internal, iso-rendering HTML/form layer owned by the package.
- **Bootstrap 5 is now the default.** Bootstrap 4 remains **fully supported** for backward compatibility (frozen),
  switchable via `bootstrap_version` globally, per form (`BF::open(['bootstrap_version' => 4])`), or per field.
- **Laravel 12 & 13**, PHP **8.2+** (older versions dropped).
- **x-components** — a new Blade component syntax (`<x-bf::text .../>`) alongside the directives and the `BF`
  facade; all three render identical markup.
- **Richer choice grammar** for `select` / `checkboxes` / `radios` (optgroups, per-option attributes) accepting
  any `iterable` (array, Collection, generator).
- **New field types & layouts** — `datetime-local`, `month`, `week`, `search` inputs, Bootstrap 5 **floating
  labels** (4th layout), opt-in **valid feedback**, and accessible `aria-describedby` / `aria-invalid` wiring.
- **LLM-optimized documentation** in [`docs/llm/`](docs/llm/index.md) (+ [`llms.txt`](llms.txt)).

## Documentation

The [**LLM usage guide**](docs/llm/index.md) is the current source of truth for v4:

- [index.md](docs/llm/index.md) — hub: config detection, the universal field model, the three syntaxes, and the
  full field catalog.
- On-demand spokes: [choice-fields](docs/llm/choice-fields.md), [layouts](docs/llm/layouts.md),
  [input-groups](docs/llm/input-groups.md), [model-binding](docs/llm/model-binding.md),
  [options-and-attributes](docs/llm/options-and-attributes.md), [components](docs/llm/components.md),
  [bootstrap5](docs/llm/bootstrap5.md), [config](docs/llm/config.md).

## License

Open-sourced software licensed under the [MIT license](LICENSE).
