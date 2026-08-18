<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * One channel per element: supplying a class takes over that element's styling.
 *
 * The package then adds only what the version driver requires (component, grid and state
 * classes); every config-sourced class — group_class, hspace, vspace, left_class, lspace —
 * is skipped. group_class itself is configured in the version sections and overridden by
 * styling the group, at form or field level.
 */
class ConfigurableClassesTest extends TestCase
{
    // ## GROUP CLASS ############################################################

    public function test_group_class_defaults_to_the_version_section_value(): void
    {
        $this->assertStringContainsString('<div id="login-group" class="mb-3">', (string) BF::text('login'));

        BF::open(['bootstrap_version' => 4]);
        $html = (string) BF::text('login');
        BF::close();

        $this->assertStringContainsString('<div id="login-group" class="form-group">', $html);
    }

    public function test_group_class_is_overridable_in_the_config(): void
    {
        config()->set('bootstrap_form.bootstrap5.group_class', 'mb-5');

        $html = (string) BF::text('login');

        $this->assertStringContainsString('<div id="login-group" class="mb-5">', $html);
        $this->assertStringNotContainsString('mb-3', $html);
    }

    public function test_a_group_class_replaces_the_default_it_never_adds_to_it(): void
    {
        $html = (string) BF::text('login', null, null, ['group' => ['class' => 'border']]);

        $this->assertStringContainsString('<div class="border" id="login-group">', $html);
        $this->assertStringNotContainsString('mb-3', $html);
    }

    public function test_a_form_level_group_class_covers_every_field(): void
    {
        BF::open(['group' => ['class' => 'mb-4']]);
        $login = (string) BF::text('login');
        $email = (string) BF::text('email');
        BF::close();

        $this->assertStringContainsString('<div class="mb-4" id="login-group">', $login);
        $this->assertStringContainsString('<div class="mb-4" id="email-group">', $email);
        $this->assertStringNotContainsString('mb-3', $login.$email);
    }

    public function test_a_field_group_class_wins_over_the_form_one(): void
    {
        BF::open(['group' => ['class' => 'mb-4']]);
        $html = (string) BF::text('login', null, null, ['group' => ['class' => 'mb-0']]);
        BF::close();

        $this->assertStringContainsString('<div class="mb-0" id="login-group">', $html);
        $this->assertStringNotContainsString('mb-4', $html);
    }

    public function test_a_false_group_class_leaves_the_wrapper_without_any_class(): void
    {
        $html = (string) BF::text('login', null, null, ['group' => ['class' => false]]);

        $this->assertStringContainsString('<div id="login-group"><label', $html);
    }

    public function test_group_false_still_drops_the_wrapper_entirely(): void
    {
        $html = (string) BF::text('login', null, null, ['group' => false]);

        $this->assertStringNotContainsString('login-group', $html);
        $this->assertStringNotContainsString('mb-3', $html);
    }

    public function test_group_class_is_never_emitted_as_an_html_attribute(): void
    {
        $this->assertStringNotContainsString('group_class', (string) BF::text('login'));
    }

    // ## GROUP CLASS × DRIVER CLASSES ###########################################

    /**
     * getErrors() puts is-invalid on the group bag during construction. The "styled by the
     * application" flag is captured before that, so an invalid field keeps its group_class.
     */
    public function test_an_invalid_field_keeps_its_default_group_class(): void
    {
        $this->withErrors(['login' => ['Required.']]);

        $html = (string) BF::text('login');

        $this->assertStringContainsString('<div id="login-group" class="is-invalid mb-3">', $html);
    }

    public function test_an_invalid_styled_field_keeps_its_own_class_and_the_state_class(): void
    {
        $this->withErrors(['login' => ['Required.']]);

        $html = (string) BF::text('login', null, null, ['group' => ['class' => 'border']]);

        $this->assertStringContainsString('<div class="border is-invalid" id="login-group">', $html);
        $this->assertStringNotContainsString('mb-3', $html);
    }

    public function test_the_horizontal_row_class_survives_a_group_class(): void
    {
        BF::horizontal(['url' => '/foo', 'group' => ['class' => 'mb-4']]);
        $html = (string) BF::text('login');
        BF::close();

        $this->assertStringContainsString('<div class="mb-4 row" id="login-group">', $html);
    }

