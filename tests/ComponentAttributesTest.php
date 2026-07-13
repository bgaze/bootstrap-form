<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * The ergonomic attribute contract: top-level -> input, label:/group:/input: prefixes,
 * kebab-case normalization, and the group/label toggles. Each case is checked against the
 * equivalent BF options array (the oracle).
 */
class ComponentAttributesTest extends TestCase
{
    private function render(string $template): string
    {
        return trim(Blade::render($template));
    }

    public function test_label_prefix_targets_the_label_element(): void
    {
        $this->assertSame(
            (string) BF::text('login', null, null, ['label' => ['class' => 'fw-bold']]),
            $this->render('<x-bf::text name="login" label:class="fw-bold"/>')
        );
    }

    public function test_group_prefix_targets_the_group_element(): void
    {
        $this->assertSame(
            (string) BF::text('login', null, null, ['group' => ['class' => 'mb-4']]),
            $this->render('<x-bf::text name="login" group:class="mb-4"/>')
        );
    }

    public function test_prefixes_and_input_attributes_combine(): void
    {
        $this->assertSame(
            (string) BF::text('email', 'Email', null, [
                'class' => 'form-control-lg',
                'label' => ['class' => 'fw-bold'],
                'group' => ['class' => 'mb-4'],
            ]),
            $this->render('<x-bf::text name="email" label="Email" class="form-control-lg" label:class="fw-bold" group:class="mb-4"/>')
        );
    }

    public function test_input_prefix_forces_a_literal_html_attribute(): void
    {
        // input:size escapes the "size" sizing setting and emits a literal HTML size attribute.
        $this->assertSame(
            (string) BF::text('q', null, null, ['~size' => '10']),
            $this->render('<x-bf::text name="q" input:size="10"/>')
        );
    }

    public function test_size_without_prefix_is_the_sizing_setting(): void
    {
        $this->assertSame(
            (string) BF::text('q', null, null, ['size' => 'lg']),
            $this->render('<x-bf::text name="q" size="lg"/>')
        );
    }

    public function test_kebab_case_setting_is_normalized(): void
    {
        // bootstrap-version (kebab, idiomatic) maps to the bootstrap_version setting.
        $this->assertSame(
            (string) BF::text('login', null, null, ['bootstrap_version' => 5]),
            $this->render('<x-bf::text name="login" bootstrap-version="5"/>')
        );
    }

    public function test_kebab_html_attribute_is_preserved(): void
    {
        // data-* is not a setting -> kept verbatim (not turned into data_*).
        $this->assertSame(
            (string) BF::text('login', null, null, ['data-toggle' => 'tooltip']),
            $this->render('<x-bf::text name="login" data-toggle="tooltip"/>')
        );
    }

    public function test_label_false_disables_the_label(): void
    {
        $this->assertSame(
            (string) BF::text('login', false),
            $this->render('<x-bf::text name="login" :label="false"/>')
        );
    }

    public function test_group_false_disables_the_wrapper(): void
    {
        $this->assertSame(
            (string) BF::text('login', null, null, ['group' => false]),
            $this->render('<x-bf::text name="login" :group="false"/>')
        );
    }
}
