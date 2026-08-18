<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * The textarea sizing contract, which is not the one the historical Collective API had.
 *
 * "size" is consumed as the package's Bootstrap control-size setting before it can reach the
 * textarea renderer, so the historical "COLSxROWS" form only survives through the literal
 * escape. Dimensions otherwise come from the plain cols / rows HTML attributes, which are
 * always emitted and default to 50 x 10.
 */
class TextareaSizeTest extends TestCase
{
    public function test_size_is_a_bootstrap_control_size_like_any_other_text_like_input(): void
    {
        $expected = '<div id="bio-group" class="mb-3"><label for="bio" class="form-label">Bio</label>'
            .'<div><textarea id="bio" class="form-control form-control-lg" name="bio" cols="50" rows="10"></textarea></div></div>';

        $this->assertSame($expected, (string) BF::textarea('bio', null, null, ['size' => 'lg']));
    }

    public function test_dimensions_come_from_the_cols_and_rows_attributes(): void
    {
        $expected = '<div id="bio-group" class="mb-3"><label for="bio" class="form-label">Bio</label>'
            .'<div><textarea cols="30" rows="5" id="bio" class="form-control" name="bio"></textarea></div></div>';

        $this->assertSame($expected, (string) BF::textarea('bio', null, null, ['cols' => 30, 'rows' => 5]));
    }

    public function test_cols_and_rows_default_to_50_by_10(): void
    {
        $this->assertStringContainsString('cols="50" rows="10"', (string) BF::textarea('bio'));
    }

    /**
     * The historical COLSxROWS form is reachable, but only through the literal escape: a plain
     * "size" never gets past the setting partition, so it applies neither a size class (the value
     * is not sm|lg) nor any dimension.
     */
    public function test_the_historical_cols_by_rows_form_requires_the_literal_escape(): void
    {
        $expected = '<div id="bio-group" class="mb-3"><label for="bio" class="form-label">Bio</label>'
            .'<div><textarea id="bio" class="form-control" name="bio" cols="30" rows="5"></textarea></div></div>';

        $this->assertSame($expected, (string) BF::textarea('bio', null, null, ['~size' => '30x5']));
    }

    public function test_a_plain_cols_by_rows_size_is_swallowed_and_changes_nothing(): void
    {
        $this->assertSame(
            (string) BF::textarea('bio'),
            (string) BF::textarea('bio', null, null, ['size' => '30x5']),
        );
    }
}
