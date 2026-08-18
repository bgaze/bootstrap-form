<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

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

    public function test_a_bare_boolean_setting_passes_true(): void
    {
        $this->assertSame(
            (string) BF::text('q', '<b>Bold</b>', null, ['escape' => true]),
            $this->render('<x-bf::text name="q" label="<b>Bold</b>" escape/>')
        );
    }

    /**
     * The Blade footgun: an attribute string is truthy, so `escape="false"` used to ENABLE what
     * the author meant to disable. Both idioms must now agree.
     */
    public function test_a_boolean_setting_given_as_a_string_is_normalized(): void
    {
        $oracle = (string) BF::text('q', '<b>Bold</b>', null, ['escape' => false]);

        $this->assertSame($oracle, $this->render('<x-bf::text name="q" label="<b>Bold</b>" escape="false"/>'));
        $this->assertSame($oracle, $this->render('<x-bf::text name="q" label="<b>Bold</b>" :escape="false"/>'));
    }

    public function test_the_normalization_covers_the_other_boolean_settings(): void
    {
        $this->assertSame(
            (string) BF::checkbox('cgu', 'CGU', 1, null, ['switch' => false]),
            $this->render('<x-bf::checkbox name="cgu" label="CGU" switch="false"/>')
        );
    }

    /**
     * The guard on the other side: a setting that legitimately accepts a string is NOT normalized,
     * so `help="false"` stays the text "false".
     */
    public function test_a_string_setting_named_false_is_left_alone(): void
    {
        $this->assertSame(
            (string) BF::text('q', 'Q', null, ['help' => 'false']),
            $this->render('<x-bf::text name="q" label="Q" help="false"/>')
        );
    }

    /**
     * A slot is markup the author wrote in the template, so it keeps the raw regime even under
     * escape -- which the oracle states by passing a HtmlString.
     */
    public function test_an_addon_slot_stays_raw_under_escape(): void
    {
        $this->assertSame(
            (string) BF::text('amt', 'Amt', null, [
                'escape' => true,
                'prepend' => new HtmlString('<button type="button">Go</button>'),
            ]),
            $this->render('<x-bf::text name="amt" label="Amt" escape><x-slot:prepend><button type="button">Go</button></x-slot:prepend></x-bf::text>')
        );
    }

    public function test_a_label_slot_stays_raw_under_escape(): void
    {
        $this->assertSame(
            (string) BF::text('q', new HtmlString('<b>Bold</b>'), null, ['escape' => true]),
            $this->render('<x-bf::text name="q" escape><x-slot:label><b>Bold</b></x-slot:label></x-bf::text>')
        );
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
