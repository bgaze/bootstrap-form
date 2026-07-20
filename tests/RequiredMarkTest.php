<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * Behavior tests for the global "required_mark" option (Bootstrap 5 default).
 *
 * The mark is appended to the label of any field carrying the HTML "required" attribute.
 * Assertions target the label fragment (not the whole markup) so they stay robust to
 * attribute ordering; byte-exact rendering is locked by the golden snapshots.
 */
class RequiredMarkTest extends TestCase
{
    // ## TRIGGER (the "required" attribute, in all its forms) ###################

    public function test_no_mark_when_not_required(): void
    {
        $html = (string) BF::text('email', 'Email');

        $this->assertStringContainsString('<label for="email" class="form-label">Email</label>', $html);
        $this->assertStringNotContainsString('Email *', $html);
    }

    public function test_default_mark_when_required_true(): void
    {
        $html = (string) BF::text('email', 'Email', null, ['required' => true]);

        $this->assertStringContainsString('<label for="email" class="form-label">Email *</label>', $html);
    }

    public function test_mark_when_required_string(): void
    {
        $html = (string) BF::text('email', 'Email', null, ['required' => 'required']);

        $this->assertStringContainsString('<label for="email" class="form-label">Email *</label>', $html);
    }

    public function test_mark_when_required_is_a_bare_attribute(): void
    {
        $html = (string) BF::text('email', 'Email', null, ['required']);

        $this->assertStringContainsString('<label for="email" class="form-label">Email *</label>', $html);
    }

    public function test_required_false_does_not_trigger_the_mark(): void
    {
        $html = (string) BF::text('email', 'Email', null, ['required' => false]);

        $this->assertStringContainsString('<label for="email" class="form-label">Email</label>', $html);
        $this->assertStringNotContainsString('Email *', $html);
    }

    // ## MARK VALUE (HTML, overrides, disabling) ################################

    public function test_html_mark_is_rendered_verbatim(): void
    {
        $html = (string) BF::text('email', 'Email', null, [
            'required' => true,
            'required_mark' => ' <span class="text-danger">*</span>',
        ]);

        $this->assertStringContainsString(
            '<label for="email" class="form-label">Email <span class="text-danger">*</span></label>',
            $html
        );
    }

    public function test_per_field_mark_override(): void
    {
        $html = (string) BF::text('email', 'Email', null, ['required' => true, 'required_mark' => ' (required)']);

        $this->assertStringContainsString('<label for="email" class="form-label">Email (required)</label>', $html);
    }

    public function test_per_field_false_disables_the_mark(): void
    {
        $html = (string) BF::text('email', 'Email', null, ['required' => true, 'required_mark' => false]);

        $this->assertStringContainsString('<label for="email" class="form-label">Email</label>', $html);
        $this->assertStringNotContainsString('Email *', $html);
    }

    public function test_per_form_mark_override_is_inherited(): void
    {
        BF::open(['url' => '/x', 'required_mark' => ' (obligatoire)']);
        $html = (string) BF::text('email', 'Email', null, ['required' => true]);
        BF::close();

        $this->assertStringContainsString('<label for="email" class="form-label">Email (obligatoire)</label>', $html);
    }

    public function test_per_form_false_disables_the_mark(): void
    {
        BF::open(['url' => '/x', 'required_mark' => false]);
        $html = (string) BF::text('email', 'Email', null, ['required' => true]);
        BF::close();

        $this->assertStringContainsString('<label for="email" class="form-label">Email</label>', $html);
        $this->assertStringNotContainsString('Email *', $html);
    }

    // ## SMART DISPLAY (collections mark the global label only) #################

    public function test_checkboxes_marks_the_global_label_not_the_choices(): void
    {
        $html = (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', 'editor' => 'Editor'], null, ['required' => true]);

        $this->assertStringContainsString('<label for="roles" class="form-label">Roles *</label>', $html);
        $this->assertStringNotContainsString('Admin *', $html);
        $this->assertStringNotContainsString('Editor *', $html);
    }

    public function test_radios_marks_the_global_label_not_the_choices(): void
    {
        $html = (string) BF::radios('gender', 'Gender', ['m' => 'Male', 'f' => 'Female'], null, ['required' => true]);

        $this->assertStringContainsString('<label for="gender" class="form-label">Gender *</label>', $html);
        $this->assertStringNotContainsString('Male *', $html);
        $this->assertStringNotContainsString('Female *', $html);
    }

    public function test_single_checkbox_gets_the_mark_on_its_label(): void
    {
        $html = (string) BF::checkbox('accept', 'I accept', 1, null, ['required' => true]);

        $this->assertStringContainsString('<label for="accept" class="form-check-label">I accept *</label>', $html);
    }

    public function test_single_radio_gets_the_mark_on_its_label(): void
    {
        $html = (string) BF::radio('gender', 'Male', 'm', null, ['required' => true]);

        $this->assertStringContainsString('<label for="gender" class="form-check-label">Male *</label>', $html);
    }

    // ## COMPONENT PARITY (the `required` boolean projects identically) #########

    public function test_required_text_component_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::text('email', 'Email', null, ['required' => true]),
            trim(Blade::render('<x-bf::text name="email" label="Email" required/>'))
        );
    }

    public function test_required_checkboxes_component_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', 'editor' => 'Editor'], null, ['required' => true]),
            trim(Blade::render('<x-bf::checkboxes name="roles" label="Roles" :choices="[\'admin\' => \'Admin\', \'editor\' => \'Editor\']" required/>'))
        );
    }
}
