<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Characterization tests for FileInput (Bootstrap 4).
 */
class FileInputTest extends Bootstrap4TestCase
{
    public function test_native_file(): void
    {
        $expected = '<div id="avatar-group" class="form-group"><label for="avatar">Avatar</label>'
            .'<div><input id="avatar" name="avatar" type="file"></div></div>';

        $this->assertSame($expected, (string) BF::file('avatar'));
    }

    public function test_custom_file(): void
    {
        $expected = '<div id="avatar-group" class="form-group"><label for="avatar">Avatar</label>'
            .'<div><div class="custom-file"><input id="avatar" class="custom-file-input" name="avatar" type="file">'
            .'<label for="avatar" class="custom-file-label">Choose file</label></div></div></div>';

        $this->assertSame($expected, (string) BF::file('avatar', null, ['custom' => true]));
    }

    /**
     * The custom-file markup carries two settings of its own: "text" is the label shown inside the
     * control (default "Choose file"), and "button" drives the browse-button label through
     * data-browse. Both are Bootstrap 4 custom-file only.
     */
    public function test_custom_file_labels_are_configurable(): void
    {
        $expected = '<div id="avatar-group" class="form-group"><label for="avatar">Avatar</label>'
            .'<div><div class="custom-file"><input id="avatar" class="custom-file-input" name="avatar" type="file">'
            .'<label for="avatar" class="custom-file-label" data-browse="Browse">Pick a file</label></div></div></div>';

        $this->assertSame($expected, (string) BF::file('avatar', null, [
            'custom' => true,
            'text' => 'Pick a file',
            'button' => 'Browse',
        ]));
    }

    public function test_the_custom_file_labels_are_swallowed_without_custom(): void
    {
        $html = (string) BF::file('avatar', null, ['text' => 'Pick a file', 'button' => 'Browse']);

        $this->assertSame((string) BF::file('avatar'), $html);
        $this->assertStringNotContainsString('Pick a file', $html);
        $this->assertStringNotContainsString('data-browse', $html);
    }
}
