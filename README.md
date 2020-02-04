# Bootstrap 4 forms builder for Laravel 5.8+ <!-- omit in toc --> 

<p align="center">
    <img src="./intro.png">
</p>

This package uses in background [Laravel Collective HTML](https://laravelcollective.com/docs/5.8/html) to simplify Bootstrap 4 forms creation into Laravel applications.

Model form binding and automatic error display are supported, as well as most of Bootstrap forms features : form layouts, custom fields, input groups, ... 

Any contribution or feedback is highly welcomed, please feel free to create a pull request or [submit a new issue](https://github.com/bgaze/bootstrap-form/issues/new).

> BF is mainly inspired by [Dwight Watson's](https://github.com/dwightwatson/bootstrap-form) and [Michael Burgin's](https://github.com/realripley00/bootstrap-form) awesome work.  
> Credits and many thanks to them :-)

## Table of content <!-- omit in toc --> 

> Demos are available at [https://www.bgaze.fr/bootstrap-form](https://www.bgaze.fr/bootstrap-form)

- [Installation](#installation)
- [Quick start](#quick-start)
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
- [Misc](#misc)
  - [Hidden input](#hidden-input)
  - [Input groups](#input-groups)
  - [Label](#label)
  - [Buttons](#buttons)
- [Available methods and directives](#available-methods-and-directives)

## Installation

Simply install the package using Composer:

```shell
composer require bgaze/bootstrap-form
```

There are a various configuration options available, publish the configuration file to customize them:

```shell
php artisan vendor:publish --provider="Bgaze\BootstrapForm\BootstrapFormServiceProvider"
```

That's it, you can start to build forms.

## Quick start

> Demos are available at [https://www.bgaze.fr/bootstrap-form]()

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

## Forms

### Creating forms

Open a form using `BF::open()` method, or `@open()` directive, wich accepts an array of options as argument.  
By default, a `POST` method will be assumed. However, you are free to specify another method.

Close the form using `BF::close()` method or `@close` directive.

```html
@open(['route' => 'users.index', 'method' => 'GET', 'id' => 'users-search-form'])
<!-- Add some fields -->
@close
```

There is three ways to set the form action, priority order is: **url > route > action**

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
=> Options take precedence on attributes, to use an option key as HTML attribute, prefix it with a `~`.  
=> Any key that is not in form's options list will be used as HTML attribute.

| Option                            | Default&nbsp;value              | Accepted&nbsp;values               | Decription                                                                                                |
| :-------------------------------- | :------------------------------ | :--------------------------------- | :-------------------------------------------------------------------------------------------------------- |
| files                             | null                            | null / true                        | Configure form enctype for file upload                                                                    |
| url                               | null                            | string                             | An url to use as form action<br>Example: `/foo/bar`                                                       |
| route                             | null                            | string / array                     | A route to use as form action<br>Example: `users.update`                                                  |
| action                            | null                            | string / array                     | A controller action to use as form action<br>Example: `UserController@update`                             |
| store                             | null                            | string / array                     | The store action when using model binding                                                                 |
| update                            | null                            | string / array                     | The update action when using model binding                                                                |
| model                             | null                            | Illuminate\Database\Eloquent\Model | A model to bind to the form                                                                               |
| layout                            | Inherited&nbsp;<sup>**1**</sup> | vertical / horizontal / inline     | The Bootstrap layout of the form ([doc](https://getbootstrap.com/docs/4.3/components/forms/#layout))      |
| group                             | Inherited&nbsp;<sup>**1**</sup> | bool / array                       | A set of HTML attributes for all the form groups (extends **group** package configuration)                |
| custom                            | Inherited&nbsp;<sup>**1**</sup> | bool                               | Use Bootstrap custom style by default when available                                                      |
| show_all_errors                   | Inherited&nbsp;<sup>**1**</sup> | bool                               | Show all the errors of an input or just the first one                                                     |
| pull_right&nbsp;<sup>**2**</sup>  | Inherited&nbsp;<sup>**1**</sup> | false / HTML class                 | Add an empty left column to checkboxes, radios and fields without label to preserve fields alignment      |
| left_class&nbsp;<sup>**2**</sup>  | Inherited&nbsp;<sup>**1**</sup> | string                             | The default width of left column                                                                          |
| right_class&nbsp;<sup>**2**</sup> | Inherited&nbsp;<sup>**1**</sup> | string                             | The default width of right column                                                                         |
| lspace&nbsp;<sup>**3**</sup>      | Inherited&nbsp;<sup>**1**</sup> | false / HTML class                 | The horizontal blank space between labels and fields, doesn't apply to checkboxes and radios (CSS needed) |
| hspace&nbsp;<sup>**3**</sup>      | Inherited&nbsp;<sup>**1**</sup> | false / HTML class                 | The horizontal blank space between fields                                                                 |
| vspace&nbsp;<sup>**3**</sup>      | Inherited&nbsp;<sup>**1**</sup> | false / HTML class                 | The vertical blank space between fields                                                                   |

<small>**1:** Inherited from package configuration.</small>  
<small>**2:** Horizontal layout only.</small>  
<small>**3:** Inline layout only.</small>  

### Form variations

BF support the three Bootstrap form layouts: `vertical`, `horizontal`, `inline`.  
The layout can be set when opening forms using the `layout` option.

BF also provide helpers to open a form with a specific layout:

+ `BF::vertical()` / `@vertical()`
+ `BF::horizontal()` / `@horizontal()`
+ `BF::inline()` / `@inline()`

These functions works exactly the same than `BF::open()` / `@open()` with the `layout` option forced.

### Model binding

Model binding allows to populate a form based on a model attributes.  
Fields will be populated with this priority: **Session (Old Input) > Explicit value > Model attribute**.

To open a model binded form, use the `model` option. 
Passed value **must** be an instance of `Illuminate\Database\Eloquent\Model` otherwise it will be ignored.  

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
@open(['model' => $user, 'store' => 'users.store', 'update' => 'users.update'])

<!-- Using controller actions -->
@open(['model' => $user, 'store' => 'UserController@store', 'update' => 'UserController@update'])

<!-- You may pass parameters as well but remeber that model route key will be added in last position -->
@open(['model' => $user, 'store' => ['users.store', $routeParameter], 'update' => ['users.update', $routeParameter]])
@open(['model' => $user, 'store' => ['UserController@store', $routeParameter], 'update' => ['UserController@update', $routeParameter]])
```

## Form inputs

### Creating form inputs

Most of input functions accept following arguments:  

+ **name**  
The name of the field.  
Unless they are explicitly provided, name will also be used as id attribute and as base for the form group id attribute.

+ **label**  
The label of the field.  
Please note that HTML escaping is disabled on labels to ease complex label creation.  
If label is `null`, it will be generated based on name value.  
If label is `false`, no label will be inserted.   

+ **value**  
The value of the field.  
Field value is automatically evaluated with this priority:  
**Session (Old Input) > Explicit value > Model attribute (model binded forms only)**  
Most of times, you just need to set `null` as value. 

+ **options**  
An array of options.  
=> Options take precedence on attributes, to use an option key as HTML attribute, prefix it with a `~`.  
=> Any key that is not in field's options list will be used as HTML attribute.

### Common form inputs options

All the input functions accept following common options in addition to their specific ones.

> The `layout` option allows to override the current context layout: for instance, you can add a horizontal form group into a vertical form.
> As this is not a planned Bootstrap feature, you'll probably need to adapt CSS accordingly.

| Option                            | Default&nbsp;value              | Accepted&nbsp;values           | Decription                                                                                                                                   |
| :-------------------------------- | :------------------------------ | :----------------------------- | :------------------------------------------------------------------------------------------------------------------------------------------- |
| help                              | false                           | false / string                 | Display a help text under the field ([see doc](https://getbootstrap.com/docs/4.3/components/forms/#help-text))                               |
| pull_right                        | true                            | bool                           | Add an empty left column to checkboxes, radios and fields without label to preserve fields alignment (horizontal forms only)                 |
| group                             | Inherited&nbsp;<sup>**1**</sup> | null / false / array           | `array`: a set of HTML attributes for the **form-group** element (extends **group** form option).<br>`false`: return the input element only. |
| layout                            | Inherited&nbsp;<sup>**1**</sup> | vertical / horizontal / inline | The Bootstrap layout of the field                                                                                                            |
| show_all_errors                   | Inherited&nbsp;<sup>**1**</sup> | bool                           | Show all the errors of an input or just the first one                                                                                        |
| pull_right&nbsp;<sup>**2**</sup>  | Inherited&nbsp;<sup>**1**</sup> | false / HTML class             | Add an empty left column to checkboxes, radios and fields without label to preserve fields alignment                                         |
| left_class&nbsp;<sup>**2**</sup>  | Inherited&nbsp;<sup>**1**</sup> | string                         | The default width of left column                                                                                                             |
| right_class&nbsp;<sup>**2**</sup> | Inherited&nbsp;<sup>**1**</sup> | string                         | The default width of right column                                                                                                            |
| lspace&nbsp;<sup>**3**</sup>      | Inherited&nbsp;<sup>**1**</sup> | false / HTML class             | The horizontal blank space between label and field, doesn't apply to checkboxes and radios (CSS needed)                                      |
| hspace&nbsp;<sup>**3**</sup>      | Inherited&nbsp;<sup>**1**</sup> | false / HTML class             | The horizontal blank space between form groups                                                                                               |
| vspace&nbsp;<sup>**3**</sup>      | Inherited&nbsp;<sup>**1**</sup> | false / HTML class             | The vertical blank space between form groups                                                                                                 |

<small>**1:** Inherited from current form options, or package configuration if no opened form.</small>  
<small>**2:** Horizontal layout only.</small>  
<small>**3:** Inline layout only.</small>  

### Text inputs

 Text inputs funtions are:

+ `BF::text()` / `@text()`
+ `BF::email()` / `@email()`
+ `BF::url()` / `@url()`
+ `BF::tel()` / `@tel()`
+ `BF::number()` / `@number()`
+ `BF::date()` / `@date()`
+ `BF::time()` / `@time()`
+ `BF::textarea()` / `@textarea()`
+ `BF::password()` / `@password()`

**Signature:**

All these functions signature is the same, except password input that doesn't accept a value:

```html
BF::text($name, $label = null, $value = null, array $options = [])
@text($name, $label = null, $value = null, array $options = [])

BF::password($name, $label = null, array $options = [])
@password($name, $label = null, array $options = [])
```

**Options:**

In addition to [common options](#form-inputs), following ones are accepted:

| Option  | Default value | Accepted values        | Decription                                                                                |
| :------ | :------------ | :--------------------- | :---------------------------------------------------------------------------------------- |
| size    | null          | null / sm / lg         | The size of the field ([doc](https://getbootstrap.com/docs/4.3/components/forms/#sizing)) |
| append  | false         | false / string / array | An input group prefix (see [Input groups](#input-groups))                                 |
| prepend | false         | false / string / array | An input group suffix (see [Input groups](#input-groups))                                 |

### Checkbox & radio

**Single input:**

Pass a boolean into **$checked** argument to force input's checked state.

```html
BF::checkbox($name, $label = null, $value = 1, $checked = null, array $options = [])
@checkbox($name, $label = null, $value = 1, $checked = null, array $options = [])

BF::radio($name, $label = null, $value = 1, $checked = null, array $options = [])
@radio($name, $label = null, $value = 1, $checked = null, array $options = [])
```

**Inputs group:**

+ Use the **$choices** argument to define group inputs.  
A `value => label` associative array is expected: `['1' => 'Yes', '0' => 'No']`
+ **$checked** argument accept an array containing the value(s) of checked input(s).  
If only one input is checked, you can pass its value directly.

```html
BF::checkboxes($name, $label = null, array $choices = [], $checked = null, array $options = [])
@checkboxes($name, $label = null, array $choices = [], $checked = null, array $options = [])

BF::radios($name, $label = null, array $choices = [], $checked = null, array $options = [])
@radios($name, $label = null, array $choices = [], $checked = null, array $options = [])
```

**Options:**

In addition to [common options](#form-inputs), following ones are accepted:

| Option                       | Default value                   | Accepted values | Decription                                                                                                  |
| :--------------------------- | :------------------------------ | :-------------- | :---------------------------------------------------------------------------------------------------------- |
| inline                       | false                           | bool            | Create inline input(s) ([doc](https://getbootstrap.com/docs/4.3/components/forms/#inline))                  |
| custom                       | Inherited&nbsp;<sup>**1**</sup> | bool            | Create custom input(s) ([doc](https://getbootstrap.com/docs/4.3/components/forms/#checkboxes-and-radios-1)) |
| switch&nbsp;<sup>**2**</sup> | false                           | bool            | Create switch custom input(s) ([doc](https://getbootstrap.com/docs/4.3/components/forms/#switches))         |

<small>**1:** Inherited from current form options, or package configuration if no opened form.</small>  
<small>**2:** Checkbox inputs and groups only.</small>

### Select input

**Signature:**

```html
BF::select($name, $label = null, $choices = [], $selected = null, array $options = [])
@select($name, $label = null, $choices = [], $selected = null, array $options = [])
```

+ **$choices:** the select options.  
Pass a `value => label` associative array: 
`['L' => 'Large', 'S' => 'Small']`  
Options groups can be created using nested array. Example:  
`['Cats' => ['leopard' => 'Leopard'],'Dogs' => ['spaniel' => 'Spaniel']]` 
+ **$selected:** an array of selected option(s) value(s).  
If only one option is selected, you can pass its value directly.

**Options:**

In addition to [common options](#form-inputs), following ones are accepted:

| Option  | Default value                   | Accepted values        | Decription                                                                                           |
| :------ | :------------------------------ | :--------------------- | :--------------------------------------------------------------------------------------------------- |
| custom  | Inherited&nbsp;<sup>**1**</sup> | bool                   | Create a custom file input ([doc](https://getbootstrap.com/docs/4.3/components/forms/#file-browser)) |
| size    | null                            | null / sm / lg         | The size of the field ([doc](https://getbootstrap.com/docs/4.3/components/forms/#sizing))            |
| append  | false                           | false / string / array | An input group prefix (see [Input groups](#input-groups))                                            |
| prepend | false                           | false / string / array | An input group suffix (see [Input groups](#input-groups))                                            |

<small>**1:** Inherited from current form options, or package configuration if no opened form.</small>  

### File input

> Custom file input requires additional JavaScript.  
> The recommended plugin is [bs-custom-file-input](https://www.npmjs.com/package/bs-custom-file-input).

**Signature:**

```html
BF::file($name, $label = null, array $options = [])
@file($name, $label = null, array $options = [])
```

**Options:**

In addition to [common options](#form-inputs), following ones are accepted:

| Option                        | Default value                   | Accepted values        | Decription                                                                                           |
| :---------------------------- | :------------------------------ | :--------------------- | :--------------------------------------------------------------------------------------------------- |
| custom                        | Inherited&nbsp;<sup>**1**</sup> | bool                   | Create a custom file input ([doc](https://getbootstrap.com/docs/4.3/components/forms/#file-browser)) |
| text&nbsp;<sup>**2**</sup>    | "Choose file"                   | string                 | The placeholder text                                                                                 |
| button&nbsp;<sup>**2**</sup>  | null                            | string                 | The button text                                                                                      |
| append&nbsp;<sup>**2**</sup>  | false                           | false / string / array | An input group prefix (see [Input groups](#input-groups))                                            |
| prepend&nbsp;<sup>**2**</sup> | false                           | false / string / array | An input group suffix (see [Input groups](#input-groups))                                            |

<small>**1:** Inherited from current form options, or package configuration if no opened form.</small>  
<small>**2:** Custom file inputs only.</small>  

### Range input

See [documentation](https://getbootstrap.com/docs/4.3/components/forms/#range) for more details on range inputs usage.

**Signature:**

```html
BF::range($name, $label = null, $value = null, array $options = [])
@range($name, $label = null, $value = null, array $options = [])
```

**Options:**

In addition to [common options](#form-inputs), following option is accepted:

| Option | Default value                   | Accepted values | Decription                                                                               |
| :----- | :------------------------------ | :-------------- | :--------------------------------------------------------------------------------------- |
| custom | Inherited&nbsp;<sup>**1**</sup> | bool            | Create a [custom range input](https://getbootstrap.com/docs/4.3/components/forms/#range) |

<small>**1:** Inherited from current form options, or package configuration if no opened form.</small>  

## Misc

### Hidden input

This function is an alias to [Form Builder function](https://laravelcollective.com/docs/5.8/html#text).  
As for other inputs, `id` attribute will be added if missing

```html
BF::hidden($name, $value = null, $options = [])
@hidden($name, $value = null, $options = [])
```
### Input groups

For compatible input types, set `append` / `prepend` options to create a Bootstrap input group.  
See [documentation](https://getbootstrap.com/docs/4.3/components/input-group/) for details.

These options accept a HTML string, or an array of HTML strings, so you're free to build your field addons as you want.  
Please note that provided HTML **is not escaped**.

```shell
@text('input_with_prepend', null, null, [
    'prepend' => '<span class="input-group-text">€</span>'
])

@text('input_with_append', null, null, [
    'append' => '<span class="input-group-text">€</span>'
])

@text('input_with_both', null, null, [
    'prepend' => '<span class="input-group-text">Price</span>',
    'append' => '<span class="input-group-text">€</span>'
])

@text('input_with_addons_array', null, null, [
    'prepend' => ['<span class="input-group-text">Price</span>', '<span class="input-group-text">€</span>']
])
```

### Label

This function is an alias to [Form Builder function](https://laravelcollective.com/docs/5.8/html#labels) with escaping HTML disabled by default.

```html
BF::label($name, $value = null, array $options = [], $escapeHtml = false)
@label($name, $value = null, array $options = [], $escapeHtml = false)
```

### Buttons

To create Bootstrap flavoured buttons, BF provides following helpers.

If you pass a string into the **options** argument, it will be prefixed by `btn btn-`.  
If you pass an array, it will be merge on `['class' => 'btn btn-xxx']`, with `xxx` the default button style.

**Submit button:**

Default style is `primary`.  
This function generates an `input` tag, if you want to create a `button` tag, use `BF::button()` or `@button()` instead, passing a `type` option.

Signature:

```html
BF::submit($value = null, $options = null)
@submit($value = null, $options = null)
```

Examples:

```html
@submit()
@submit('Reset form', 'success btn-sm')
@submit(null, ['id' => 'submit-button'])
@submit(null, ['class' => false])

<!-- Result: -->

<input class="btn btn-primary" type="submit">
<input class="btn btn-success btn-sm" type="submit" value="Reset form">
<input class="btn btn-primary" id="submit-button" type="submit">
<input type="submit">
```

**Reset button:**

Default style is `danger`.  
This function generates an `input` tag, if you want to create a `button` tag, use `BF::button()` or `@button()` instead, passing a `type` option.

Signature:

```html
BF::reset($value = null, $options = null)
@reset($value = null, $options = null)
```

Examples:

```html
@reset()
@reset('Reset form', 'warning btn-sm')
@reset(null, ['id' => 'reset-button'])
@reset(null, ['class' => false])

<!-- Result: -->

<input class="btn btn-danger" type="reset">
<input class="btn btn-warning btn-sm" type="reset" value="Reset form">
<input class="btn btn-danger" id="reset-button" type="reset">
<input type="reset">
```

**Standard button:**

Default style is `primary`.

Signature:

```html
BF::button($value = null, $options = null)
@button($value = null, $options = null)
```

Examples:

```html
@button('Button')
@button('Info', 'info btn-sm')
@button('Submit', ['class' => 'btn btn-primary', 'type' => 'submit'])
@button('Reset', ['class' => 'btn btn-danger', 'type' => 'submit'])
@button('Button', ['class' => false])

<!-- Result: -->

<button class="btn btn-primary" type="button">Button</button>
<button class="btn btn-info btn-sm" type="button">Info</button>
<button class="btn btn-primary" type="submit">Submit</button>
<button class="btn btn-danger" type="reset">Reset</button>
<button type="button">Button</button>
```

**Link button:**

Default style is `primary`.

Signature:

```html
BF::link($url, $title = null, $options = null)
@link($url, $title = null, $options = null)
```

Examples:

```html
@link('#', 'Button')
@link('#', 'Info', 'info btn-sm')
@link('#', 'Submit', ['id' => 'submit-link'])
@link('#', 'Reset', ['class' => 'btn btn-danger'])
@link('#', 'Link', ['class' => false])

<!-- Result: -->

<a href="#" class="btn btn-primary">Button</a>
<a href="#" class="btn btn-info btn-sm">Info</a>
<a href="#" class="btn btn-primary" id="submit-link">Submit</a>
<a href="#" class="btn btn-danger">Reset</a>
<a href="#" >Link</a>
```

## Available methods and directives

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
| BF::link()        | @link()          | Create a link button                                |
