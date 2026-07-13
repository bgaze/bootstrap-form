<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * Spike: proves the render-ordering mechanism of the stateful <x-bf::form> component.
 *
 * The builder is stateful; fields read the form state at render time. Blade buffers the
 * slot (fields) BEFORE the form view renders, so the state must be set in withAttributes().
 * If ordering were wrong, fields would render with the default (Bootstrap 4) state instead
 * of the form's overridden state.
 */
class ComponentSpikeTest extends TestCase
{
    public function test_standalone_field_matches_facade(): void
    {
        $component = trim(Blade::render('<x-bf::text name="login"/>'));
        $facade = (string) BF::text('login');

        $this->assertSame($facade, $component);
    }

    public function test_field_inside_form_inherits_bootstrap5(): void
    {
        $html = Blade::render('<x-bf::form url="/foo" bootstrap_version="5"><x-bf::text name="login"/></x-bf::form>');

        // The <form> wraps the field.
        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('</form>', $html);

        // The field rendered Bootstrap 5 markup -> it saw the state set by open().
        $this->assertStringContainsString('mb-3', $html);
        $this->assertStringContainsString('form-label', $html);
        $this->assertStringNotContainsString('form-group', $html);
    }

    public function test_field_inside_form_inherits_horizontal_layout(): void
    {
        $html = Blade::render('<x-bf::form url="/foo" layout="horizontal"><x-bf::text name="login"/></x-bf::form>');

        $this->assertStringContainsString('form-group row', $html);
        $this->assertStringContainsString('col-form-label', $html);
    }

    public function test_close_resets_state_for_fields_rendered_after_the_form(): void
    {
        Blade::render('<x-bf::form url="/foo" bootstrap_version="5"><x-bf::text name="inside"/></x-bf::form>');

        // A standalone field after the form reverts to the package default (Bootstrap 4).
        $after = (string) BF::text('after');

        $this->assertStringContainsString('form-group', $after);
        $this->assertStringNotContainsString('mb-3', $after);
    }
}
