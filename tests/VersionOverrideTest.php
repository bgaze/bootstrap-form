<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * The version mechanism: default is Bootstrap 5, with Bootstrap 4 available for
 * backward compatibility, overridable per form and per field.
 * No `bootstrap_version` config override here, so the package default (5) applies.
 */
class VersionOverrideTest extends TestCase
{
    public function test_default_is_bootstrap5(): void
    {
        $html = (string) BF::text('login');

        $this->assertStringContainsString('class="mb-3"', $html);
        $this->assertStringContainsString('class="form-label"', $html);
        $this->assertStringNotContainsString('form-group', $html);
    }

    public function test_per_form_override_switches_to_bootstrap4(): void
    {
        BF::open(['bootstrap_version' => 4]);
        $html = (string) BF::text('login');
        BF::close();

        $this->assertStringContainsString('class="form-group"', $html);
        $this->assertStringNotContainsString('mb-3', $html);
        $this->assertStringNotContainsString('form-label', $html);
    }

    public function test_fields_after_close_revert_to_default(): void
    {
        BF::open(['bootstrap_version' => 4]);
        BF::close();

        $html = (string) BF::text('login');

        $this->assertStringContainsString('class="mb-3"', $html);
        $this->assertStringNotContainsString('form-group', $html);
    }

    /**
     * A per-field override switches the driver — hence the component classes — but layout
     * settings stay inherited from the form. group_class is such a setting (it lives in the
     * version sections of the config), so the field keeps the form's value: pin the version
     * at form level, or pass group_class alongside, to get the other version's group class.
     */
    public function test_per_field_override_switches_component_classes_only(): void
    {
        $html = (string) BF::text('login', null, null, ['bootstrap_version' => 4]);

        // Component classes follow the driver: Bootstrap 4 has no .form-label.
        $this->assertStringNotContainsString('form-label', $html);
        // Layout settings stay inherited from the (Bootstrap 5) form.
        $this->assertStringContainsString('class="mb-3"', $html);
        $this->assertStringNotContainsString('bootstrap_version', $html);
    }

    public function test_per_field_override_can_carry_its_own_group_class(): void
    {
        $html = (string) BF::text('login', null, null, [
            'bootstrap_version' => 4,
            'group_class' => 'form-group',
        ]);

        $this->assertStringContainsString('class="form-group"', $html);
        $this->assertStringNotContainsString('mb-3', $html);
    }
}
