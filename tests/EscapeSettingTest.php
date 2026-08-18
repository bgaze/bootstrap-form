<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\HtmlString;

/**
 * The opt-in `escape` regime.
 *
 * The content sinks are raw by default (characterized in RawContentTest). `escape => true` makes
 * them escape their content instead, following the usual cascade (config -> form -> field). A
 * Htmlable value is markup by construction, so it opts itself out of escaping whatever the
 * setting says — which is what keeps the x-component slots working under a global escape.
 *
 * See https://github.com/bgaze/bootstrap-form/issues/20.
 */
class EscapeSettingTest extends TestCase
{
    // ## THE SETTING ############################################################

    /**
     * `escape` is a recognized setting, so it is consumed as configuration and never leaks onto
     * the control as an HTML attribute.
     */
    public function test_the_setting_never_reaches_the_markup(): void
    {
        $this->assertStringNotContainsString('escape', (string) BF::text('q', 'Q', null, ['escape' => true]));
    }

    /**
     * The key is seeded before the config merge, so a config file published before it existed
     * keeps it a known setting instead of letting it render as an attribute.
     */
    public function test_the_setting_survives_a_published_config_that_omits_it(): void
    {
        $config = config('bootstrap_form');
        unset($config['escape']);
        config(['bootstrap_form' => $config]);
        BF::close();

        $this->assertStringNotContainsString('escape', (string) BF::text('q', 'Q', null, ['escape' => true]));
    }

    /**
     * The application value still wins over the seeded default.
     */
    public function test_the_config_value_wins_over_the_seeded_default(): void
    {
        config(['bootstrap_form.escape' => true]);
        BF::close();

        $this->assertTrue(BF::settings()->get('escape'));
    }

    // ## THE LABEL SINK #########################################################

    public function test_a_field_label_is_escaped(): void
    {
        $expected = '<div id="q-group" class="mb-3"><label for="q" class="form-label">&lt;b&gt;Bold&lt;/b&gt; &amp; co</label>'
            .'<div><input id="q" class="form-control" name="q" type="text"></div></div>';

        $this->assertSame($expected, (string) BF::text('q', '<b>Bold</b> & co', null, ['escape' => true]));
    }

    /**
     * The required mark is configuration, not content: its HTML is a documented feature, so it is
     * appended after the label has been escaped.
     */
    public function test_the_required_mark_keeps_its_markup_while_the_label_is_escaped(): void
    {
        $html = (string) BF::text('q', '<b>Q</b>', null, [
            'required' => true,
            'required_mark' => ' <span class="text-danger">*</span>',
            'escape' => true,
        ]);

        $this->assertStringContainsString(
            '>&lt;b&gt;Q&lt;/b&gt; <span class="text-danger">*</span></label>',
            $html,
        );
    }

    public function test_a_htmlable_label_is_emitted_verbatim(): void
    {
        $this->assertStringContainsString(
            '<label for="q" class="form-label"><b>Bold</b></label>',
            (string) BF::text('q', new HtmlString('<b>Bold</b>'), null, ['escape' => true]),
        );
    }

    /**
     * A choice collection propagates its settings to the generated children, so the child labels
     * follow the collection's escaping policy without anything to declare.
     */
    public function test_choice_child_labels_inherit_the_setting(): void
    {
        $html = (string) BF::checkboxes('opts', 'Opts', ['a' => '<b>A</b>'], null, ['escape' => true]);

        $this->assertStringContainsString('&lt;b&gt;A&lt;/b&gt;', $html);
        $this->assertStringNotContainsString('<b>A</b>', $html);
    }

    /**
     * Inherited from the form like any other setting.
     */
    public function test_the_setting_is_inherited_from_the_form(): void
    {
        BF::open(['url' => '/foo', 'escape' => true]);
        $html = (string) BF::text('q', '<b>Bold</b>');
        BF::close();

        $this->assertStringContainsString('&lt;b&gt;Bold&lt;/b&gt;', $html);
    }

    // ## THE HELP AND SUCCESS SINKS #############################################

    public function test_help_text_is_escaped(): void
    {
        $this->assertStringContainsString(
            '<small id="q-help" class="form-text">&lt;b&gt;Bold&lt;/b&gt; &amp; co</small>',
            (string) BF::text('q', 'Q', null, ['help' => '<b>Bold</b> & co', 'escape' => true]),
        );
    }

    public function test_a_htmlable_help_text_is_emitted_verbatim(): void
    {
        $this->assertStringContainsString(
            '<small id="q-help" class="form-text"><b>Bold</b></small>',
            (string) BF::text('q', 'Q', null, ['help' => new HtmlString('<b>Bold</b>'), 'escape' => true]),
        );
    }

