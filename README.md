# Bootstrap 4 & 5 forms builder for Laravel 12+ <!-- omit in toc -->

[![Tests](https://github.com/bgaze/bootstrap-form/actions/workflows/tests.yml/badge.svg)](https://github.com/bgaze/bootstrap-form/actions/workflows/tests.yml)
[![GitHub license](https://img.shields.io/github/license/bgaze/bootstrap-form)](https://github.com/bgaze/bootstrap-form/blob/master/LICENSE)
![Maintenance](https://img.shields.io/maintenance/yes/2030)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/bgaze/bootstrap-form)
[![GitHub Repo stars](https://img.shields.io/github/stars/bgaze/bootstrap-form?style=flat)](https://github.com/bgaze/bootstrap-form/stargazers)
![Packagist](https://img.shields.io/packagist/dt/bgaze/bootstrap-form)

This package simplifies Bootstrap forms creation in Laravel applications, rendering all markup through its own
lightweight HTML/form layer.

It renders **Bootstrap 5** markup by default and **fully supports Bootstrap 4** for backward compatibility.

Model form binding and automatic error display are supported, as well as most Bootstrap form features: form layouts,
custom fields, input groups, and more.

Any contribution or feedback is highly welcomed, please feel free to create a pull request
or [submit a new issue](https://github.com/bgaze/bootstrap-form/issues/new).

## ℹ️ v4 status — functional, documentation in progress

v4 is functional and tested, but its full documentation is still being written.

In the meantime, the [LLM usage guide](docs/llm/index.md) shipped in this repository is the **authoritative,
up-to-date reference** (dense but exact — usable by humans and AI assistants alike).

> The [full documentation site](https://packages.bgaze.fr/bootstrap-form) still reflects v3 and will be regenerated
> for v4.

## ⚠️ Upgrading — v4 has breaking changes

v4 introduces **breaking changes** over v3 (see [What's new](#whats-new-in-v4) below).

To keep using a previous major version, require it explicitly and refer to its dedicated branch:

| Version                                 | Install                                        | Docs                                                                                                            |
|-----------------------------------------|------------------------------------------------|-----------------------------------------------------------------------------------------------------------------|
| **v3** (Bootstrap 4 default, B5 opt-in) | `composer require "bgaze/bootstrap-form:^3.0"` | [v3 branch](https://github.com/bgaze/bootstrap-form/tree/v3) · [site](https://packages.bgaze.fr/bootstrap-form) |
| **v2** (Bootstrap 4 only)               | `composer require "bgaze/bootstrap-form:^2.0"` | [v2 branch](https://github.com/bgaze/bootstrap-form/tree/v2) · [site](https://packages.bgaze.fr/bootstrap-form) |

## What's new in v4

- **No more third-party dependency**  
  The historical `laravelcollective/html` dependency is gone, replaced by an
  internal, iso-rendering HTML/form layer owned by the package.
- **Bootstrap 5 is now the default**  
  Bootstrap 4 remains fully supported for backward compatibility (frozen),
  switchable via `bootstrap_version` globally, per form (`BF::open(['bootstrap_version' => 4])`), or per field.
- **Laravel 12 & 13, PHP 8.2+**  
  Older versions dropped.
- **x-components**  
  A new Blade component syntax (`<x-bf::text .../>`) alongside the directives and the `BF` facade; all three render
  identical markup.
- **Richer choice grammar for select/checkboxes/radios**  
  Optgroups, per-option attributes, accepting any `iterable` (array, Collection, generator).
- **New field types & layouts**  
  `datetime-local`, `month`, `week`, `search` inputs, Bootstrap 5 floating labels, opt-in valid feedback, and accessible
  `aria-describedby` / `aria-invalid` wiring.
- **LLM-optimized documentation**  
  See [`docs/llm/`](docs/llm/index.md) (+ [`llms.txt`](llms.txt)).

## Quick start

### Requirements

- PHP **8.2+**
- Laravel **12** or **13**

### Installation

Install the package using Composer:

```shell
composer require bgaze/bootstrap-form
```

Several configuration options are available; publish the configuration file to customize them:

```shell
php artisan vendor:publish --provider="Bgaze\BootstrapForm\BootstrapFormServiceProvider"
```

### Usage

Forms can be built through three interchangeable syntaxes that produce byte-identical HTML:

**x-components**

```blade
<x-bf::form url="/x">
    <x-bf::text name="field"/>
    <x-bf::submit>Save</x-bf::submit>
</x-bf::form>
```

**Blade directives**

```blade
@open(['url' => '/x'])
@text('field')
@submit('Save')
@close
```

**BF facade**

```php
echo BF::open(['url' => '/x']);
echo BF::text('field');
echo BF::submit('Save');
echo BF::close();
```

## License

Open-sourced software licensed under the [MIT license](LICENSE).
