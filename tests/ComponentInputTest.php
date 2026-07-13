<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * Characterization: each text-like x-component renders byte-identically to its BF facade
 * equivalent (the components are thin delegators, so the facade output is the oracle).
 */
class ComponentInputTest extends TestCase
{
    private function render(string $template): string
    {
        return trim(Blade::render($template));
    }

    public function test_text_like_components_match_facade(): void
    {
        // tag => BF facade method
        $map = [
            'text' => 'text',
            'email' => 'email',
            'url' => 'url',
            'tel' => 'tel',
            'number' => 'number',
            'date' => 'date',
            'time' => 'time',
            'month' => 'month',
            'week' => 'week',
            'search' => 'search',
            'color' => 'color',
            'textarea' => 'textarea',
            'range' => 'range',
            'datetime-local' => 'datetimeLocal',
        ];

        foreach ($map as $tag => $method) {
            $this->assertSame(
                (string) BF::{$method}('field'),
                $this->render("<x-bf::{$tag} name=\"field\"/>"),
                "<x-bf::{$tag}> should match BF::{$method}()"
            );
        }
    }

    public function test_password_matches_facade(): void
    {
        $this->assertSame(
            (string) BF::password('secret'),
            $this->render('<x-bf::password name="secret"/>')
        );
    }

    public function test_label_and_value_are_positional(): void
    {
        $this->assertSame(
            (string) BF::text('login', 'My label', 'the value'),
            $this->render('<x-bf::text name="login" label="My label" value="the value"/>')
        );
    }

    public function test_bound_value_is_passed_through(): void
    {
        $this->assertSame(
            (string) BF::number('age', null, 42),
            $this->render('<x-bf::number name="age" :value="42"/>')
        );
    }

    public function test_input_attributes_flow_to_the_control(): void
    {
        $this->assertSame(
            (string) BF::text('login', null, null, ['class' => 'custom', 'placeholder' => 'Type here', 'required' => true]),
            $this->render('<x-bf::text name="login" class="custom" placeholder="Type here" required/>')
        );
    }
}
