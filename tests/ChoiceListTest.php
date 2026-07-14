<?php

namespace Bgaze\BootstrapForm\Tests;

use Bgaze\BootstrapForm\Support\ChoiceList;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the rich choice grammar parser (the SSOT). Covers normalization of the
 * select ($list / $optionsAttributes / $optgroupsAttributes) and checkable shapes, the
 * "all children" bag ⊕ per-item merge (item wins), and every strict-validation throw.
 */
class ChoiceListTest extends TestCase
{
    // ## SELECT — normalization ################################################

    public function test_select_simple_options(): void
    {
        $this->assertSame(
            [['a' => 'A', 'b' => 'B'], [], []],
            ChoiceList::select(['a' => 'A', 'b' => 'B'])
        );
    }

    public function test_select_option_attributes_apply_to_every_option(): void
    {
        $this->assertSame(
            [['a' => 'A', 'b' => 'B'], ['a' => ['class' => 'x'], 'b' => ['class' => 'x']], []],
            ChoiceList::select(['a' => 'A', 'b' => 'B'], ['class' => 'x'])
        );
    }

    public function test_select_advanced_option_carries_attributes_and_ignores_key(): void
    {
        $this->assertSame(
            [['b' => 'B'], ['b' => ['data-x' => 'y']], []],
            ChoiceList::select(['ignored' => ['value' => 'b', 'label' => 'B', 'data-x' => 'y']])
        );
    }

    public function test_select_item_attributes_win_over_the_all_bag(): void
    {
        $this->assertSame(
            [['a' => 'A'], ['a' => ['class' => 'special', 'data-g' => '1']], []],
            ChoiceList::select([['value' => 'a', 'label' => 'A', 'class' => 'special']], ['class' => 'base', 'data-g' => '1'])
        );
    }

    public function test_select_simple_optgroup(): void
    {
        $this->assertSame(
            [['G1' => ['a' => 'A', 'b' => 'B']], [], []],
            ChoiceList::select(['G1' => ['a' => 'A', 'b' => 'B']])
        );
    }

    public function test_select_simple_optgroup_gets_the_optgroup_bag(): void
    {
        $this->assertSame(
            [['G1' => ['a' => 'A']], [], ['G1' => ['class' => 'g']]],
            ChoiceList::select(['G1' => ['a' => 'A']], [], ['class' => 'g'])
        );
    }

    public function test_select_advanced_optgroup(): void
    {
        $this->assertSame(
            [
                ['Group' => ['a' => 'A', 'b' => 'B']],
                ['Group' => ['b' => ['data-x' => 'y']]],
                ['Group' => ['class' => 'grp', 'data-z' => 'yo']],
            ],
            ChoiceList::select(
                [['label' => 'Group', 'data-z' => 'yo', 'options' => ['a' => 'A', ['value' => 'b', 'label' => 'B', 'data-x' => 'y']]]],
                [],
                ['class' => 'grp']
            )
        );
    }

    // ## CHECKABLES — normalization ############################################

    public function test_checkables_simple_and_advanced(): void
    {
        $this->assertSame(
            [
                ['value' => 'a', 'label' => 'A', 'attributes' => ['data-g' => '1']],
                ['value' => 'b', 'label' => 'B', 'attributes' => ['data-g' => '1', 'data-x' => 'y']],
            ],
            ChoiceList::checkables(['a' => 'A', ['value' => 'b', 'label' => 'B', 'data-x' => 'y']], ['data-g' => '1'])
        );
    }

    // ## STRICT VALIDATION — throws ############################################

    public function test_throws_when_advanced_option_misses_label(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChoiceList::select([['value' => 'a']]);
    }

    public function test_throws_when_advanced_option_misses_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChoiceList::select([['label' => 'A']]);
    }

    public function test_throws_when_advanced_optgroup_misses_label(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChoiceList::select([['options' => ['a' => 'A']]]);
    }

    public function test_throws_when_advanced_optgroup_options_is_not_an_array(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChoiceList::select([['label' => 'G', 'options' => 'nope']]);
    }

    public function test_throws_on_ambiguous_numeric_keyed_array(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChoiceList::select([['a', 'b']]);
    }

    public function test_throws_on_nested_optgroup(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChoiceList::select(['G' => ['Sub' => ['a' => 'A']]]);
    }

    public function test_throws_when_checkables_get_an_optgroup(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChoiceList::checkables([['label' => 'G', 'options' => ['a' => 'A']]]);
    }
}
