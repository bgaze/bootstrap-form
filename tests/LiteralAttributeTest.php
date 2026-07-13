<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Characterization tests for the "~" literal-attribute escape (Attributes::LITERAL_PREFIX).
 *
 * A "~"-prefixed option name bypasses the settings filter so an HTML attribute whose
 * name collides with an internal setting (e.g. `size`) can still be emitted; the prefix
 * is stripped at render time. These outputs were verified byte-identical before and
 * after the Attributes value-object rewrite.
 */
class LiteralAttributeTest extends TestCase
{
    public function test_tilde_emits_literal_attribute_colliding_with_a_setting(): void
    {
        // `size` is a package setting (sm|lg); `~size` forces the literal HTML attribute.
        $expected = '<div id="login-group" class="form-group"><label for="login">Login</label>'
            . '<div><input size="5" id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, (string) BF::text('login', null, null, ['~size' => 5]));
    }

    public function test_tilde_survives_into_checkbox_collection_children(): void
    {
        $expected = '<div id="roles-group" class="form-group"><label for="roles">Roles</label><div>'
            . '<div class="form-check"><input data-x="y" id="roles-a" class="form-check-input" name="roles" type="checkbox" value="a"><label for="roles-a" class="form-check-label">A</label></div>'
            . '<div class="form-check"><input data-x="y" id="roles-b" class="form-check-input" name="roles" type="checkbox" value="b"><label for="roles-b" class="form-check-label">B</label></div>'
            . '</div></div>';

        $this->assertSame($expected, (string) BF::checkboxes('roles', 'Roles', ['a' => 'A', 'b' => 'B'], null, ['~data-x' => 'y']));
    }
}
