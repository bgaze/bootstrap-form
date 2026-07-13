<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Exhaustive golden-snapshot oracle: renders a fixed fixture matrix and asserts the
 * output byte-for-byte against committed `.golden` files captured on the current code.
 * After the dependency-removal refactor these must still match exactly (iso proof).
 *
 * Form open() is intentionally excluded (CSRF token value is non-deterministic).
 * To (re)capture the baseline: UPDATE_GOLDEN=1 vendor/bin/phpunit --filter GoldenSnapshot
 */
class GoldenSnapshotTest extends TestCase
{
    public static function fixtures(): array
    {
        $names = [
            // Text family (Bootstrap 4)
            'text.simple', 'text.value', 'text.help', 'text.size_sm', 'text.prepend_append',
            'text.email', 'text.number', 'text.date', 'text.color',
            'text.search', 'text.month', 'text.week', 'text.datetime_local',
            'text.id_false', 'text.id_explicit',
            'textarea', 'password', 'hidden',
            // Select
            'select.native', 'select.custom', 'select.size_lg', 'select.selected',
            'select.placeholder', 'select.optgroup', 'select.collection_selected',
            // File / range
            'file.native', 'file.custom', 'range.native', 'range.custom',
            // Checkables
            'check.checkbox', 'check.custom', 'check.switch', 'check.inline',
            'check.label_false', 'check.radio', 'check.checkboxes', 'check.radios',
            // Misc elements
            'el.submit', 'el.reset', 'el.button', 'el.link', 'el.label',
            // Layouts
            'layout.h_text', 'layout.i_text', 'layout.h_checkbox',
            // Bootstrap 5 (per-field override)
            'b5.text', 'b5.checkbox', 'b5.switch', 'b5.select', 'b5.file', 'b5.range', 'b5.error',
            // Validation errors
            'error.text', 'error.checkboxes', 'error.help_describedby',
            // Value binding
            'old.text', 'old.select', 'old.checkbox', 'model.text', 'model.checkbox',
        ];

        return array_map(fn ($n) => [$n], $names);
    }

    #[DataProvider('fixtures')]
    public function test_golden(string $name): void
    {
        $actual = $this->render($name);

        $path = __DIR__ . '/golden/' . $name . '.html';

        if (getenv('UPDATE_GOLDEN') || !is_file($path)) {
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            file_put_contents($path, $actual);
        }

        $this->assertSame(file_get_contents($path), $actual, "golden mismatch: {$name}");
    }

