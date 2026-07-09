<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Characterization tests for standalone elements: hidden, buttons, link, label (Bootstrap 4).
 */
class ElementsTest extends TestCase
{
    public function test_hidden(): void
    {
        $this->assertSame(
            '<input id="token" name="token" type="hidden" value="abc">',
            (string) BF::hidden('token', 'abc')
        );
    }

    public function test_submit(): void
    {
        $this->assertSame(
            '<input class="btn btn-primary" type="submit" value="Save">',
            (string) BF::submit('Save')
        );
    }

    public function test_reset(): void
    {
        $this->assertSame(
            '<input class="btn btn-danger" type="reset" value="Reset">',
            (string) BF::reset('Reset')
        );
    }

    public function test_button(): void
    {
        $this->assertSame(
            '<button class="btn btn-primary" type="button">Click</button>',
            (string) BF::button('Click')
        );
    }

    public function test_link(): void
    {
        $this->assertSame(
            '<a href="http://localhost/go" class="btn btn-primary">Go</a>',
            (string) BF::link('/go', 'Go')
        );
    }

    public function test_label(): void
    {
        $this->assertSame(
            '<label for="login">Login</label>',
            (string) BF::label('login', 'Login')
        );
    }
}
