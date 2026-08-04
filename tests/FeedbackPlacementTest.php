<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Placement invariant for the trailing blocks of a form group: help text, error message
 * and valid feedback are each emitted EXACTLY ONCE, whatever the field type.
 *
 * Regression guard: CheckInput overrides inputGroup() to build its .form-check wrapper and
 * used to emit those blocks itself while the base class re-emitted them from
 * rightGroupColumn() — duplicating the markup AND the element ids.
 *
 * Placement rule asserted here:
 * - validation feedback lives INSIDE the .form-check wrapper, as a sibling of the input,
 *   because Bootstrap only displays it through `.is-invalid ~ .invalid-feedback` (so no
 *   d-block is needed);
 * - the help text lives AFTER the wrapper, full width, like every other field type.
 *
 * Runs on the Bootstrap 5 default; the Bootstrap 4 path is covered through a per-field
 * version override.
 */
class FeedbackPlacementTest extends TestCase
{
    /**
     * Assert a needle appears exactly once in the rendered markup.
     */
    private function assertRenderedOnce(string $needle, string $html): void
    {
        $this->assertSame(1, substr_count($html, $needle), "expected exactly one '{$needle}' in: {$html}");
    }

    // ## HELP ###################################################################

    public function test_checkbox_help_is_rendered_once_after_the_wrapper(): void
    {
        $expected = '<div id="demo-group" class="mb-3"><div><div class="form-check">'
            .'<input id="demo" class="form-check-input" aria-describedby="demo-help" name="demo" type="checkbox" value="1">'
            .'<label for="demo" class="form-check-label">Label</label></div>'
            .'<small id="demo-help" class="form-text">Some help</small></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('demo', 'Label', 1, false, ['help' => 'Some help']));
    }

    public function test_switch_help_is_rendered_once(): void
    {
        $html = (string) BF::checkbox('demo', 'Label', 1, false, ['help' => 'Some help', 'switch' => true]);

        $this->assertStringContainsString('class="form-check form-switch"', $html);
        $this->assertRenderedOnce('id="demo-help"', $html);
    }

    public function test_inline_checkbox_help_is_rendered_once(): void
    {
        $html = (string) BF::checkbox('demo', 'Label', 1, false, ['help' => 'Some help', 'inline' => true]);

        $this->assertStringContainsString('class="form-check form-check-inline"', $html);
        $this->assertRenderedOnce('id="demo-help"', $html);
    }

    public function test_radio_help_is_rendered_once(): void
    {
        $html = (string) BF::radio('gender', 'Male', 'm', false, ['help' => 'Some help']);

        $this->assertRenderedOnce('id="gender-help"', $html);
    }

    public function test_horizontal_checkbox_help_is_rendered_once(): void
    {
        BF::horizontal(['url' => '/x']);
        $html = (string) BF::checkbox('demo', 'Label', 1, false, ['help' => 'Some help']);
        BF::close();

        $this->assertRenderedOnce('id="demo-help"', $html);
    }

    public function test_labelless_custom_checkbox_keeps_its_empty_label_and_a_single_help(): void
    {
        $html = (string) BF::checkbox('demo', false, 1, false, ['help' => 'Some help', 'custom' => true]);

        $this->assertStringContainsString('<label for="demo" class="form-check-label"></label>', $html);
        $this->assertRenderedOnce('id="demo-help"', $html);
    }

    public function test_bootstrap4_checkbox_help_is_rendered_once(): void
    {
        $expected = '<div id="demo-group" class="form-group"><div><div class="form-check">'
            .'<input id="demo" class="form-check-input" aria-describedby="demo-help" name="demo" type="checkbox" value="1">'
            .'<label for="demo" class="form-check-label">Label</label></div>'
            .'<small id="demo-help" class="form-text">Some help</small></div></div>';

        $html = (string) BF::checkbox('demo', 'Label', 1, false, ['bootstrap_version' => 4, 'help' => 'Some help']);

        $this->assertSame($expected, $html);
    }

    // ## VALIDATION FEEDBACK ####################################################

    public function test_checkbox_error_is_rendered_once_inside_the_wrapper(): void
    {
        $this->withErrors(['demo' => ['The demo field is required.']]);

        $expected = '<div id="demo-group" class="is-invalid mb-3"><div><div class="form-check">'
            .'<input id="demo" class="form-check-input is-invalid" aria-describedby="demo-error" aria-invalid="true" name="demo" type="checkbox" value="1">'
            .'<label for="demo" class="form-check-label">Label</label>'
            .'<div class="invalid-feedback" id="demo-error">The demo field is required.</div></div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('demo', 'Label', 1, false));
    }

