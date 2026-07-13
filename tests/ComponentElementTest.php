<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * Characterization: button / reset / button / link / label components match the BF facade,
 * with the value/title/text provided either as an attribute or as the default slot.
 */
class ComponentElementTest extends TestCase
{
    private function render(string $template): string
    {
        return trim(Blade::render($template));
    }

    public function test_submit_from_attribute_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::submit('Save'),
            $this->render('<x-bf::submit value="Save"/>')
        );
    }

    public function test_submit_from_slot_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::submit('Login'),
            $this->render('<x-bf::submit>Login</x-bf::submit>')
        );
    }

    public function test_reset_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::reset('Reset'),
            $this->render('<x-bf::reset>Reset</x-bf::reset>')
        );
    }

    public function test_button_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::button('Click'),
            $this->render('<x-bf::button>Click</x-bf::button>')
        );
    }

    public function test_link_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::link('/go', 'Go'),
            $this->render('<x-bf::link href="/go">Go</x-bf::link>')
        );
    }

    public function test_label_from_name_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::label('login', 'Login'),
            $this->render('<x-bf::label name="login">Login</x-bf::label>')
        );
    }

    public function test_label_accepts_for_alias(): void
    {
        $this->assertSame(
            (string) BF::label('login', 'Login'),
            $this->render('<x-bf::label for="login">Login</x-bf::label>')
        );
    }
}
