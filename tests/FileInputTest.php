<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Characterization tests for FileInput (Bootstrap 4).
 */
class FileInputTest extends TestCase
{
    public function test_native_file(): void
    {
        $expected = '<div id="avatar-group" class="form-group"><label for="avatar">Avatar</label>'
            . '<div><input id="avatar" name="avatar" type="file"></div></div>';

        $this->assertSame($expected, (string) BF::file('avatar'));
    }

    public function test_custom_file(): void
    {
        $expected = '<div id="avatar-group" class="form-group"><label for="avatar">Avatar</label>'
            . '<div><div class="custom-file"><input id="avatar" class="custom-file-input" name="avatar" type="file">'
            . '<label for="avatar" class="custom-file-label">Choose file</label></div></div></div>';

        $this->assertSame($expected, (string) BF::file('avatar', null, ['custom' => true]));
    }
}