    public function test_checkbox_error_and_help_are_each_rendered_once(): void
    {
        $this->withErrors(['demo' => ['The demo field is required.']]);

        $expected = '<div id="demo-group" class="is-invalid mb-3"><div><div class="form-check">'
            .'<input id="demo" class="form-check-input is-invalid" aria-describedby="demo-error demo-help" aria-invalid="true" name="demo" type="checkbox" value="1">'
            .'<label for="demo" class="form-check-label">Label</label>'
            .'<div class="invalid-feedback" id="demo-error">The demo field is required.</div></div>'
            .'<small id="demo-help" class="form-text">Some help</small></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('demo', 'Label', 1, false, ['help' => 'Some help']));
    }

    public function test_checkbox_valid_feedback_is_rendered_once_inside_the_wrapper(): void
    {
        $this->withErrors(['other' => ['err']]);

        $expected = '<div id="demo-group" class="is-valid mb-3"><div><div class="form-check">'
            .'<input id="demo" class="form-check-input is-valid" aria-describedby="demo-valid" aria-invalid="false" name="demo" type="checkbox" value="1">'
            .'<label for="demo" class="form-check-label">Label</label>'
            .'<div class="valid-feedback" id="demo-valid">Looks good!</div></div></div></div>';

        $html = (string) BF::checkbox('demo', 'Label', 1, false, ['show_valid_feedback' => true, 'success' => 'Looks good!']);

        $this->assertSame($expected, $html);
    }

    public function test_disable_errors_suppresses_the_feedback_of_a_standalone_check(): void
    {
        $this->withErrors(['demo' => ['The demo field is required.']]);

        // The group still carries the invalid state, but no feedback element is emitted —
        // consistent with rendersOwnFeedback(), which also skips the aria wiring.
        $expected = '<div id="demo-group" class="is-invalid mb-3"><div><div class="form-check">'
            .'<input id="demo" class="form-check-input is-invalid" aria-invalid="true" name="demo" type="checkbox" value="1">'
            .'<label for="demo" class="form-check-label">Label</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('demo', 'Label', 1, false, ['disable_errors' => true]));
    }

    // ## COLLECTIONS & OTHER inputGroup() OVERRIDES #############################

    public function test_choice_collection_renders_its_help_once_after_the_children(): void
    {
        $html = (string) BF::checkboxes('roles', 'Roles', ['a' => 'A', 'b' => 'B'], null, ['help' => 'Some help']);

        $expected = '<div id="roles-group" class="mb-3"><label for="roles" class="form-label">Roles</label><div>'
            .'<div class="form-check"><input id="roles-a" class="form-check-input" name="roles" type="checkbox" value="a">'
            .'<label for="roles-a" class="form-check-label">A</label></div>'
            .'<div class="form-check"><input id="roles-b" class="form-check-input" name="roles" type="checkbox" value="b">'
            .'<label for="roles-b" class="form-check-label">B</label></div>'
            .'<small id="roles-help" class="form-text">Some help</small></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_choice_collection_error_stays_rendered_once_as_a_block(): void
    {
        $this->withErrors(['roles' => ['Pick a role.']]);

        $html = (string) BF::checkboxes('roles', 'Roles', ['a' => 'A'], null, ['help' => 'Some help']);

        $this->assertRenderedOnce('id="roles-error"', $html);
        $this->assertRenderedOnce('id="roles-help"', $html);
        $this->assertStringContainsString('class="invalid-feedback d-block" id="roles-error"', $html);
    }

    public function test_file_input_help_is_rendered_once(): void
    {
        $this->assertRenderedOnce('id="doc-help"', (string) BF::file('doc', 'Doc', ['help' => 'Some help']));
    }

    public function test_select_help_is_rendered_once(): void
    {
        $this->assertRenderedOnce('id="sel-help"', (string) BF::select('sel', 'Sel', ['a' => 'A'], null, ['help' => 'Some help']));
    }

    public function test_range_help_is_rendered_once(): void
    {
        $this->assertRenderedOnce('id="vol-help"', (string) BF::range('vol', 'Vol', null, ['help' => 'Some help']));
    }
}
