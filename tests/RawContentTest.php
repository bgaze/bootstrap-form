<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * The raw-HTML contract of the content sinks.
 *
 * Injecting markup into a label or an addon is a supported, deliberate use case, so these sinks
 * are NOT escaped. Two distinct regimes, and the difference is what callers must know:
 *
 *  - label / help / success are ALWAYS raw — a predictable contract;
 *  - an addon is raw ONLY when its value carries an HTML tag, otherwise it is escaped and wrapped
 *    in the version's text addon. The decision is taken by the value, not by the caller.
 *
 * Escaping content that comes from user input, the database or translation files is therefore the
 * application's responsibility. The opt-in `escape` setting inverts both regimes — it is
 * characterized in EscapeSettingTest.
 */
class RawContentTest extends TestCase
{
    public function test_a_field_label_is_emitted_raw(): void
    {
        $expected = '<div id="q-group" class="mb-3"><label for="q" class="form-label"><b>Bold</b> & co</label>'
            .'<div><input id="q" class="form-control" name="q" type="text"></div></div>';

        $this->assertSame($expected, (string) BF::text('q', '<b>Bold</b> & co'));
    }

    public function test_a_standalone_label_is_emitted_raw(): void
    {
        $this->assertSame('<label for="q"><b>Bold</b> & co</label>', (string) BF::label('q', '<b>Bold</b> & co'));
    }

    public function test_help_text_is_emitted_raw(): void
    {
        $expected = '<div id="q-group" class="mb-3"><label for="q" class="form-label">Q</label>'
            .'<div><input id="q" class="form-control" aria-describedby="q-help" name="q" type="text">'
            .'<small id="q-help" class="form-text"><b>Bold</b> & co</small></div></div>';

        $this->assertSame($expected, (string) BF::text('q', 'Q', null, ['help' => '<b>Bold</b> & co']));
    }

    public function test_a_valid_feedback_message_is_emitted_raw(): void
    {
        $this->withErrors(['other' => 'Other is invalid.']);

        $html = (string) BF::text('q', 'Q', null, ['show_valid_feedback' => true, 'success' => '<b>Looks good</b>']);

        $this->assertStringContainsString('<div class="valid-feedback" id="q-valid"><b>Looks good</b></div>', $html);
    }

    public function test_a_tag_free_addon_is_escaped_and_wrapped(): void
    {
        $this->assertStringContainsString(
            '<span class="input-group-text">a &amp; b</span>',
            (string) BF::text('amt', 'Amt', null, ['prepend' => 'a & b']),
        );
    }

    public function test_an_addon_carrying_a_tag_is_emitted_raw_and_unwrapped(): void
    {
        $this->assertStringContainsString(
            '<div class="input-group"><script>alert(1)</script><input id="amt"',
            (string) BF::text('amt', 'Amt', null, ['prepend' => '<script>alert(1)</script>']),
        );
    }

    /**
     * Escaping a standalone label is opt-in through the fourth argument, which defaults to false.
     */
    public function test_a_standalone_label_can_be_escaped_on_demand(): void
    {
        $this->assertSame(
            '<label for="q">&lt;b&gt;Bold&lt;/b&gt; &amp; co</label>',
            (string) BF::label('q', '<b>Bold</b> & co', [], true),
        );
    }
}
