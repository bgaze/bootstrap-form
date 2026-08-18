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
