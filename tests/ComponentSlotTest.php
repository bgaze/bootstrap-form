<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * Named slots for fields: <x-slot:label> (rich label, overriding the attribute) and
 * <x-slot:prepend> / <x-slot:append> (rich input-group addons). Checked against the
 * equivalent BF options (the oracle).
 */
class ComponentSlotTest extends TestCase
{
    private function render(string $template): string
    {
        return trim(Blade::render($template));
    }

    public function test_label_slot_overrides_the_label_attribute(): void
    {
        $this->assertSame(
            (string) BF::text('email', 'From slot'),
            $this->render('<x-bf::text name="email" label="From attr"><x-slot:label>From slot</x-slot:label></x-bf::text>')
        );
    }

    public function test_label_slot_allows_rich_html(): void
    {
        $this->assertSame(
            (string) BF::text('email', 'Email <span class="req">*</span>'),
            $this->render('<x-bf::text name="email"><x-slot:label>Email <span class="req">*</span></x-slot:label></x-bf::text>')
        );
    }

    public function test_prepend_slot_becomes_an_addon(): void
    {
        $this->assertSame(
            (string) BF::text('amount', null, null, ['prepend' => '$']),
            $this->render('<x-bf::text name="amount"><x-slot:prepend>$</x-slot:prepend></x-bf::text>')
        );
    }

    public function test_append_slot_becomes_an_addon(): void
    {
        $this->assertSame(
            (string) BF::text('domain', null, null, ['append' => '.com']),
            $this->render('<x-bf::text name="domain"><x-slot:append>.com</x-slot:append></x-bf::text>')
        );
    }

    public function test_label_slot_applies_to_select(): void
    {
        $this->assertSame(
            (string) BF::select('color', 'Pick a color', ['red' => 'Red']),
            $this->render('<x-bf::select name="color" :choices="[\'red\' => \'Red\']"><x-slot:label>Pick a color</x-slot:label></x-bf::select>')
        );
    }
}