    public function test_a_group_class_takes_over_the_inline_spacing(): void
    {
        BF::inline(['url' => '/foo']);
        $inherited = (string) BF::text('login');
        $styled = (string) BF::text('email', null, null, ['group' => ['class' => 'mb-4']]);
        BF::close();

        $this->assertStringContainsString('<div id="login-group" class="mb-3 me-3 my-1">', $inherited);
        $this->assertStringContainsString('<div class="mb-4" id="email-group">', $styled);
        $this->assertStringNotContainsString('me-3', $styled);
        $this->assertStringNotContainsString('my-1', $styled);
    }

    // ## LABEL CLASS ############################################################

    public function test_a_label_class_takes_over_the_horizontal_column_width(): void
    {
        BF::horizontal(['url' => '/foo']);
        $inherited = (string) BF::text('login');
        $styled = (string) BF::text('email', null, null, ['label' => ['class' => 'col-lg-4']]);
        BF::close();

        $this->assertStringContainsString('<label for="login" class="col-form-label col-lg-2 col-xl-3">', $inherited);
        // What the application writes is what it gets: no competing col-lg-* is appended.
        $this->assertStringContainsString('<label for="email" class="col-lg-4 col-form-label">', $styled);
        $this->assertStringNotContainsString('col-lg-2', $styled);
    }

    public function test_a_label_class_keeps_the_component_class(): void
    {
        $html = (string) BF::text('login', null, null, ['label' => ['class' => 'fw-bold']]);

        $this->assertStringContainsString('<label for="login" class="fw-bold form-label">', $html);
    }

    public function test_a_label_class_takes_over_the_inline_label_spacing(): void
    {
        BF::inline(['url' => '/foo']);
        $html = (string) BF::text('login', null, null, ['label' => ['class' => 'fw-bold']]);
        BF::close();

        $this->assertStringContainsString('<label for="login" class="fw-bold form-label">', $html);
        $this->assertStringNotContainsString('me-2', $html);
    }

    /**
     * pt-0 is driver-owned: it aligns the collection label with its first choice, which is
     * a requirement of the horizontal layout rather than a styling default.
     */
    public function test_the_choice_collection_alignment_class_survives_a_label_class(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::checkboxes('tags', null, ['a' => 'A'], null, ['label' => ['class' => 'fw-bold']]);
        BF::close();

        $this->assertStringContainsString('<label for="tags" class="fw-bold pt-0 col-form-label">', $html);
        $this->assertStringNotContainsString('col-lg-2', $html);
    }

    // ## X-COMPONENT PARITY #####################################################

    public function test_components_project_the_group_and_label_class(): void
    {
        $this->assertSame(
            (string) BF::text('login', null, null, ['group' => ['class' => 'mb-4']]),
            trim(Blade::render('<x-bf::text name="login" group:class="mb-4"/>'))
        );

        $this->assertSame(
            (string) BF::text('login', null, null, ['label' => ['class' => 'fw-bold']]),
            trim(Blade::render('<x-bf::text name="login" label:class="fw-bold"/>'))
        );
    }

    // ## CONTROL CLASS ##########################################################

    /**
     * A false class means "none of mine", not "no class": the driver's component class is a
     * requirement of the markup, not a configured default, so it always survives.
     */
    public function test_a_false_control_class_still_keeps_the_driver_component_class(): void
    {
        $expected = '<div id="q-group" class="mb-3"><label for="q" class="form-label">Search</label>'
            .'<div><input class="form-control" id="q" name="q" type="text"></div></div>';

        $this->assertSame($expected, (string) BF::text('q', 'Search', null, ['class' => false]));
    }

    public function test_a_false_control_class_keeps_the_driver_class_on_select_and_checkable(): void
    {
        $this->assertStringContainsString(
            '<select class="form-select" id="s" name="s">',
            (string) BF::select('s', 'S', ['a' => 'A'], null, ['class' => false]),
        );

        $this->assertStringContainsString(
            '<input class="form-check-input" id="c" name="c" type="checkbox" value="1">',
            (string) BF::checkbox('c', 'C', 1, null, ['class' => false]),
        );
    }

    public function test_a_supplied_control_class_is_added_to_the_driver_class(): void
    {
        $this->assertStringContainsString(
            '<input class="custom form-control" id="q" name="q" type="text">',
            (string) BF::text('q', 'Search', null, ['class' => 'custom']),
        );
    }
}
