# Bootstrap 4 & 5 forms builder for Laravel 12+ <!-- omit in toc -->

[![Tests](https://github.com/bgaze/bootstrap-form/actions/workflows/tests.yml/badge.svg)](https://github.com/bgaze/bootstrap-form/actions/workflows/tests.yml)
[![GitHub license](https://img.shields.io/github/license/bgaze/bootstrap-form)](https://github.com/bgaze/bootstrap-form/blob/master/LICENSE)
![Maintenance](https://img.shields.io/maintenance/yes/2030)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/bgaze/bootstrap-form)
[![GitHub Repo stars](https://img.shields.io/github/stars/bgaze/bootstrap-form?style=flat)](https://github.com/bgaze/bootstrap-form/stargazers)
![Packagist](https://img.shields.io/packagist/dt/bgaze/bootstrap-form)

<p align="center">
    <img src="https://packages.bgaze.fr/images/bootstrap-form.png" alt="Bootstrap 4 & 5 forms builder for Laravel 12+">
</p>

This package simplifies Bootstrap forms creation in Laravel applications, rendering all markup through its own
lightweight HTML/form layer.

It renders **Bootstrap 5** markup by default and **fully supports Bootstrap 4** for backward compatibility.

Model form binding and automatic error display are supported, as well as most Bootstrap form features: form layouts,
custom fields, input groups, and more.

Any contribution or feedback is highly welcomed, please feel free to create a pull request
or [submit a new issue](https://github.com/bgaze/bootstrap-form/issues/new).

## ⚠️ v4 has breaking changes

v4 drops the historical `laravelcollective/html` dependency in favor of an internal, iso-rendering HTML/form layer,
and renders Bootstrap 5 by default. 

Before upgrading an existing application, read [Upgrading from v3](https://packages.bgaze.fr/bootstrap-form#upgrading-from-v3).

To keep using a previous major version, require it explicitly and refer to its dedicated branch:

| Version                                 | Install                                        | Docs                                                                                                        |
|-----------------------------------------|------------------------------------------------|-------------------------------------------------------------------------------------------------------------|
| **v3** (Bootstrap 4 default, B5 opt-in) | `composer require "bgaze/bootstrap-form:^3.0"` | [v3 branch](https://github.com/bgaze/bootstrap-form/tree/v3) · [archived docs](https://packages.bgaze.fr/bootstrap-form/v3) |
| **v2** (Bootstrap 4 only)               | `composer require "bgaze/bootstrap-form:^2.0"` | [v2 branch](https://github.com/bgaze/bootstrap-form/tree/v2) · [archived docs](https://packages.bgaze.fr/bootstrap-form/v3) |

## Documentation

Full documentation and examples are available
at [https://packages.bgaze.fr/bootstrap-form](https://packages.bgaze.fr/bootstrap-form)

If you use **PhpStorm IDE**, you can also
check [this gist](https://gist.github.com/bgaze/1f559782c85511dc2671cdb6b453f0c6) which allow to easily configure
**syntax highlighting** and **live templates** for this package's custom Blade directives.

If you build forms with the help of an AI coding assistant, this repository also ships an LLM-optimized usage
guide: [`docs/llm/index.md`](docs/llm/index.md), indexed by [`llms.txt`](llms.txt).

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

Forms can be built through three interchangeable syntaxes that produce byte-identical HTML.

Blade x-components:

```blade
<x-bf::form url="/my/url" novalidate>
    <x-bf::text name="login"/>
    <x-bf::email name="email"/>
    <x-bf::checkbox name="remember_me" switch inline/>
    <x-bf::submit>Login</x-bf::submit>
</x-bf::form>
```

Blade directives:

```blade
@open(['url' => '/my/url', 'novalidate' => true])
@text('login')
@email('email')
@checkbox('remember_me', null, 1, null, ['switch' => true, 'inline' => true])
@submit('Login')
@close
```

The `BF` facade, in any PHP context:

```php
echo BF::open(['url' => '/my/url', 'novalidate' => true]);
echo BF::text('login');
echo BF::email('email');
echo BF::checkbox('remember_me', null, 1, null, ['switch' => true, 'inline' => true]);
echo BF::submit('Login');
echo BF::close();
```

## License

Open-sourced software licensed under the [MIT license](LICENSE).
