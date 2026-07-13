<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Characterization tests for SelectInput (Bootstrap 4).
 */
class SelectInputTest extends TestCase
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
}
