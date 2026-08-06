<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Characterization tests for field rendering under horizontal / inline layouts (Bootstrap 4).
 */
class LayoutTest extends Bootstrap4TestCase
{
    public function test_horizontal_text(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::text('login');
        BF::close();

        $expected = '<div id="login-group" class="form-group row">'
            .'<label for="login" class="col-form-label col-lg-2 col-xl-3">Login</label>'
            .'<div class="col"><input id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_horizontal_checkbox_uses_left_spacer(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::checkbox('remember', 'Remember me');
        BF::close();

        $expected = '<div id="remember-group" class="form-group row">'
            .'<div class="col-lg-2 col-xl-3"></div>'
            .'<div class="col"><div class="form-check">'
            .'<input id="remember" class="form-check-input" name="remember" type="checkbox" value="1">'
            .'<label for="remember" class="form-check-label">Remember me</label></div></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_horizontal_choice_collection_label_is_top_aligned(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::checkboxes('tags', null, ['a' => 'A', 'b' => 'B']);
        BF::close();

        $expected = '<div id="tags-group" class="form-group row">'
            .'<label for="tags" class="pt-0 col-form-label col-lg-2 col-xl-3">Tags</label>'
            .'<div class="col">'
            .'<div class="form-check"><input id="tags-a" class="form-check-input" name="tags" type="checkbox" value="a">'
            .'<label for="tags-a" class="form-check-label">A</label></div>'
            .'<div class="form-check"><input id="tags-b" class="form-check-input" name="tags" type="checkbox" value="b">'
            .'<label for="tags-b" class="form-check-label">B</label></div></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_inline_text(): void
    {
        BF::inline(['url' => '/foo']);
        $html = (string) BF::text('login');
        BF::close();

        $expected = '<div id="login-group" class="form-group mr-3 my-1">'
            .'<label for="login" class="mr-2">Login</label>'
            .'<div><input id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_horizontal_text_with_error(): void
    {
        $this->withErrors(['login' => ['The login field is required.']]);

        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::text('login');
        BF::close();

        $expected = '<div id="login-group" class="is-invalid form-group row">'
            .'<label for="login" class="col-form-label col-lg-2 col-xl-3">Login</label>'
            .'<div class="col"><input id="login" class="form-control is-invalid" aria-describedby="login-error" aria-invalid="true" name="login" type="text">'
            .'<div class="invalid-feedback" id="login-error">The login field is required.</div></div></div>';

        $this->assertSame($expected, $html);
    }
}
