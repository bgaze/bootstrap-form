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
