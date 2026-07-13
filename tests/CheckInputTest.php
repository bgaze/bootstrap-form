<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Characterization tests for CheckInput / CheckChoice (Bootstrap 4).
 */
class CheckInputTest extends TestCase
{
    public function test_checkbox_basic(): void
    {
        $expected = '<div id="remember-group" class="form-group"><div><div class="form-check">'
            .'<input id="remember" class="form-check-input" name="remember" type="checkbox" value="1">'
            .'<label for="remember" class="form-check-label">Remember me</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('remember', 'Remember me'));
    }

    public function test_checkbox_custom(): void
    {
        $expected = '<div id="remember-group" class="form-group"><div><div class="custom-control custom-checkbox">'
            .'<input id="remember" class="custom-control-input" name="remember" type="checkbox" value="1">'
            .'<label for="remember" class="custom-control-label">Remember me</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('remember', 'Remember me', 1, null, ['custom' => true]));
    }

    public function test_checkbox_switch(): void
    {
        $expected = '<div id="remember-group" class="form-group"><div><div class="custom-control custom-switch">'
            .'<input id="remember" class="custom-control-input" name="remember" type="checkbox" value="1">'
            .'<label for="remember" class="custom-control-label">Remember me</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('remember', 'Remember me', 1, null, ['switch' => true]));
    }

    public function test_checkbox_inline(): void
    {
        $expected = '<div id="remember-group" class="form-group"><div><div class="form-check form-check-inline">'
            .'<input id="remember" class="form-check-input" name="remember" type="checkbox" value="1">'
            .'<label for="remember" class="form-check-label">Remember me</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('remember', 'Remember me', 1, null, ['inline' => true]));
    }

    public function test_checkbox_label_false_adds_position_static(): void
    {
        $expected = '<div id="remember-group" class="form-group"><div><div class="form-check">'
            .'<input id="remember" class="form-check-input position-static" name="remember" type="checkbox" value="1">'
            .'</div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('remember', false));
    }

    public function test_radio_basic(): void
    {
        $expected = '<div id="gender-group" class="form-group"><div><div class="form-check">'
            .'<input id="gender" class="form-check-input" name="gender" type="radio" value="m">'
            .'<label for="gender" class="form-check-label">Male</label></div></div></div>';

        $this->assertSame($expected, (string) BF::radio('gender', 'Male', 'm'));
    }

    public function test_checkboxes_collection(): void
    {
        $expected = '<div id="tags-group" class="form-group"><label for="tags">Tags</label><div>'
            .'<div class="form-check"><input id="tags-a" class="form-check-input" name="tags" type="checkbox" value="a">'
            .'<label for="tags-a" class="form-check-label">A</label></div>'
            .'<div class="form-check"><input id="tags-b" class="form-check-input" name="tags" type="checkbox" value="b">'
            .'<label for="tags-b" class="form-check-label">B</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkboxes('tags', null, ['a' => 'A', 'b' => 'B']));
    }

    public function test_radios_collection(): void
    {
        $expected = '<div id="gender-group" class="form-group"><label for="gender">Gender</label><div>'
            .'<div class="form-check"><input id="gender-m" class="form-check-input" name="gender" type="radio" value="m">'
            .'<label for="gender-m" class="form-check-label">Male</label></div>'
            .'<div class="form-check"><input id="gender-f" class="form-check-input" name="gender" type="radio" value="f">'
            .'<label for="gender-f" class="form-check-label">Female</label></div></div></div>';

        $this->assertSame($expected, (string) BF::radios('gender', null, ['m' => 'Male', 'f' => 'Female']));
    }

    public function test_checkboxes_collection_with_error(): void
    {
        $this->withErrors(['login' => ['The login field is required.']]);

        $expected = '<div id="login-group" class="is-invalid form-group"><label for="login">Login</label><div>'
            .'<div class="form-check"><input id="login-a" class="is-invalid form-check-input" aria-invalid="true" name="login" type="checkbox" value="a">'
            .'<label for="login-a" class="form-check-label">A</label></div>'
            .'<div class="invalid-feedback d-block" id="login-error">The login field is required.</div></div></div>';

        $this->assertSame($expected, (string) BF::checkboxes('login', null, ['a' => 'A']));
    }
}
