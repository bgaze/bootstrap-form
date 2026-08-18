<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * The two error-rendering settings: which bag a field reads, and how many of its messages it
 * renders. Both are inherited from the form and overridable per field.
 */
class ErrorSettingsTest extends TestCase
{
    public function test_only_the_first_error_is_rendered_by_default(): void
    {
        $this->withErrors(['login' => ['Too short.', 'Already taken.']]);

        $html = (string) BF::text('login');

        $this->assertStringContainsString('<div class="invalid-feedback" id="login-error">Too short.</div>', $html);
        $this->assertStringNotContainsString('Already taken.', $html);
    }

    public function test_show_all_errors_renders_every_message(): void
    {
        $this->withErrors(['login' => ['Too short.', 'Already taken.']]);

        $html = (string) BF::text('login', null, null, ['show_all_errors' => true]);

        $this->assertStringContainsString('Too short.', $html);
        $this->assertStringContainsString('Already taken.', $html);
    }

    public function test_error_bag_targets_a_named_bag(): void
    {
        $this->withErrors(['login' => 'From the other bag.'], 'signup');

        $this->assertStringNotContainsString('From the other bag.', (string) BF::text('login'));
        $this->assertStringContainsString(
            'From the other bag.',
            (string) BF::text('login', null, null, ['error_bag' => 'signup']),
        );
    }

    public function test_the_error_bag_is_inherited_from_the_form(): void
    {
        $this->withErrors(['login' => 'From the other bag.'], 'signup');

        BF::open(['url' => '/x', 'error_bag' => 'signup']);
        $html = (string) BF::text('login');
        BF::close();

        $this->assertStringContainsString('From the other bag.', $html);
    }
}
