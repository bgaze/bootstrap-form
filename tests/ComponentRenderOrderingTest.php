<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * The render-ordering invariant of the stateful <x-bf::form> component.
 *
 * The builder is stateful; fields read the form state at render time. Blade buffers the slot
 * (fields) before the parent renders, so the form must establish its state in withAttributes()
 * (which runs before the slot). If ordering were wrong, fields would render with the default
 * (Bootstrap 4) state instead of the form's overridden state.
 */
class ComponentRenderOrderingTest extends Bootstrap4TestCase
{
    private function render(string $template): string
    {
        return trim(Blade::render($template));
    }

    public function test_standalone_field_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::text('login'),
            $this->render('<x-bf::text name="login"/>')
        );
    }

    public function test_field_inside_form_inherits_bootstrap5(): void
    {
        $html = $this->render('<x-bf::form url="/foo" bootstrap_version="5"><x-bf::text name="login"/></x-bf::form>');

        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('</form>', $html);

        // The field rendered Bootstrap 5 markup -> it saw the state set by open().
        $this->assertStringContainsString('mb-3', $html);
        $this->assertStringContainsString('form-label', $html);
        $this->assertStringNotContainsString('form-group', $html);
    }

    public function test_field_inside_form_inherits_horizontal_layout(): void
    {
        $html = $this->render('<x-bf::form url="/foo" layout="horizontal"><x-bf::text name="login"/></x-bf::form>');

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
