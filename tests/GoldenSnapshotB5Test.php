<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Golden-snapshot oracle for the Bootstrap 5 default rendering.
 *
 * Bootstrap 5 is the package default; this suite locks its markup byte-for-byte for the
 * fixtures showcased in the docs (docs/llm/*), which lift these exact strings. The frozen
 * Bootstrap 4 baseline lives in GoldenSnapshotTest. Goldens live under golden/b5/.
 *
 * To (re)capture: UPDATE_GOLDEN=1 vendor/bin/phpunit --filter GoldenSnapshotB5
 */
class GoldenSnapshotB5Test extends TestCase
{
    protected function defineEnvironment($app)
    {
        $app['config']->set('bootstrap_form.bootstrap_version', 5);
    }

    public static function fixtures(): array
    {
        $names = [
            'text', 'text.help', 'text.prepend_append',
            'select.native', 'select.selected', 'select.optgroup',
            'checkbox', 'checkbox.switch', 'radios.checked', 'checkboxes.option_attributes',
            'required.text', 'required.text_html', 'required.checkbox', 'required.checkboxes', 'required.radios',
            'file', 'range',
            'layout.horizontal', 'layout.horizontal_checkbox', 'layout.inline',
            'float.text', 'float.select', 'float.textarea', 'float.addon', 'float.checkbox',
            'model.text', 'old.text', 'error.text', 'valid.text_success',
        ];

        return array_map(fn ($n) => [$n], $names);
    }

    #[DataProvider('fixtures')]
    public function test_golden(string $name): void
    {
        $actual = $this->render($name);

        $path = __DIR__.'/golden/b5/'.$name.'.html';

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
        $choices = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        switch ($name) {
            case 'text': return (string) BF::text('field');
            case 'text.help': return (string) BF::text('login', null, null, ['help' => 'Some help']);
            case 'text.prepend_append': return (string) BF::text('amount', 'Amount', null, ['prepend' => '$', 'append' => '.00']);

            case 'select.native': return (string) BF::select('sel', null, $choices);
            case 'select.selected': return (string) BF::select('sel', null, $choices, 'b');
            case 'select.optgroup': return (string) BF::select('sel', null, ['G1' => ['a' => 'A', 'b' => 'B'], 'G2' => ['c' => 'C']]);

            case 'checkbox': return (string) BF::checkbox('accept', 'Accept');
            case 'checkbox.switch': return (string) BF::checkbox('accept', 'Accept', 1, null, ['switch' => true]);
            case 'radios.checked': return (string) BF::radios('gender', 'Gender', ['m' => 'Male', 'f' => 'Female'], 'f');
            case 'checkboxes.option_attributes': return (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', 'editor' => 'Editor'], null, ['option_attributes' => ['data-g' => '1']]);

            case 'required.text': return (string) BF::text('email', 'Email', null, ['required' => true]);
            case 'required.text_html': return (string) BF::text('email', 'Email', null, ['required' => true, 'required_mark' => ' <span class="text-danger">*</span>']);
            case 'required.checkbox': return (string) BF::checkbox('accept', 'I accept', 1, null, ['required' => true]);
            case 'required.checkboxes': return (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', 'editor' => 'Editor'], null, ['required' => true]);
            case 'required.radios': return (string) BF::radios('gender', 'Gender', ['m' => 'Male', 'f' => 'Female'], null, ['required' => true]);

            case 'file': return (string) BF::file('doc');
            case 'range': return (string) BF::range('vol');

            case 'float.text': return (string) BF::text('email', 'Email address', null, ['layout' => 'floating']);
            case 'float.select': return (string) BF::select('country', 'Country', ['fr' => 'France'], null, ['layout' => 'floating']);
            case 'float.textarea': return (string) BF::textarea('bio', 'Bio', null, ['layout' => 'floating']);
            case 'float.addon': return (string) BF::text('amount', 'Amount', null, ['layout' => 'floating', 'prepend' => '$']);
            case 'float.checkbox': return (string) BF::checkbox('accept', 'Accept', 1, null, ['layout' => 'floating']);

            case 'layout.horizontal': BF::horizontal(['url' => '/x']);
                $h = (string) BF::text('login');
                BF::close();

                return $h;
            case 'layout.horizontal_checkbox': BF::horizontal(['url' => '/x']);
                $h = (string) BF::checkbox('accept', 'Accept', 1);
                BF::close();

                return $h;
            case 'layout.inline': BF::inline(['url' => '/x']);
                $h = (string) BF::text('login');
                BF::close();

                return $h;

            case 'model.text': BF::open(['model' => new GoldenUser(['login' => 'jdoe']), 'url' => '/x']);
                $h = (string) BF::text('login');
                BF::close();

                return $h;
            case 'old.text':
                $this->withOldInput(['login' => 'old']);

                return (string) BF::text('login');
            case 'error.text':
                $this->withErrors(['login' => ['The login field is required.']]);

                return (string) BF::text('login');
            case 'valid.text_success':
                $this->withErrors(['other' => ['err']]);

                return (string) BF::text('login', null, null, ['show_valid_feedback' => true, 'success' => 'Looks good!']);
        }

        $this->fail("Unknown fixture: {$name}");
    }
}