    /**
     * Produce the HTML for a named fixture (context set inline when needed).
     */
    protected function render(string $name): string
    {
        $choices = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        switch ($name) {
            // Text family
            case 'text.simple': return (string) BF::text('field');
            case 'text.value': return (string) BF::text('login', 'Your login', 'john');
            case 'text.help': return (string) BF::text('login', null, null, ['help' => 'Some help']);
            case 'text.size_sm': return (string) BF::text('login', null, null, ['size' => 'sm']);
            case 'text.prepend_append': return (string) BF::text('amount', null, null, ['prepend' => '$', 'append' => '.00']);
            case 'text.email': return (string) BF::email('mail');
            case 'text.number': return (string) BF::number('qty');
            case 'text.date': return (string) BF::date('day');
            case 'text.color': return (string) BF::color('hue');
            case 'text.search': return (string) BF::search('q');
            case 'text.month': return (string) BF::month('m');
            case 'text.week': return (string) BF::week('w');
            case 'text.datetime_local': return (string) BF::datetimeLocal('dt');
            case 'text.id_false': return (string) BF::text('field', 'My label', null, ['id' => false]);
            case 'text.id_explicit': return (string) BF::text('field', null, null, ['id' => 'custom_id']);
            case 'textarea': return (string) BF::textarea('bio');
            case 'password': return (string) BF::password('secret');
            case 'hidden': return (string) BF::hidden('token_field', 'abc');

            // Select
            case 'select.native': return (string) BF::select('sel', null, $choices);
            case 'select.custom': return (string) BF::select('sel', null, $choices, null, ['custom' => true]);
            case 'select.size_lg': return (string) BF::select('sel', null, $choices, null, ['size' => 'lg']);
            case 'select.selected': return (string) BF::select('sel', null, $choices, 'b');
            case 'select.placeholder': return (string) BF::select('sel', null, $choices, null, ['placeholder' => 'Pick']);
            case 'select.optgroup': return (string) BF::select('sel', null, ['G1' => ['a' => 'A', 'b' => 'B'], 'G2' => ['c' => 'C']]);
            case 'select.collection_selected': return (string) BF::select('sel', null, $choices, collect(['b', 'c']), ['multiple' => true]);

            // File / range
            case 'file.native': return (string) BF::file('doc');
            case 'file.custom': return (string) BF::file('doc', null, ['custom' => true]);
            case 'range.native': return (string) BF::range('vol');
            case 'range.custom': return (string) BF::range('vol', null, null, ['custom' => true]);

            // Checkables
            case 'check.checkbox': return (string) BF::checkbox('accept', 'Accept', 1);
            case 'check.custom': return (string) BF::checkbox('accept', 'Accept', 1, null, ['custom' => true]);
            case 'check.switch': return (string) BF::checkbox('accept', 'Accept', 1, null, ['switch' => true]);
            case 'check.inline': return (string) BF::checkbox('accept', 'Accept', 1, null, ['inline' => true]);
            case 'check.label_false': return (string) BF::checkbox('accept', false, 1);
            case 'check.radio': return (string) BF::radio('gender', 'Female', 'f');
            case 'check.checkboxes': return (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', 'editor' => 'Editor']);
            case 'check.radios': return (string) BF::radios('gender', 'Gender', ['m' => 'Male', 'f' => 'Female']);

            // Misc elements
            case 'el.submit': return (string) BF::submit('Save');
            case 'el.reset': return (string) BF::reset('Reset');
            case 'el.button': return (string) BF::button('Go');
            case 'el.link': return (string) BF::link('/home', 'Home');
            case 'el.label': return (string) BF::label('field', 'My label');

            // Layouts
            case 'layout.h_text': BF::horizontal(['url' => '/x']); $h = (string) BF::text('login'); BF::close(); return $h;
            case 'layout.i_text': BF::inline(['url' => '/x']); $h = (string) BF::text('login'); BF::close(); return $h;
            case 'layout.h_checkbox': BF::horizontal(['url' => '/x']); $h = (string) BF::checkbox('accept', 'Accept', 1); BF::close(); return $h;

            // Bootstrap 5 (per-field override)
            case 'b5.text': return (string) BF::text('login', null, null, ['bootstrap_version' => 5]);
            case 'b5.checkbox': return (string) BF::checkbox('accept', 'Accept', 1, null, ['bootstrap_version' => 5]);
            case 'b5.switch': return (string) BF::checkbox('accept', 'Accept', 1, null, ['bootstrap_version' => 5, 'switch' => true]);
            case 'b5.select': return (string) BF::select('sel', null, $choices, null, ['bootstrap_version' => 5]);
            case 'b5.file': return (string) BF::file('doc', null, ['bootstrap_version' => 5]);
            case 'b5.range': return (string) BF::range('vol', null, null, ['bootstrap_version' => 5]);
            case 'b5.error':
                $this->withErrors(['login' => ['The login field is required.']]);
                return (string) BF::text('login', null, null, ['bootstrap_version' => 5]);

            // Validation errors
            case 'error.text':
                $this->withErrors(['login' => ['The login field is required.']]);
                return (string) BF::text('login');
            case 'error.checkboxes':
                $this->withErrors(['roles' => ['Pick a role.']]);
                return (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', 'editor' => 'Editor']);
            case 'error.help_describedby':
                $this->withErrors(['login' => ['The login field is required.']]);
                return (string) BF::text('login', null, null, ['help' => 'Some help']);

            // Value binding
            case 'old.text':
                $this->withOldInput(['login' => 'old']);
                return (string) BF::text('login');
            case 'old.select':
                $this->withOldInput(['sel' => 'b']);
                return (string) BF::select('sel', null, $choices);
            case 'old.checkbox':
                $this->withOldInput(['accept' => '1']);
                return (string) BF::checkbox('accept', 'Accept', 1);
            case 'model.text':
                BF::open(['model' => new GoldenUser(['login' => 'jdoe']), 'url' => '/x']);
                $h = (string) BF::text('login'); BF::close(); return $h;
            case 'model.checkbox':
                BF::open(['model' => new GoldenUser(['accept' => 1]), 'url' => '/x']);
                $h = (string) BF::checkbox('accept', 'Accept', 1); BF::close(); return $h;
        }

        $this->fail("Unknown fixture: {$name}");
    }
}

class GoldenUser extends Model
{
    protected $guarded = [];
    public $timestamps = false;
}
