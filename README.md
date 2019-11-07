# Bootstrap 4 forms builder for Laravel 5.8+ <!-- omit in toc --> 

This package (BF) uses in background [Laravel Collective form builder](https://laravelcollective.com/docs/5.8/html) to simplify Bootstrap 4 forms creation into Laravel 5.8+.

All Bootstrap forms features are supported (form layouts, custom fields, input groups, ...) as well as Model form binding and automatic error display.

> BF is heavily inspired by [Dwight Watson's](https://github.com/dwightwatson/bootstrap-form) and [Michael Burgin's](https://github.com/realripley00/bootstrap-form) Bootstrap form builders.  
> Credits and many thanks for their awesome work :-)

## Table of content <!-- omit in toc --> 

- [Installation](#installation)
- [Quick start](#quick-start)
- [Available methods & Blade directives](#available-methods--blade-directives)
- [Forms](#forms)
  - [Creating forms](#creating-forms)
  - [Form options](#form-options)
  - [Form variations](#form-variations)
  - [Model binding](#model-binding)
- [Form inputs](#form-inputs)
  - [Creating form inputs](#creating-form-inputs)
  - [Common form inputs options](#common-form-inputs-options)
  - [Text inputs](#text-inputs)
  - [Checkbox & radio](#checkbox--radio)
  - [Select input](#select-input)
  - [File input](#file-input)
  - [Range input](#range-input)
- [Input groups](#input-groups)
- [Misc](#misc)
  - [Hidden input](#hidden-input)
  - [Label](#label)
  - [Buttons](#buttons)

## Installation

Simply install the package using Composer:

```shell
composer require bgaze/bootstrap-form
```

There are a various configuration options available.  
Publish the configuration file to customize them:

```shell
php artisan vendor:publish --provider=Bgaze\BootstrapForm\BootstrapFormServiceProvider
```

## Quick start

There is two ways to use BF:

+ The `BF` facade.
+ Blade directives into blade templates.

Following examples are exactly the same:

```html
echo BF::open(['url' => 'some-url']);
echo BF::text('test', 'My test field');
echo BF::close();
```

```html
@open(['url' => 'some-url'])
@text('test', 'My test field')
@close
```

They both will produce this HTML code:

```html
<form method="POST" action="some-url" accept-charset="UTF-8" role="form">
    <input name="_token" type="hidden" value="H4qgr6kk7AlnnkxTbcUKvmEMWiFh8MaNyizdJB91">   

    <div id="test_group" class="form-group">
        <label for="test">My test field</label>
        <div>
            <input id="test" class="form-control" name="test" type="text">
        </div>
    </div>
</form>
```

Any form error will be automatically displayed:

```html
<form method="POST" action="some-url" accept-charset="UTF-8" role="form">
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

## Available methods & Blade directives

In this doc, we'll mainly use blade directives as it is probably the most common way to use BF.  
Each directive is an alias to a `BF` facade method and works exactly like it.

Here is the list of available methods and directives:

| Facade methods    | Blade directives | Description                                         |
| :---------------- | :--------------- | :-------------------------------------------------- |
| BF::htmlBuilder() | -                | Get the Laravel Collective form builder instance    |
| BF::formBuilder() | -                | Get the Laravel Collective HTML builder instance    |
| BF::open()        | @open()          | Open a form (layout based on default configuration) |
| BF::vertical()    | @vertical()      | Open a vertical form                                |
| BF::inline()      | @inline()        | Open a inline form                                  |
| BF::horizontal()  | @horizontal()    | Open a horizontal form                              |
| BF::close()       | @close           | Close a form                                        |
| BF::text()        | @text()          | Create a text input                                 |
| BF::email()       | @email()         | Create an email input                               |
| BF::url()         | @url()           | Create an url input                                 |
| BF::tel()         | @tel()           | Create a tel input                                  |
| BF::number()      | @number()        | Create a number input                               |
| BF::date()        | @date()          | Create a date input                                 |
| BF::time()        | @time()          | Create a time input                                 |
| BF::textarea()    | @textarea()      | Create a textarea                                   |
| BF::password()    | @password()      | Create a password input                             |
| BF::file()        | @file()          | Create a file input                                 |
| BF::hidden()      | @hidden()        | Create a hidden input                               |
| BF::select()      | @select()        | Create a select input                               |
| BF::range()       | @range()         | Create a range input                                |
| BF::checkbox()    | @checkbox()      | Create a checkbox input                             |
| BF::checkboxes()  | @checkboxes()    | Create a checkboxes group                           |
| BF::radio()       | @radio()         | Create a radio input                                |
| BF::radios()      | @radios()        | Create a radios group                               |
| BF::label()       | @label()         | Create a label                                      |
| BF::submit()      | @submit()        | Create a submit input                               |
| BF::reset()       | @reset()         | Create a reset input                                |
| BF::button()      | @button()        | Create a button                                     |

## Forms

### Creating forms

Open a form using `BF::open()` method, or `@open()` directive, wich accepts an array of options as argument.  
By default, a `POST` method will be assumed, however, you are free to specify another method.

Close the form using `BF::close()` method or `@close` directive.

```html
@open(['route' => 'users.index', 'method' => 'GET', 'id' => 'users-search-form'])
<!-- Add some fields -->
@close
```

There is three ways to set the form action. Priority order is: **url > route > action**

```html
<!-- Using an url --> 
@open(['url' => 'foo/bar'])

<!-- Using a named route --> 
@open(['route' => 'route.name'])

<!-- Using a controller action --> 
@open(['action' => 'Controller@method'])

<!-- You may pass in parameters as well --> 
@open(['route' => ['route.name', $id]])
@open(['action' => ['Controller@method', $id]])
```

If your form is going to accept file uploads, add a files option to your array:

```html
@open(['url' => 'foo/bar', 'files' => true])
```

### Form options

Please find below available form options.  
**Any other option passed to a form will be used as a HTML attribute.**

| Option             | Default value    | Accepted values                    | Decription                                                                    |
| :----------------- | :--------------- | :--------------------------------- | :---------------------------------------------------------------------------- |
| files              | null             | null / true                        | Configure form enctype for file upload                                        |
| url                | null             | string                             | An url to use as form action<br>Example: `/foo/bar`                           |
| route              | null             | string / array                     | A route to use as form action<br>Example: `users.update`                      |
| action             | null             | string / array                     | A controller action to use as form action<br>Example: `UserController@update` |
| store              | null             | string / array                     | The store action when using model binding                                     |
| update             | null             | string / array                     | The update action when using model binding                                    |
| model              | null             | Illuminate\Database\Eloquent\Model | A model to bind to the form                                                   |
| layout             | Inherited&nbsp;* | vertical / horizontal / inline     | The layout of the form                                                        |
| left_column_class  | Inherited&nbsp;* | string                             | The default width of left column (horizontal forms only)                      |
| right_column_class | Inherited&nbsp;* | string                             | The default width of right column (horizontal forms only)                     |
| show_all_errors    | Inherited&nbsp;* | bool                               | Show all the errors of an input or just the first one                         |

\* Inherited from package configuration. 

### Form variations

BF support the three Bootstrap form layouts: `vertical`, `horizontal`, `inline`.  
The form layout can be set when opening form using the `layout` option.

BF also provide helpers to open a form with a specific layout:

+ `BF::vertical()` / `@vertical()`
+ `BF::horizontal()` / `@horizontal()`
+ `BF::inline()` / `@inline()`

These function works exactly the same than `BF::open()` / `@open()` with the `layout` option forced.

### Model binding

Model binding allows to populate a form based on a model attributes.  
Fields will be populated with this priority: **Session (Old Input) > Explicit value > Model attribute**.

To open a model binded form, use the `model` option. 
Passed value **must** be an instance of `Illuminate\Database\Eloquent\Model` otherwise it **will** be ignored.  

```html
<!-- Using an url --> 
@open(['model' => $user, 'url' => url('users/store')])

<!-- Using a named route --> 
@open(['model' => $user, 'route' => 'users.store'])
@open(['model' => $user, 'route' => ['users.update', $user->id]])

<!-- Using a controller action --> 
@open(['model' => $user, 'action' => 'UserController@store'])
@open(['model' => $user, 'action' => ['UserController@update', $user->id]])
```

If you provide `store` and `update` options, BF will automatically set form action and method based on model existance.  
For update action, model route key will be automatically populated.

```html
<!-- Using named routes --> 
@open([
'model' => $user, 
'store' => 'users.store', 
'update' => 'users.update'
])

<!-- Using controller actions --> 
@open([
'model' => $user, 
'store' => 'UserController@store', 
'update' => 'UserController@update'
])

<!-- You may pass parameters as well but remeber that model route key will be added in last position --> 
@open([
'model' => $user, 
'store' => ['users.store', $routeParameter], 
'update' => ['users.update', $routeParameter]
])
@open([
'model' => $user, 
'store' => ['UserController@store', $routeParameter], 
'update' => ['UserController@update', $routeParameter]
])
```

## Form inputs

### Creating form inputs

Most of form input functions accept following four arguments:  

+ **name:** the name of the field.  
*Unless they are explicitly provided, name will also be use as id attribute and as base for the form group id attribute.*
+ **label**: the label of the field.  
`null` => label will be generated based on **name**.  
`false` => no label will be inserted.  
*Please note that HTML escaping is disabled on labels to ease complex label creation.*
+ **value:** the value of the field.  
If `null`, the value is automatically set based on session (`old` function) or model attributes when using model binding.
+ **options:** an array of options.  
*Any value passed that is not in field options list will be used as HTML attribute.*

### Common form inputs options

All the input functions accept following options in addition to their specific ones:

| Option             | Default value    | Accepted values                | Decription                                                                                                                                                  |
| :----------------- | :--------------- | :----------------------------- | :---------------------------------------------------------------------------------------------------------------------------------------------------------- |
| help               | false            | false / string                 | Display a help text under the field ([see doc](https://getbootstrap.com/docs/4.3/components/forms/#help-text))                                              |
| pull_right         | true             | bool                           | Add an empty left column to checkboxes, radios and fields without label to preserve fields alignment (horizontal forms only)                                |
| group              | null             | null / false / array           | `array`: a set of HTML attributes for the `form-group` element.<br>`false`: return the input element only.                                                  |
| left_column_class  | Inherited&nbsp;* | string                         | The width of left column (horizontal forms only)                                                                                                            |
| right_column_class | Inherited&nbsp;* | string                         | The width of right column (horizontal forms only)                                                                                                           |
| layout             | Inherited&nbsp;* | vertical / horizontal / inline | The form layout: you can force a field layout no matters current form layout. You'll need to adjust styles accordingly as this is not planned in Bootstrap. |
| show_all_errors    | Inherited&nbsp;* | bool                           | Show all the errors or just the first one                                                                                                                   |

\* Inherited from current form options, or package configuration if no opened form.

### Text inputs

Text inputs are: `BF::text()`, `BF::email()`, `BF::url()`, `BF::tel()`, `BF::number()`, `BF::date()`, `BF::time()`, `BF::textarea()`, `BF::password()`.  
They all work exactly the same way.  

**Signature:**

These functions signature is:

```html
BF::xxx($name, $label = null, $value = null, array $options = [])
@xxx($name, $label = null, $value = null, array $options = [])
```

Except password input that doesn't accept a value:

```html
BF::password($name, $label = null, array $options = [])
@password($name, $label = null, array $options = [])
```

**Options:**

In addition to [common options](#form-inputs), following ones are accepted:

| Option  | Default value | Accepted values        | Decription                                                                                    |
| :------ | :------------ | :--------------------- | :-------------------------------------------------------------------------------------------- |
| size    | null          | null / sm / lg         | The size of the field ([see doc](https://getbootstrap.com/docs/4.3/components/forms/#sizing)) |
| append  | false         | false / string / array | An input group prefix (see [Input groups](#input-groups) for details)                         |
| prepend | false         | false / string / array | An input group suffix (see [Input groups](#input-groups) for details)                         |

### Checkbox & radio

**Single input:**

```html
BF::checkbox($name, $label = null, $value = 1, $checked = null, array $options = [])
@checkbox($name, $label = null, $value = 1, $checked = null, array $options = [])

BF::radio($name, $label = null, $value = 1, $checked = null, array $options = [])
@radio($name, $label = null, $value = 1, $checked = null, array $options = [])
```

Pass a boolean into **$checked** argument to force input's checked state.

**Inputs group:**

```html
BF::checkboxes($name, $label = null, array $choices = [], $checked = null, array $options = [])
@checkboxes($name, $label = null, array $choices = [], $checked = null, array $options = [])

BF::radios($name, $label = null, array $choices = [], $checked = null, array $options = [])
@radios($name, $label = null, array $choices = [], $checked = null, array $options = [])
```

+ Use the **$choices** argument to define group inputs.  
A `value => label` associative array is expected: `['1' => 'Yes', '0' => 'No']`
+ **$checked** argument accept an array containing the value(s) of checked input(s).  
If only one input is checked, you can pass its value directly.

**Options:**

In addition to [common options](#form-inputs), following ones are accepted:

| Option | Default value | Accepted values | Decription                                                                                                   |
| :----- | :------------ | :-------------- | :----------------------------------------------------------------------------------------------------------- |
| inline | false         | bool            | Create an [inline input](https://getbootstrap.com/docs/4.3/components/forms/#inline)                         |
| custom | false         | bool            | Create a [custom input](https://getbootstrap.com/docs/4.3/components/forms/#checkboxes-and-radios-1)         |
| switch | false         | bool            | Create a [switch custom input](https://getbootstrap.com/docs/4.3/components/forms/#switches) (checkbox only) |

### Select input

**Signature:**

```html
BF::select($name, $label = null, $choices = [], $selected = null, array $options = [])
@select($name, $label = null, $choices = [], $selected = null, array $options = [])
```

+ **$choices:** the input options.  
Pass a `value => label` associative array: `['L' => 'Large', 'S' => 'Small']`  
Options groups can be created using `group lapel => options` array:  
`['Cats' => ['leopard' => 'Leopard'],'Dogs' => ['spaniel' => 'Spaniel']]` 
+ **$selected** argument accept an array containing the value(s) of selected option(s).  
If only one option is selected, you can pass its value directly.

**Options:**

In addition to [common options](#form-inputs), following ones are accepted:

| Option  | Default value | Accepted values        | Decription                                                                                     |
| :------ | :------------ | :--------------------- | :--------------------------------------------------------------------------------------------- |
| custom  | false         | bool                   | Create a [custom file input](https://getbootstrap.com/docs/4.3/components/forms/#file-browser) |
| size    | null          | null / sm / lg         | The size of the field ([see doc](https://getbootstrap.com/docs/4.3/components/forms/#sizing))  |
| append  | false         | false / string / array | An input group prefix (see [Input groups](#input-groups) for details)                          |
| prepend | false         | false / string / array | An input group suffix (see [Input groups](#input-groups) for details)                          |

### File input

> Custom file input requires non included additional JavaScript.  
> The recommended plugin is [bs-custom-file-input](https://www.npmjs.com/package/bs-custom-file-input).

**Signature:**

```html
BF::file($name, $label = null, array $options = [])
@file($name, $label = null, array $options = [])
```

**Options:**

In addition to [common options](#form-inputs), following ones are accepted:

| Option  | Default value | Accepted values        | Decription                                                                                     |
| :------ | :------------ | :--------------------- | :--------------------------------------------------------------------------------------------- |
| custom  | false         | bool                   | Create a [custom file input](https://getbootstrap.com/docs/4.3/components/forms/#file-browser) |
| text    | "Choose file" | string                 | The placeholder text (custom file input only)                                                  |
| button  | null          | string                 | The button text (custom file input only)                                                       |
| append  | false         | false / string / array | An input group prefix (custom file input only, see [Input groups](#input-groups) for details)  |
| prepend | false         | false / string / array | An input group suffix (custom file input only, see [Input groups](#input-groups) for details)  |

### Range input

**Signature:**

```html
BF::range($name, $label = null, $value = null, array $options = [])
@range($name, $label = null, $value = null, array $options = [])
```

**Options:**

In addition to [common options](#form-inputs), following option is accepted:

| Option | Default value | Accepted values | Decription                                                                               |
| :----- | :------------ | :-------------- | :--------------------------------------------------------------------------------------- |
| custom | false         | bool            | Create a [custom range input](https://getbootstrap.com/docs/4.3/components/forms/#range) |

## Input groups

For compatible input types, set `append` / `prepend` options to create a Bootstrap input group.  

These options accept a HTML string, or an array of HTML strings, so you're free to build your field addons as you want.  
See [documentation](https://getbootstrap.com/docs/4.3/components/input-group/) for details.

> Please note that provided HTML **is not escaped**.

```shell
@text('input_with_prepend', null, null, [
    'prepend' => '<span class="input-group-text">€</span>'
])

@text('input_with_prepend', null, null, [
    'prepend' => '<span class="input-group-text">€</span>'
])

@text('input_with_both', null, null, [
    'prepend' => '<span class="input-group-text">Price</span>',
    'append' => '<span class="input-group-text">€</span>'
])

@text('input_with_addons_array', null, null, [
    'prepend' => ['<span class="input-group-text">Price</span>', '<span class="input-group-text">€</span>']
])
```


## Misc

### Hidden input

> This function is an alias to [Form Builder](https://laravelcollective.com/docs/5.8/html#text) function.

**Signature:**

```html
BF::hidden($name, $value = null, $options = [])
@hidden($name, $value = null, $options = [])
```

### Label

> This function is an alias to [Form Builder](https://laravelcollective.com/docs/5.8/html#labels) function.

**Signature:**

```html
BF::label($name, $value = null, array $options = [], $escapeHtml = false)
@label($name, $value = null, array $options = [], $escapeHtml = false)
```

### Buttons

To create Bootstrap flavoured buttons, BF provides following helpers.

If you pass a string into the **options** argument, it will be prefixed by `btn btn-`.
If you pass an array, it will be merge on `['class' => 'btn btn-xxx']`, with `xxx` the default button style.

**Submit button:**

Signature:

```html
BF::submit($value = null, $options = 'primary')
@submit($value = null, $options = 'primary')
```

Examples:

```html
@reset()
@reset('Reset form')
@reset(null, 'success btn-block')
@reset(null, ['id' => 'reset-button'])

<!-- Result: -->

<input class="btn btn-primary" type="submit"> 
<input class="btn btn-primary" type="submit" value="Reset form">
<input class="btn btn-success btn-block" type="submit"> 
<input class="btn btn-primary" id="reset-button" type="submit"> 
```

**Reset button:**

Signature:

```html
BF::reset($value = null, $options = 'danger')
@reset($value = null, $options = 'danger')
```

Examples:

```html
@reset()
@reset('Reset form')
@reset(null, 'warning btn-sm')
@reset(null, ['id' => 'reset-button'])

<!-- Result: -->

<input class="btn btn-danger" type="reset"> 
<input class="btn btn-danger" type="reset" value="Reset form">
<input class="btn btn-warning btn-sm" type="reset"> 
<input class="btn btn-danger" id="reset-button" type="reset"> 
```

**Standard button:**

Signature:

```html
BF::button($value = null, $options = 'primary')
@button($value = null, $options = 'primary')
```

Examples:

```html
@button()
@button('Info')
@button('Info', 'info btn-lg')
@button('Info', ['id' => 'info-button'])

<!-- Result: -->

<button class="btn btn-primary" type="button"></button>
<button class="btn btn-primary" type="button">Info</button>
<button class="btn btn-info btn-lg" type="button">Info</button>
<button class="btn btn-primary" id="info-button" type="button">Info</button>
```