    public function test_a_valid_feedback_message_is_escaped(): void
    {
        $this->withErrors(['other' => 'Other is invalid.']);

        $html = (string) BF::text('q', 'Q', null, [
            'show_valid_feedback' => true,
            'success' => '<b>Looks good</b>',
            'escape' => true,
        ]);

        $this->assertStringContainsString(
            '<div class="valid-feedback" id="q-valid">&lt;b&gt;Looks good&lt;/b&gt;</div>',
            $html,
        );
    }

    // ## THE ADDON SINK #########################################################
    //
    // The truth table, all four rows (see docs/llm/input-groups.md):
    //
    //   value              | escape => false          | escape => true
    //   -------------------|--------------------------|--------------------------
    //   string, no tag     | escaped + wrapped        | escaped + wrapped
    //   string, with tag   | raw, unwrapped           | escaped + wrapped
    //   Htmlable, no tag   | escaped + wrapped        | escaped + wrapped
    //   Htmlable, with tag | raw, unwrapped           | raw, unwrapped

    public function test_a_tag_free_string_addon_is_escaped_and_wrapped(): void
    {
        $this->assertStringContainsString(
            '<span class="input-group-text">a &amp; b</span>',
            (string) BF::text('amt', 'Amt', null, ['prepend' => 'a & b', 'escape' => true]),
        );
    }

    /**
     * The heuristic is retired: a value that happens to carry a tag no longer escapes the
     * escaping. This is the regime the setting exists for.
     */
    public function test_a_string_addon_carrying_a_tag_is_escaped_and_wrapped(): void
    {
        $html = (string) BF::text('amt', 'Amt', null, ['prepend' => '<script>alert(1)</script>', 'escape' => true]);

        $this->assertStringContainsString(
            '<span class="input-group-text">&lt;script&gt;alert(1)&lt;/script&gt;</span>',
            $html,
        );
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_a_htmlable_addon_carrying_a_tag_stays_raw_and_unwrapped(): void
    {
        $this->assertStringContainsString(
            '<div class="input-group"><button type="button">Go</button><input id="amt"',
            (string) BF::text('amt', 'Amt', null, [
                'prepend' => new HtmlString('<button type="button">Go</button>'),
                'escape' => true,
            ]),
        );
    }

    /**
     * A tag-free Htmlable is still wrapped as a text addon: the bypass skips the escaping
     * decision, not the wrapping one. This is what keeps a `<x-slot:prepend>$</x-slot:prepend>`
     * rendering its .input-group-text span.
     */
    public function test_a_tag_free_htmlable_addon_stays_in_the_text_regime(): void
    {
        $this->assertStringContainsString(
            '<span class="input-group-text">$</span>',
            (string) BF::text('amt', 'Amt', null, ['prepend' => new HtmlString('$'), 'escape' => true]),
        );
    }

    public function test_an_addon_array_resolves_each_item_on_its_own(): void
    {
        $html = (string) BF::text('amt', 'Amt', null, [
            'prepend' => ['<b>$</b>', new HtmlString('<button type="button">Go</button>')],
            'escape' => true,
        ]);

        $this->assertStringContainsString('<span class="input-group-text">&lt;b&gt;$&lt;/b&gt;</span>', $html);
        $this->assertStringContainsString('<button type="button">Go</button>', $html);
    }

    // ## THE PRIMITIVE ##########################################################

    public function test_content_is_emitted_verbatim_by_default(): void
    {
        $this->assertSame('<b>x</b> & co', BF::html()->content('<b>x</b> & co'));
    }

    public function test_content_is_escaped_on_demand(): void
    {
        $this->assertSame('&lt;b&gt;x&lt;/b&gt; &amp; co', BF::html()->content('<b>x</b> & co', true));
    }

    /**
     * The package-wide policy: htmlspecialchars without double-encoding, so an already-encoded
     * entity survives a round trip instead of becoming &amp;amp;.
     */
    public function test_content_never_double_encodes(): void
    {
        $this->assertSame('a &amp; b', BF::html()->content('a &amp; b', true));
    }

    public function test_content_emits_a_htmlable_verbatim_in_both_modes(): void
    {
        $value = new HtmlString('<b>x</b> & co');

        $this->assertSame('<b>x</b> & co', BF::html()->content($value));
        $this->assertSame('<b>x</b> & co', BF::html()->content($value, true));
    }

    public function test_content_resolves_null_to_an_empty_string(): void
    {
        $this->assertSame('', BF::html()->content(null, true));
    }

    /**
     * addonText() escapes with the same policy, so a currency or unit addon carrying an entity is
     * not double-encoded on its way into the .input-group-text span.
     */
    public function test_a_text_addon_is_escaped_without_double_encoding(): void
    {
        $this->assertStringContainsString(
            '<span class="input-group-text">a &amp; b</span>',
            (string) BF::text('amt', 'Amt', null, ['prepend' => 'a &amp; b']),
        );
    }
}
