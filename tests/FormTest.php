<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Characterization tests for form open/close and layout classes (Bootstrap 4).
 * The CSRF token input value is environment-dependent, so form-open assertions
 * target the opening tag (attributes + layout class), not the token value.
 */
class FormTest extends Bootstrap4TestCase
{
    public function test_open_default_has_no_layout_class(): void
    {
        $html = BF::open(['url' => '/foo']);

        $this->assertStringStartsWith(
            '<form method="POST" action="http://localhost/foo" accept-charset="UTF-8" role="form">',
            $html
        );
        $this->assertStringContainsString('name="_token" type="hidden"', $html);
    }

    public function test_open_horizontal_adds_form_horizontal_class(): void
    {
        $html = BF::horizontal(['url' => '/foo']);

        $this->assertStringStartsWith(
            '<form method="POST" action="http://localhost/foo" accept-charset="UTF-8" role="form" class="form-horizontal">',
            $html
        );
    }

    public function test_open_inline_adds_form_inline_class(): void
    {
        $html = BF::inline(['url' => '/foo']);

        $this->assertStringStartsWith(
            '<form method="POST" action="http://localhost/foo" accept-charset="UTF-8" role="form" class="form-inline">',
            $html
        );
    }

    public function test_close(): void
    {
        BF::open(['url' => '/foo']);

        $this->assertSame('</form>', BF::close());
    }
}
