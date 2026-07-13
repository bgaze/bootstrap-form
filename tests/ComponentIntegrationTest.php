<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * Integration: components keep delegating faithfully under stateful conditions (validation
 * errors, old input, help text) and a full form composes exactly like the facade sequence.
 */
class ComponentIntegrationTest extends TestCase
{
    private function render(string $template): string
    {
        return trim(Blade::render($template));
    }

    public function test_error_rendering_matches_facade(): void
    {
        $this->withErrors(['login' => ['The login field is required.']]);

        $this->assertSame(
            (string) BF::text('login'),
            $this->render('<x-bf::text name="login"/>')
        );
    }

    public function test_old_input_repopulation_matches_facade(): void
    {
        $this->withOldInput(['login' => 'oldlogin']);

        $this->assertSame(
            (string) BF::text('login'),
            $this->render('<x-bf::text name="login"/>')
        );
    }

    public function test_help_text_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::text('login', null, null, ['help' => 'Your username']),
            $this->render('<x-bf::text name="login" help="Your username"/>')
        );
    }

    public function test_per_field_version_override_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::text('login', null, null, ['bootstrap_version' => 5]),
            $this->render('<x-bf::text name="login" :bootstrap-version="5"/>')
        );
    }

    public function test_full_form_composes_like_the_facade_sequence(): void
    {
        $expected = BF::open(['url' => '/my/url', 'novalidate' => true])
            .(string) BF::text('login')
            .(string) BF::email('email')
            .(string) BF::checkbox('remember_me', null, 1, null, ['switch' => true, 'inline' => true])
            .(string) BF::submit('Login')
            .BF::close();

        $template = '<x-bf::form url="/my/url" novalidate>'
            .'<x-bf::text name="login"/>'
            .'<x-bf::email name="email"/>'
            .'<x-bf::checkbox name="remember_me" switch inline/>'
            .'<x-bf::submit value="Login"/>'
            .'</x-bf::form>';

        $this->assertSame($expected, $this->render($template));
    }
}
