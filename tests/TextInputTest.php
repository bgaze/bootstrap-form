<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Characterization tests for the TextInput family (Bootstrap 4).
 */
class TextInputTest extends TestCase
{
    /**
     * All simple types share the TextInput path; only the `type` attribute differs.
     */
    public static function simpleTypes(): array
    {
        return [
            'text' => ['text', 'text'],
            'email' => ['email', 'email'],
            'url' => ['url', 'url'],
            'tel' => ['tel', 'tel'],
            'number' => ['number', 'number'],
            'date' => ['date', 'date'],
            'time' => ['time', 'time'],
            'color' => ['color', 'color'],
        ];
    }

    #[DataProvider('simpleTypes')]
    public function test_simple_type_input(string $method, string $type): void
    {
        $expected = '<div id="field-group" class="form-group"><label for="field">Field</label>'
            . '<div><input id="field" class="form-control" name="field" type="' . $type . '"></div></div>';

        $this->assertSame($expected, (string) BF::{$method}('field'));
    }

    public function test_label_and_value(): void
    {
        $expected = '<div id="login-group" class="form-group"><label for="login">Your login</label>'
            . '<div><input id="login" class="form-control" name="login" type="text" value="john"></div></div>';

        $this->assertSame($expected, (string) BF::text('login', 'Your login', 'john'));
    }

    public function test_label_false_omits_label(): void
    {
        $expected = '<div id="login-group" class="form-group">'
            . '<div><input id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, (string) BF::text('login', false));
    }

    public function test_help_text(): void
    {
        $expected = '<div id="login-group" class="form-group"><label for="login">Login</label>'
            . '<div><input id="login" class="form-control" aria-describedby="login-help" name="login" type="text">'
            . '<small id="login-help" class="form-text">Some help</small></div></div>';

        $this->assertSame($expected, (string) BF::text('login', null, null, ['help' => 'Some help']));
    }

    public function test_size_small(): void
    {
        $expected = '<div id="login-group" class="form-group"><label for="login">Login</label>'
            . '<div><input id="login" class="form-control form-control-sm" name="login" type="text"></div></div>';

        $this->assertSame($expected, (string) BF::text('login', null, null, ['size' => 'sm']));
    }

    public function test_prepend_and_append_build_input_group(): void
    {
        $expected = '<div id="amount-group" class="form-group"><label for="amount">Amount</label>'
            . '<div><div class="input-group"><div class="input-group-prepend">$</div>'
            . '<input id="amount" class="form-control" name="amount" type="text">'
            . '<div class="input-group-append">.00</div></div></div></div>';

        $this->assertSame($expected, (string) BF::text('amount', null, null, ['prepend' => '$', 'append' => '.00']));
    }

    public function test_textarea(): void
    {
        $expected = '<div id="bio-group" class="form-group"><label for="bio">Bio</label>'
            . '<div><textarea id="bio" class="form-control" name="bio" cols="50" rows="10"></textarea></div></div>';

        $this->assertSame($expected, (string) BF::textarea('bio'));
    }

    public function test_password(): void
    {
        $expected = '<div id="secret-group" class="form-group"><label for="secret">Secret</label>'
            . '<div><input id="secret" class="form-control" name="secret" type="password" value=""></div></div>';

        $this->assertSame($expected, (string) BF::password('secret'));
    }

    public function test_validation_error_adds_invalid_classes_and_feedback(): void
    {
        $this->withErrors(['login' => ['The login field is required.']]);

        $expected = '<div id="login-group" class="is-invalid form-group"><label for="login">Login</label>'
            . '<div><input id="login" class="form-control is-invalid" aria-describedby="login-error" aria-invalid="true" name="login" type="text">'
            . '<div class="invalid-feedback" id="login-error">The login field is required.</div></div></div>';

        $this->assertSame($expected, (string) BF::text('login'));
    }
}
