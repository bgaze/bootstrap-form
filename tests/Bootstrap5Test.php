<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Rendering tests for Bootstrap 5 (bootstrap_version = 5).
 */
class Bootstrap5Test extends TestCase
{
    protected function defineEnvironment($app)
    {
        $app['config']->set('bootstrap_form.bootstrap_version', 5);
    }

    public function test_open_horizontal_has_no_layout_class(): void
    {
        $html = BF::horizontal(['url' => '/foo']);

        $this->assertStringStartsWith(
            '<form method="POST" action="http://localhost/foo" accept-charset="UTF-8" role="form">',
            $html
        );
        $this->assertStringNotContainsString('form-horizontal', $html);
    }

    public function test_open_inline_has_no_layout_class(): void
    {
        $html = BF::inline(['url' => '/foo']);

        $this->assertStringNotContainsString('form-inline', $html);
    }

    public function test_text_uses_mb3_and_form_label(): void
    {
        $expected = '<div id="login_group" class="mb-3"><label for="login" class="form-label">Login</label>'
            . '<div><input id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, (string) BF::text('login'));
    }

    public function test_text_help(): void
    {
        $expected = '<div id="login_group" class="mb-3"><label for="login" class="form-label">Login</label>'
            . '<div><input id="login" class="form-control" name="login" type="text">'
            . '<small class="form-text">Some help</small></div></div>';

        $this->assertSame($expected, (string) BF::text('login', null, null, ['help' => 'Some help']));
    }

    public function test_text_size_small(): void
    {
        $expected = '<div id="login_group" class="mb-3"><label for="login" class="form-label">Login</label>'
            . '<div><input id="login" class="form-control form-control-sm" name="login" type="text"></div></div>';

        $this->assertSame($expected, (string) BF::text('login', null, null, ['size' => 'sm']));
    }

    public function test_input_group_has_no_prepend_append_wrappers(): void
    {
        $expected = '<div id="amount_group" class="mb-3"><label for="amount" class="form-label">Amount</label>'
            . '<div><div class="input-group"><span class="input-group-text">$</span>'
            . '<input id="amount" class="form-control" name="amount" type="text">'
            . '<span class="input-group-text">.00</span></div></div></div>';

        $html = (string) BF::text('amount', null, null, [
            'prepend' => '<span class="input-group-text">$</span>',
            'append' => '<span class="input-group-text">.00</span>',
        ]);

        $this->assertSame($expected, $html);
        $this->assertStringNotContainsString('input-group-prepend', $html);
        $this->assertStringNotContainsString('input-group-append', $html);
    }

    public function test_textarea(): void
    {
        $expected = '<div id="bio_group" class="mb-3"><label for="bio" class="form-label">Bio</label>'
            . '<div><textarea id="bio" class="form-control" name="bio" cols="50" rows="10"></textarea></div></div>';

        $this->assertSame($expected, (string) BF::textarea('bio'));
    }

    public function test_select_uses_form_select(): void
    {
        $expected = '<div id="country_group" class="mb-3"><label for="country" class="form-label">Country</label>'
            . '<div><select id="country" class="form-select" name="country">'
            . '<option value="fr">France</option></select></div></div>';

        $this->assertSame($expected, (string) BF::select('country', null, ['fr' => 'France']));
    }

    public function test_select_custom_is_ignored_and_not_leaked(): void
    {
        $html = (string) BF::select('country', null, ['fr' => 'France'], null, ['custom' => true]);

        $this->assertStringContainsString('class="form-select"', $html);
        $this->assertStringNotContainsString('custom-select', $html);
        $this->assertStringNotContainsString('<select custom', $html);
    }

    public function test_select_size_large(): void
    {
        $expected = '<div id="country_group" class="mb-3"><label for="country" class="form-label">Country</label>'
            . '<div><select id="country" class="form-select form-select-lg" name="country">'
            . '<option value="fr">France</option></select></div></div>';

        $this->assertSame($expected, (string) BF::select('country', null, ['fr' => 'France'], null, ['size' => 'lg']));
    }

    public function test_checkbox_basic(): void
    {
        $expected = '<div id="remember_group" class="mb-3"><div><div class="form-check">'
            . '<input id="remember" class="form-check-input" name="remember" type="checkbox" value="1">'
            . '<label for="remember" class="form-check-label">Remember me</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('remember', 'Remember me'));
    }

