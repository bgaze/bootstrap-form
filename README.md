# Bootstrap 4 forms builder for Laravel 5.8+ <!-- omit in toc --> 

[![GitHub license](https://img.shields.io/github/license/bgaze/bootstrap-form)](https://github.com/bgaze/bootstrap-form/blob/master/LICENSE)
![Maintenance](https://img.shields.io/maintenance/yes/2020)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/bgaze/bootstrap-form)
[![GitHub stars](https://img.shields.io/github/stars/bgaze/bootstrap-form)](https://github.com/bgaze/bootstrap-form/stargazers)
![Packagist](https://img.shields.io/packagist/dt/bgaze/bootstrap-form)

<p align="center">
    <img src="./intro.png">
</p>

This package uses in background [Laravel Collective HTML](https://laravelcollective.com/docs/5.8/html) to simplify Bootstrap 4 forms creation into Laravel applications.

Model form binding and automatic error display are supported, as well as most of Bootstrap forms features : form layouts, custom fields, input groups, ... 

Any contribution or feedback is highly welcomed, please feel free to create a pull request or [submit a new issue](https://github.com/bgaze/bootstrap-form/issues/new).

> BF is mainly inspired by [Dwight Watson's](https://github.com/dwightwatson/bootstrap-form) and [Michael Burgin's](https://github.com/realripley00/bootstrap-form) awesome work.  
> Credits and many thanks to them :-)

## Documentation 

Full fodumentation, as well as dmos, are available at [https://packages.bgaze.fr/bootstrap-form](https://packages.bgaze.fr/bootstrap-form)

## Quick start

Simply install the package using Composer:

```shell
composer require bgaze/bootstrap-form
```

There are a various configuration options available, publish the configuration file to customize them:

```shell
php artisan vendor:publish --provider="Bgaze\BootstrapForm\BootstrapFormServiceProvider"
```

That's it, you can start to build forms.  
The `BF` facade provides [many methods](#available-methods-and-directives) to create forms and form inputs.  
All of them have a Blade directive alias.

In this doc, we'll mainly use blade directives as it is probably the most common way to use BF.  
Following examples are exactly the same:

```html
echo BF::open(['url' => '/login']);
echo BF::text('test', 'My test field');
echo BF::close();
```

```html
@open(['url' => '/login'])
@text('test', 'My test field')
@close
```

They both will produce this HTML code:

```html
<form method="POST" action="/login" accept-charset="UTF-8" role="form">
    <input name="_token" type="hidden" value="H4qgr6kk7AlnnkxTbcUKvmEMWiFh8MaNyizdJB91">   

    <div id="test_group" class="form-group">
        <label for="test">My test field</label>
        <div>
            <input id="test" class="form-control" name="test" type="text">
        </div>
    </div>
</form>
```

Any form error present in the session error bag (`old` function) will be automatically displayed:

```html
<form method="POST" action="/login" accept-charset="UTF-8" role="form">
    <input name="_token" type="hidden" value="H4qgr6kk7AlnnkxTbcUKvmEMWiFh8MaNyizdJB91">   

    <div id="test_group" class="is-invalid form-group">
        <label for="test">My test field</label>
        <div>
            <input id="test" class="form-control is-invalid" name="test" type="text">
            <div class="invalid-feedback">The test field is required.</div>
        </div>
    </div>
</form>
```
