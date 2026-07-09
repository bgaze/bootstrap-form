<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * The opt-in mechanism: default is Bootstrap 4, overridable per form and per field.
 * No `bootstrap_version` config override here, so the package default (4) applies.
 */
class VersionOverrideTest extends TestCase
{
    public function test_default_is_bootstrap4(): void
    {
        $html = (string) BF::text('login');

        $this->assertStringContainsString('class="form-group"', $html);
        $this->assertStringNotContainsString('mb-3', $html);
        $this->assertStringNotContainsString('form-label', $html);
    }

    public function test_per_form_override_switches_to_bootstrap5(): void
    {
        BF::open(['bootstrap_version' => 5]);
        $html = (string) BF::text('login');
        BF::close();

        $this->assertStringContainsString('class="mb-3"', $html);
        $this->assertStringContainsString('class="form-label"', $html);
        $this->assertStringNotContainsString('form-group', $html);
    }

    public function test_fields_after_close_revert_to_default(): void
    {
        BF::open(['bootstrap_version' => 5]);
        BF::close();

        $html = (string) BF::text('login');

        $this->assertStringContainsString('class="form-group"', $html);
        $this->assertStringNotContainsString('mb-3', $html);
    }

    public function test_per_field_override_switches_to_bootstrap5(): void
    {
        $html = (string) BF::text('login', null, null, ['bootstrap_version' => 5]);

        $this->assertStringContainsString('class="mb-3"', $html);
        $this->assertStringContainsString('class="form-label"', $html);
        $this->assertStringNotContainsString('bootstrap_version', $html);
    }
}