    public function test_checkbox_custom_falls_back_to_form_check(): void
    {
        $html = (string) BF::checkbox('remember', 'Remember me', 1, null, ['custom' => true]);

        $this->assertStringContainsString('<div class="form-check">', $html);
        $this->assertStringNotContainsString('custom-control', $html);
        $this->assertStringNotContainsString('<input custom', $html);
    }

    public function test_switch_uses_form_switch_and_role(): void
    {
        $expected = '<div id="remember_group" class="mb-3"><div><div class="form-check form-switch">'
            . '<input id="remember" class="form-check-input" role="switch" name="remember" type="checkbox" value="1">'
            . '<label for="remember" class="form-check-label">Remember me</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('remember', 'Remember me', 1, null, ['switch' => true]));
    }

    public function test_checkbox_inline(): void
    {
        $expected = '<div id="remember_group" class="mb-3"><div><div class="form-check form-check-inline">'
            . '<input id="remember" class="form-check-input" name="remember" type="checkbox" value="1">'
            . '<label for="remember" class="form-check-label">Remember me</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('remember', 'Remember me', 1, null, ['inline' => true]));
    }

    public function test_checkbox_label_false(): void
    {
        $expected = '<div id="remember_group" class="mb-3"><div><div class="form-check">'
            . '<input id="remember" class="form-check-input position-static" name="remember" type="checkbox" value="1">'
            . '</div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('remember', false));
    }

    public function test_radios_collection(): void
    {
        $expected = '<div id="tags_group" class="mb-3"><label for="tags" class="form-label">Tags</label><div>'
            . '<div class="form-check"><input id="tags_a" class="form-check-input" name="tags" type="checkbox" value="a">'
            . '<label for="tags_a" class="form-check-label">A</label></div>'
            . '<div class="form-check"><input id="tags_b" class="form-check-input" name="tags" type="checkbox" value="b">'
            . '<label for="tags_b" class="form-check-label">B</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkboxes('tags', null, ['a' => 'A', 'b' => 'B']));
    }

    public function test_file_uses_form_control(): void
    {
        $expected = '<div id="avatar_group" class="mb-3"><label for="avatar" class="form-label">Avatar</label>'
            . '<div><input id="avatar" class="form-control" name="avatar" type="file"></div></div>';

        $this->assertSame($expected, (string) BF::file('avatar'));
    }

    public function test_file_custom_falls_back_to_form_control(): void
    {
        $html = (string) BF::file('avatar', null, ['custom' => true]);

        $this->assertStringContainsString('class="form-control"', $html);
        $this->assertStringNotContainsString('custom-file', $html);
        $this->assertStringNotContainsString('<input custom', $html);
    }

    public function test_range_uses_form_range(): void
    {
        $expected = '<div id="volume_group" class="mb-3"><label for="volume" class="form-label">Volume</label>'
            . '<div><input id="volume" class="form-range" name="volume" type="range"></div></div>';

        $this->assertSame($expected, (string) BF::range('volume'));
    }

    public function test_button_class_unchanged(): void
    {
        $this->assertSame('<input class="btn btn-primary" type="submit" value="Save">', (string) BF::submit('Save'));
    }

    public function test_horizontal_text(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::text('login');
        BF::close();

        $expected = '<div id="login_group" class="mb-3 row">'
            . '<label for="login" class="col-form-label col-lg-2 col-xl-3">Login</label>'
            . '<div class="col"><input id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_horizontal_checkbox_uses_left_spacer(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::checkbox('remember', 'Remember me');
        BF::close();

        $expected = '<div id="remember_group" class="mb-3 row">'
            . '<div class="col-lg-2 col-xl-3"></div>'
            . '<div class="col"><div class="form-check">'
            . '<input id="remember" class="form-check-input" name="remember" type="checkbox" value="1">'
            . '<label for="remember" class="form-check-label">Remember me</label></div></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_inline_text_uses_me_spacing(): void
    {
        BF::inline(['url' => '/foo']);
        $html = (string) BF::text('login');
        BF::close();

        $expected = '<div id="login_group" class="mb-3 me-3 my-1">'
            . '<label for="login" class="form-label me-2">Login</label>'
            . '<div><input id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_validation_error(): void
    {
        $this->withErrors(['login' => ['The login field is required.']]);

        $expected = '<div id="login_group" class="is-invalid mb-3"><label for="login" class="form-label">Login</label>'
            . '<div><input id="login" class="form-control is-invalid" name="login" type="text">'
            . '<div class="invalid-feedback">The login field is required.</div></div></div>';

        $this->assertSame($expected, (string) BF::text('login'));
    }
}
