<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Golden-snapshot oracle for the frozen Bootstrap 4 baseline.
 *
 * Bootstrap 5 is the package default; Bootstrap 4 is frozen for backward compatibility, so this
 * suite pins the version to 4 (via Bootstrap4TestCase) and locks the historical markup byte-for-byte.
 * Goldens live under golden/b4/ and must not drift. Version-agnostic elements (buttons, label, hidden)
 * live in GoldenSnapshotCommonTest (golden/ root); the B5 default in GoldenSnapshotB5Test (golden/b5/).
 *
 * Form open() is intentionally excluded (CSRF token value is non-deterministic).
 * To (re)capture: UPDATE_GOLDEN=1 vendor/bin/phpunit --filter GoldenSnapshotB4
 */
class GoldenSnapshotB4Test extends Bootstrap4TestCase
{
    public static function fixtures(): array
    {
        $names = [
            // Text family
            'text.simple', 'text.value', 'text.help', 'text.size_sm', 'text.prepend_append',
            'text.email', 'text.number', 'text.date', 'text.color',
            'text.search', 'text.month', 'text.week', 'text.datetime_local',
            'text.id_false', 'text.id_explicit',
            'textarea', 'password',
            // Select
            'select.native', 'select.custom', 'select.size_lg', 'select.selected',
            'select.placeholder', 'select.optgroup', 'select.collection_selected',
            // Select child attributes
            'select.option_attributes', 'select.advanced_option', 'select.option_item_wins',
            'select.advanced_optgroup',
            // File / range
            'file.native', 'file.custom', 'range.native', 'range.custom',
            // Checkables
            'check.checkbox', 'check.custom', 'check.switch', 'check.inline',
            'check.label_false', 'check.radio', 'check.checkboxes', 'check.radios',
            // Checkable child attributes
            'check.option_attributes', 'check.advanced_option', 'check.option_id_override',
            // Required mark (version-agnostic behavior; proven on the frozen B4 baseline too)
            'required.text', 'required.checkboxes',
            // Layouts
            'layout.h_text', 'layout.i_text', 'layout.h_checkbox',
            // Floating layout degrades to vertical on Bootstrap 4
            'float.degrades',
            // Validation errors
            'error.text', 'error.checkboxes', 'error.help_describedby',
            // Valid feedback (opt-in)
            'valid.text', 'valid.text_success', 'valid.checkboxes',
            // Value binding
            'old.text', 'old.select', 'old.checkbox', 'model.text', 'model.checkbox',
        ];

        return array_map(fn ($n) => [$n], $names);
    }

    #[DataProvider('fixtures')]
    public function test_golden(string $name): void
    {
        $actual = $this->render($name);

        $path = __DIR__.'/golden/b4/'.$name.'.html';

        if (getenv('UPDATE_GOLDEN') || ! is_file($path)) {
            if (! is_dir(dirname($path))) {
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

                // Select
            case 'select.native': return (string) BF::select('sel', null, $choices);
            case 'select.custom': return (string) BF::select('sel', null, $choices, null, ['custom' => true]);
            case 'select.size_lg': return (string) BF::select('sel', null, $choices, null, ['size' => 'lg']);
            case 'select.selected': return (string) BF::select('sel', null, $choices, 'b');
            case 'select.placeholder': return (string) BF::select('sel', null, $choices, null, ['placeholder' => 'Pick']);
            case 'select.optgroup': return (string) BF::select('sel', null, ['G1' => ['a' => 'A', 'b' => 'B'], 'G2' => ['c' => 'C']]);
            case 'select.collection_selected': return (string) BF::select('sel', null, $choices, collect(['b', 'c']), ['multiple' => true]);

                // Select child attributes
            case 'select.option_attributes': return (string) BF::select('sel', null, $choices, null, ['option_attributes' => ['class' => 'opt']]);
            case 'select.advanced_option': return (string) BF::select('sel', null, ['a' => 'A', ['value' => 'b', 'label' => 'B', 'data-x' => 'y', 'disabled' => true]]);
            case 'select.option_item_wins': return (string) BF::select('sel', null, [['value' => 'a', 'label' => 'A', 'class' => 'special']], null, ['option_attributes' => ['class' => 'base', 'data-g' => '1']]);
            case 'select.advanced_optgroup': return (string) BF::select('sel', null, [
                ['label' => 'Group', 'data-z' => 'yo', 'options' => ['a' => 'A', ['value' => 'b', 'label' => 'B', 'data-x' => 'y']]],
            ], null, ['optgroup_attributes' => ['class' => 'grp']]);

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

                // Checkable child attributes
            case 'check.option_attributes': return (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', 'editor' => 'Editor'], null, ['option_attributes' => ['data-g' => '1']]);
            case 'check.advanced_option': return (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', ['value' => 'editor', 'label' => 'Editor', 'data-x' => 'y']]);
            case 'check.option_id_override': return (string) BF::radios('gender', 'Gender', ['m' => 'Male', ['value' => 'f', 'label' => 'Female', 'id' => 'gender-female']]);

                // Required mark
            case 'required.text': return (string) BF::text('email', 'Email', null, ['required' => true]);
            case 'required.checkboxes': return (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', 'editor' => 'Editor'], null, ['required' => true]);

                // Layouts
            case 'layout.h_text': BF::horizontal(['url' => '/x']);
                $h = (string) BF::text('login');
                BF::close();

                return $h;
            case 'layout.i_text': BF::inline(['url' => '/x']);
                $h = (string) BF::text('login');
                BF::close();

                return $h;
            case 'layout.h_checkbox': BF::horizontal(['url' => '/x']);
                $h = (string) BF::checkbox('accept', 'Accept', 1);
                BF::close();

                return $h;

                // Floating layout degrades to vertical on Bootstrap 4
            case 'float.degrades': return (string) BF::text('email', 'Email address', null, ['layout' => 'floating']);

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

                // Valid feedback (a bag exists on another field, this one is untouched)
            case 'valid.text':
                $this->withErrors(['other' => ['err']]);

                return (string) BF::text('login', null, null, ['show_valid_feedback' => true]);
            case 'valid.text_success':
                $this->withErrors(['other' => ['err']]);

                return (string) BF::text('login', null, null, ['show_valid_feedback' => true, 'success' => 'Looks good!']);
            case 'valid.checkboxes':
                $this->withErrors(['other' => ['err']]);

                return (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin'], null, ['show_valid_feedback' => true, 'success' => 'Nice']);

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
                $h = (string) BF::text('login');
                BF::close();

                return $h;
            case 'model.checkbox':
                BF::open(['model' => new GoldenUser(['accept' => 1]), 'url' => '/x']);
                $h = (string) BF::checkbox('accept', 'Accept', 1);
                BF::close();

                return $h;
        }

        $this->fail("Unknown fixture: {$name}");
    }
}

class GoldenUser extends Model
{
    protected $guarded = [];

    public $timestamps = false;
}
