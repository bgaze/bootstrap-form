<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * Characterization: select / file / hidden components match their BF facade equivalents.
 */
class ComponentChoiceFieldTest extends TestCase
{
    private function render(string $template): string
    {
        return trim(Blade::render($template));
    }

    public function test_select_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::select('color', 'Color', ['red' => 'Red', 'blue' => 'Blue'], 'blue'),
            $this->render('<x-bf::select name="color" label="Color" :choices="[\'red\' => \'Red\', \'blue\' => \'Blue\']" :selected="\'blue\'"/>')
        );
    }

    public function test_select_placeholder_setting(): void
    {
        $this->assertSame(
            (string) BF::select('color', null, ['red' => 'Red'], null, ['placeholder' => 'Pick one']),
            $this->render('<x-bf::select name="color" :choices="[\'red\' => \'Red\']" placeholder="Pick one"/>')
        );
    }

    public function test_select_option_prefix_targets_every_option(): void
    {
        $this->assertSame(
            (string) BF::select('color', null, ['red' => 'Red'], null, ['option_attributes' => ['class' => 'opt']]),
            $this->render('<x-bf::select name="color" :choices="[\'red\' => \'Red\']" option:class="opt"/>')
        );
    }

    public function test_select_optgroup_prefix_targets_every_optgroup(): void
    {
        $this->assertSame(
            (string) BF::select('color', null, ['Warm' => ['red' => 'Red']], null, ['optgroup_attributes' => ['data-z' => 'yo']]),
            $this->render('<x-bf::select name="color" :choices="[\'Warm\' => [\'red\' => \'Red\']]" optgroup:data-z="yo"/>')
        );
    }

    public function test_select_advanced_choices_pass_through_the_data(): void
    {
        $this->assertSame(
            (string) BF::select('color', null, [['value' => 'red', 'label' => 'Red', 'data-x' => 'y']]),
            $this->render('<x-bf::select name="color" :choices="[[\'value\' => \'red\', \'label\' => \'Red\', \'data-x\' => \'y\']]"/>')
        );
    }

    public function test_select_choices_accept_a_collection(): void
    {
        // The widened iterable property lets :choices bind a Collection, not just an array.
        $this->assertSame(
            (string) BF::select('color', 'Color', ['red' => 'Red', 'blue' => 'Blue']),
            trim(Blade::render(
                '<x-bf::select name="color" label="Color" :choices="$choices"/>',
                ['choices' => collect(['red' => 'Red', 'blue' => 'Blue'])]
            ))
        );
    }

    public function test_checkboxes_option_prefix_targets_every_child(): void
    {
        $this->assertSame(
            (string) BF::checkboxes('roles', 'Roles', ['a' => 'A', 'b' => 'B'], null, ['option_attributes' => ['data-g' => '1']]),
            $this->render('<x-bf::checkboxes name="roles" label="Roles" :choices="[\'a\' => \'A\', \'b\' => \'B\']" option:data-g="1"/>')
        );
    }

    public function test_option_prefix_is_not_projected_on_a_non_choice_component(): void
    {
        // The guard: option:/optgroup: are only projected by choice components, so the
        // option_attributes array bag never leaks as a rendered attribute elsewhere.
        $out = $this->render('<x-bf::text name="foo" option:class="x"/>');

        $this->assertStringNotContainsString('option_attributes', $out);
        $this->assertStringNotContainsString('Array', $out);
    }

    public function test_file_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::file('document', 'Document'),
            $this->render('<x-bf::file name="document" label="Document"/>')
        );
    }

    public function test_hidden_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::hidden('token', 'abc'),
            $this->render('<x-bf::hidden name="token" value="abc"/>')
        );
    }
}
