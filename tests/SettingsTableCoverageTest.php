<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Bgaze\BootstrapForm\BootstrapForm;
use Bgaze\BootstrapForm\Support\FormElements;
use Bgaze\BootstrapForm\Support\Input;
use ReflectionClass;
use ReflectionProperty;

/**
 * The documented settings reference must match the settings the code actually recognizes, in BOTH
 * directions: a setting the library honours but nobody documented is as much a defect as a setting
 * the guide describes and the library does not have.
 *
 * This validates the hand-written Markdown rather than generating it, so the guide stays a document
 * a human writes -- but it can no longer drift from the code without turning the suite red.
 */
class SettingsTableCoverageTest extends TestCase
{
    private const DOC = __DIR__.'/../docs/llm/options-and-attributes.md';

    /**
     * Set on the Input during configure(), never read from the caller's options array, so they are
     * not option names and have no row of their own. The hub documents them in its collision table
     * instead, because passing them as options silently loses them.
     */
    private const NOT_OPTION_KEYS = ['name', 'value', 'errors'];

    public function test_every_recognized_setting_is_documented_and_vice_versa(): void
    {
        $documented = $this->documentedSettings();
        $recognized = $this->recognizedSettings();

        $this->assertSame(
            $recognized,
            $documented,
            "docs/llm/options-and-attributes.md is out of sync with the code.\n"
            .'Undocumented settings: '.(implode(', ', array_diff($recognized, $documented)) ?: 'none')."\n"
            .'Documented but unknown: '.(implode(', ', array_diff($documented, $recognized)) ?: 'none'),
        );
    }

    public function test_every_documented_setting_names_an_existing_oracle(): void
    {
        foreach ($this->referenceRows() as $names => $anchor) {
            $this->assertNotSame('', $anchor, "no oracle anchored for: {$names}");

            $this->assertFileExists(
                __DIR__.'/../'.$anchor,
                "the oracle anchored for {$names} does not exist: {$anchor}",
            );
        }
    }

    // ## THE CODE ###############################################################

    /**
     * @return list<string> the option names the library consumes, sorted
     */
    private function recognizedSettings(): array
    {
        $keys = [];

        foreach ([
            fn () => BF::text('x'),
            fn () => BF::textarea('x'),
            fn () => BF::select('x'),
            fn () => BF::file('x'),
            fn () => BF::range('x'),
            fn () => BF::checkbox('x'),
            fn () => BF::radio('x'),
            fn () => BF::checkboxes('x'),
            fn () => BF::radios('x'),
        ] as $make) {
            /** @var Input $input */
            $input = $make();
            $property = new ReflectionProperty($input, 'settings');
            $property->setAccessible(true);
            $keys = array_merge($keys, $property->getValue($input)->keys()->all());
        }

        $keys = array_merge(
            array_diff($keys, self::NOT_OPTION_KEYS),
            (new ReflectionClass(BootstrapForm::class))->getConstant('RESERVED'),
            (new ReflectionClass(FormElements::class))->getConstant('RESERVED'),
        );

        $keys = array_unique($keys);
        sort($keys);

        return array_values($keys);
    }

    // ## THE DOCUMENT ###########################################################

    /**
     * @return list<string> the setting names of the reference tables, sorted
     */
    private function documentedSettings(): array
    {
        $names = [];

        foreach (array_keys($this->referenceRows()) as $cell) {
            foreach (explode('/', $cell) as $name) {
                $names[] = trim($name);
            }
        }

        $names = array_unique($names);
        sort($names);

        return array_values($names);
    }

    /**
     * The reference tables, as "first cell" => "anchor cell".
     *
     * @return array<string, string>
     */
    private function referenceRows(): array
    {
        $doc = file_get_contents(self::DOC);
        $this->assertNotFalse($doc, 'the settings reference could not be read');

        $start = strpos($doc, '## Reference — every setting');
        $this->assertNotFalse($start, 'the settings reference section is gone from the document');

        $end = strpos($doc, "\n## ", $start + 1);
        $section = substr($doc, $start, $end === false ? null : $end - $start);

        $rows = [];

        foreach (explode("\n", $section) as $line) {
            $line = trim($line);

            if (! str_starts_with($line, '|') || str_contains($line, '---')) {
                continue;
            }

            $cells = array_map('trim', explode('|', trim($line, '|')));
            $name = str_replace('`', '', $cells[0]);

            if ($name === '' || $name === 'Setting') {
                continue;
            }

            $rows[$name] = str_replace('`', '', end($cells));
        }

        $this->assertNotEmpty($rows, 'no reference row could be parsed');

        return $rows;
    }
}
