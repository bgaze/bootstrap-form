<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Characterization tests for field rendering under horizontal / inline layouts (Bootstrap 4).
 */
class LayoutTest extends TestCase
{
    public function test_horizontal_text(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::text('login');
        BF::close();

        $expected = '<div id="login_group" class="form-group row">'
            . '<label for="login" class="col-form-label col-lg-2 col-xl-3">Login</label>'
            . '<div class="col"><input id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_horizontal_checkbox_uses_left_spacer(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::checkbox('remember', 'Remember me');
        BF::close();

        $expected = '<div id="remember_group" class="form-group row">'
            . '<div class="col-lg-2 col-xl-3"></div>'
            . '<div class="col"><div class="form-check">'
            . '<input id="remember" class="form-check-input" name="remember" type="checkbox" value="1">'
            . '<label for="remember" class="form-check-label">Remember me</label></div></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_inline_text(): void
    {
        BF::inline(['url' => '/foo']);
        $html = (string) BF::text('login');
        BF::close();

        $expected = '<div id="login_group" class="form-group mr-3 my-1">'
            . '<label for="login" class="mr-2">Login</label>'
            . '<div><input id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_horizontal_text_with_error(): void
    {
        $this->withErrors(['login' => ['The login field is required.']]);

        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::text('login');
        BF::close();

        $expected = '<div id="login_group" class="is-invalid form-group row">'
            . '<label for="login" class="col-form-label col-lg-2 col-xl-3">Login</label>'
            . '<div class="col"><input id="login" class="form-control is-invalid" name="login" type="text">'
            . '<div class="invalid-feedback">The login field is required.</div></div></div>';

        $this->assertSame($expected, $html);
    }
}
