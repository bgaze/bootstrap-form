<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * The two directions of the settings / attributes partition.
 *
 * A key the field recognises as a setting is consumed and never rendered; every other key is
 * rendered on the control verbatim. Both directions can lose a caller's intent silently, which is
 * why the guide states the recognised names as a closed set.
 */
class SettingsPartitionTest extends TestCase
{
    // ## SETTINGS SWALLOW A COLLIDING ATTRIBUTE #################################

    /**
     * "size" is the only setting name that is also an HTML attribute a caller would plausibly want
     * on the element it targets. A value that is neither sm nor lg is simply lost.
     */
    public function test_size_is_swallowed_on_an_input_and_recovered_by_the_literal_escape(): void
    {
        $this->assertStringContainsString(
            '<input id="code" class="form-control" name="code" type="text">',
            (string) BF::text('code', 'Code', null, ['size' => '10']),
        );

        $this->assertStringContainsString(
            '<input size="10" id="code" class="form-control" name="code" type="text">',
            (string) BF::text('code', 'Code', null, ['~size' => '10']),
        );
    }

    public function test_size_is_swallowed_on_a_select_and_recovered_by_the_literal_escape(): void
    {
        $this->assertStringContainsString(
            '<select id="s" class="form-select" name="s">',
            (string) BF::select('s', 'S', ['a' => 'A'], null, ['size' => '5']),
        );

        $this->assertStringContainsString(
            '<select size="5" id="s" class="form-select" name="s">',
            (string) BF::select('s', 'S', ['a' => 'A'], null, ['~size' => '5']),
        );
    }

    /**
     * "tag" is recognised but the builder overwrites it after the caller's options, so it can only
     * ever be swallowed: it never changes the rendered type.
     */
    public function test_tag_is_recognised_and_always_ignored(): void
    {
        $this->assertSame((string) BF::text('q', 'Q'), (string) BF::text('q', 'Q', null, ['tag' => 'email']));
        $this->assertSame((string) BF::checkbox('c', 'C'), (string) BF::checkbox('c', 'C', 1, null, ['tag' => 'radio']));
    }

    // ## UNKNOWN KEYS BECOME ATTRIBUTES #########################################

    /**
     * The mirror direction: a misspelled or invented option name is not rejected, it is rendered as
     * an HTML attribute.
     */
    public function test_an_unrecognised_key_is_rendered_as_an_html_attribute(): void
    {
        $this->assertStringContainsString(
            '<input helper="oops" sucess="typo" id="q" class="form-control" name="q" type="text">',
            (string) BF::text('q', 'Q', null, ['helper' => 'oops', 'sucess' => 'typo']),
        );
    }
}
