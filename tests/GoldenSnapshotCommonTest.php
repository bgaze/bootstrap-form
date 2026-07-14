<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Golden-snapshot oracle for the version-agnostic (transverse) elements: buttons, link, label and
 * hidden input render identically under Bootstrap 4 and 5 (no version driver delta). Rendered under
 * the package default and locked once, at the golden/ root. Version-specific fixtures live in
 * GoldenSnapshotB4Test (golden/b4/) and GoldenSnapshotB5Test (golden/b5/).
 *
 * To (re)capture: UPDATE_GOLDEN=1 vendor/bin/phpunit --filter GoldenSnapshotCommon
 */
class GoldenSnapshotCommonTest extends TestCase
{
    public static function fixtures(): array
    {
        $names = ['el.submit', 'el.reset', 'el.button', 'el.link', 'el.label', 'hidden'];

        return array_map(fn ($n) => [$n], $names);
    }

    #[DataProvider('fixtures')]
    public function test_golden(string $name): void
    {
        $actual = $this->render($name);

        $path = __DIR__.'/golden/'.$name.'.html';

        if (getenv('UPDATE_GOLDEN') || ! is_file($path)) {
            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            file_put_contents($path, $actual);
        }

        $this->assertSame(file_get_contents($path), $actual, "golden mismatch: {$name}");
    }

    protected function render(string $name): string
    {
        switch ($name) {
            case 'el.submit': return (string) BF::submit('Save');
            case 'el.reset': return (string) BF::reset('Reset');
            case 'el.button': return (string) BF::button('Go');
            case 'el.link': return (string) BF::link('/home', 'Home');
            case 'el.label': return (string) BF::label('field', 'My label');
            case 'hidden': return (string) BF::hidden('token_field', 'abc');
        }

        $this->fail("Unknown fixture: {$name}");
    }
}
