<?php

namespace Bgaze\BootstrapForm\Support;

/**
 * @method static bool isFormOpened() Check if a form is currently opened
 * @method static \Illuminate\Support\Collection settings() Get inheritable forms settings.
 * @method static \Bgaze\BootstrapForm\Bootstrap\Form|null form() Get currently opened form.
 * @method static string open(array $options = []) Open a form.
 * @method static string close() Reset and close the form.
 * @method static string vertical(array $options = []) Open a vertical Bootstrap form.
 * @method static string inline(array $options = []) Open an inline Bootstrap form.
 * @method static string horizontal(array $options = []) Open a horizontal Bootstrap form.
 * @method static string input(string $type, string $name,  $label = null,  $value = null, array $options = []) Create a Bootstrap input by tag.
 * @method static string text(string $name,  $label = null,  $value = null, array $options = []) Create a Bootstrap text input.
 * @method static string email(string $name = 'email',  $label = null,  $value = null, array $options = []) Create a Bootstrap email input.
 * @method static string url(string $name,  $label = null,  $value = null, array $options = []) Create a Bootstrap URL input.
 * @method static string tel(string $name,  $label = null,  $value = null, array $options = []) Create a Bootstrap tel input.
 * @method static string number(string $name,  $label = null,  $value = null, array $options = []) Create a Bootstrap number input.
 * @method static string date(string $name,  $label = null,  $value = null, array $options = []) Create a Bootstrap date input.
 * @method static string time(string $name,  $label = null,  $value = null, array $options = []) Create a Bootstrap time input.
 * @method static string password(string $name,  $label = null, array $options = []) Create a Bootstrap password input.
 * @method static string color(string $name,  $label = null,  $value = null, array $options = []) Create a Bootstrap color input.
 * @method static string textarea(string $name,  $label = null,  $value = null, array $options = []) Create a Bootstrap textarea input.
 * @method static string select(string $name,  $label = null, \Illuminate\Contracts\Support\Arrayable|array $choices = [],  $selected = null, array $options = []) Create a select box field.
 * @method static string file(string $name,  $label = null, array $options = []) Create a Boostrap file upload button.
 * @method static string range(string $name,  $label = null,  $value = null, array $options = []) Create a Boostrap file upload button.
 * @method static string checkbox(string $name,  $label = null,  $value = 1,  $checked = null, array $options = []) Create a Bootstrap checkbox input.
 * @method static string checkboxes(string $name,  $label = null, \Illuminate\Contracts\Support\Arrayable|array $choices = [],  $checked = null, array $options = []) Create a collection of Bootstrap checkboxes.
 * @method static string radio(string $name,  $label = null,  $value = null,  $checked = null, array $options = []) Create a Bootstrap radio input.
 * @method static string radios(string $name,  $label = null, \Illuminate\Contracts\Support\Arrayable|array $choices = [],  $checked = null, array $options = []) Create a collection of Bootstrap radio inputs.
 * @method static string hidden(string $name,  $value = null, array $options = []) Create a hidden field.
 * @method static string label(string $for,  $content = null, array $options = []) Create a Bootstrap label.
 * @method static string submit( $value = null, array|string|null $options = null) Create a Boostrap submit input.
 * @method static string reset( $value = null, array|string|null $options = null) Create a Boostrap reset input.
 * @method static string button( $content = null, array|string|null $options = null) Create a Boostrap button.
 * @method static string link(string $href,  $content = null, array|string|null $options = null) Create a Boostrap link button.
 * @method static macro( $name,  $macro) Register a custom macro.
 * @method static mixin( $mixin,  $replace = true) Mix another object into the class.
 * @method static hasMacro( $name) Checks if macro is registered.
 * @method static flushMacros() Flush the existing macros.
 * @see \Bgaze\BootstrapForm\BootstrapForm
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bootstrap_form';
    }
}
