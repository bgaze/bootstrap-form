<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Characterization tests for RangeInput (Bootstrap 4).
 */
class RangeInputTest extends TestCase
{
    public function test_native_range(): void
    {
        $expected = '<div id="volume-group" class="form-group"><label for="volume">Volume</label>'
            . '<div><input id="volume" class="form-control-range" name="volume" type="range"></div></div>';

        $this->assertSame($expected, (string) BF::range('volume'));
    }

    public function test_custom_range(): void
    {
        $expected = '<div id="volume-group" class="form-group"><label for="volume">Volume</label>'
            . '<div><input id="volume" class="custom-range" name="volume" type="range"></div></div>';

        $this->assertSame($expected, (string) BF::range('volume', null, null, ['custom' => true]));
    }
}
