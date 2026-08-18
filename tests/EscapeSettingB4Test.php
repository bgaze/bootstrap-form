<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\HtmlString;

/**
 * The `escape` regime is version-agnostic: the escaping happens in the shared addon resolution,
 * only the placement of the wrapper differs. These pin the frozen Bootstrap 4 markup around it.
 */
class EscapeSettingB4Test extends Bootstrap4TestCase
{
    public function test_an_escaped_text_addon_keeps_the_bootstrap4_wrapper(): void
    {
        $this->assertStringContainsString(
            '<div class="input-group-prepend"><span class="input-group-text">&lt;script&gt;x&lt;/script&gt;</span></div>',
            (string) BF::text('amt', 'Amt', null, ['prepend' => '<script>x</script>', 'escape' => true]),
        );
    }

    /**
     * The custom-file browse label is a content sink too -- and unlike the required mark it is a
     * user-facing label an application will realistically feed from a lang file.
     */
    public function test_the_custom_file_browse_label_is_escaped(): void
    {
        $this->assertStringContainsString(
            '<label for="avatar" class="custom-file-label">&lt;b&gt;Pick&lt;/b&gt; a file</label>',
            (string) BF::file('avatar', null, ['custom' => true, 'text' => '<b>Pick</b> a file', 'escape' => true]),
        );
    }

    public function test_a_htmlable_custom_file_browse_label_is_emitted_verbatim(): void
    {
        $this->assertStringContainsString(
            '<label for="avatar" class="custom-file-label"><b>Pick</b> a file</label>',
            (string) BF::file('avatar', null, [
                'custom' => true,
                'text' => new HtmlString('<b>Pick</b> a file'),
                'escape' => true,
            ]),
        );
    }

    public function test_a_htmlable_addon_stays_raw_inside_the_bootstrap4_wrapper(): void
    {
        $this->assertStringContainsString(
            '<div class="input-group-prepend"><button type="button">Go</button></div>',
            (string) BF::text('amt', 'Amt', null, [
                'prepend' => new HtmlString('<button type="button">Go</button>'),
                'escape' => true,
            ]),
        );
    }
}
