<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Collection;

/**
 * Characterization tests for SelectInput (Bootstrap 4).
 */
class SelectInputTest extends Bootstrap4TestCase
{
    public function test_native_select(): void
    {
        $expected = '<div id="country-group" class="form-group"><label for="country">Country</label>'
            .'<div><select id="country" class="form-control" name="country">'
            .'<option value="fr">France</option><option value="us">USA</option></select></div></div>';

        $this->assertSame($expected, (string) BF::select('country', null, ['fr' => 'France', 'us' => 'USA']));
    }

    public function test_custom_select(): void
    {
        $expected = '<div id="country-group" class="form-group"><label for="country">Country</label>'
            .'<div><select id="country" class="custom-select" name="country">'
            .'<option value="fr">France</option></select></div></div>';

        $this->assertSame($expected, (string) BF::select('country', null, ['fr' => 'France'], null, ['custom' => true]));
    }

    public function test_size_large(): void
    {
        $expected = '<div id="country-group" class="form-group"><label for="country">Country</label>'
            .'<div><select id="country" class="form-control form-control-lg" name="country">'
            .'<option value="fr">France</option></select></div></div>';

        $this->assertSame($expected, (string) BF::select('country', null, ['fr' => 'France'], null, ['size' => 'lg']));
    }

    public function test_option_attributes_apply_to_every_option(): void
    {
        $expected = '<div id="country-group" class="form-group"><label for="country">Country</label>'
            .'<div><select id="country" class="form-control" name="country">'
            .'<option value="fr" class="opt">France</option><option value="us" class="opt">USA</option></select></div></div>';

        $this->assertSame($expected, (string) BF::select('country', null, ['fr' => 'France', 'us' => 'USA'], null, ['option_attributes' => ['class' => 'opt']]));
    }

    public function test_advanced_option_and_optgroup(): void
    {
        $expected = '<div id="country-group" class="form-group"><label for="country">Country</label>'
            .'<div><select id="country" class="form-control" name="country">'
            .'<optgroup label="Europe" class="grp"><option value="fr" data-flag="fr">France</option></optgroup>'
            .'</select></div></div>';

        $choices = [['label' => 'Europe', 'options' => [['value' => 'fr', 'label' => 'France', 'data-flag' => 'fr']]]];

        $this->assertSame($expected, (string) BF::select('country', null, $choices, null, ['optgroup_attributes' => ['class' => 'grp']]));
    }

    public function test_choices_accept_a_collection_like_an_array(): void
    {
        // Regression guard: v3 (Collective) accepted a Collection here — Model::pluck('name', 'id')
        // being the idiomatic source. The widened iterable signature restores that.
        $choices = ['fr' => 'France', 'us' => 'USA'];

        $this->assertSame(
            (string) BF::select('country', null, $choices),
            (string) BF::select('country', null, new Collection($choices))
        );
    }
}
