<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * Characterization: checkbox / radio / checkboxes / radios components match their BF facade
 * equivalents, including the value/checked defaults and the switch/inline settings.
 */
class ComponentCheckableTest extends TestCase
{
    private function render(string $template): string
    {
        return trim(Blade::render($template));
    }

    public function test_checkbox_matches_facade_with_default_value(): void
    {
        // Omitted value defaults to 1, like BF::checkbox().
        $this->assertSame(
            (string) BF::checkbox('accept', 'Accept'),
            $this->render('<x-bf::checkbox name="accept" label="Accept"/>')
        );
    }

    public function test_checkbox_switch_and_inline_settings(): void
    {
        $this->assertSame(
            (string) BF::checkbox('remember_me', null, 1, null, ['switch' => true, 'inline' => true]),
            $this->render('<x-bf::checkbox name="remember_me" switch inline/>')
        );
    }

    public function test_checkbox_checked_state(): void
    {
        $this->assertSame(
            (string) BF::checkbox('accept', 'Accept', 1, true),
            $this->render('<x-bf::checkbox name="accept" label="Accept" :checked="true"/>')
        );
    }

    public function test_radio_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::radio('gender', 'Female', 'f'),
            $this->render('<x-bf::radio name="gender" label="Female" value="f"/>')
        );
    }

    public function test_checkboxes_collection_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', 'editor' => 'Editor']),
            $this->render('<x-bf::checkboxes name="roles" label="Roles" :choices="[\'admin\' => \'Admin\', \'editor\' => \'Editor\']"/>')
        );
    }

    public function test_radios_collection_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::radios('size', 'Size', ['s' => 'Small', 'l' => 'Large'], 's'),
            $this->render('<x-bf::radios name="size" label="Size" :choices="[\'s\' => \'Small\', \'l\' => \'Large\']" :checked="\'s\'"/>')
        );
    }
}
