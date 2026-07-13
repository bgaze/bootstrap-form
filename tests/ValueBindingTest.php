<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Database\Eloquent\Model;

/**
 * Characterization tests for value binding — the behaviors that were previously
 * UNTESTED (old input re-population, model binding, select "selected" and checkable
 * "checked" state resolution). These lock the current byte-exact output so the
 * dependency-removal refactor can be proven iso.
 */
class ValueBindingTest extends TestCase
{
    ### OLD INPUT ##############################################################

    public function test_old_input_repopulates_text(): void
    {
        $this->withOldInput(['login' => 'oldlogin']);

        $expected = '<div id="login-group" class="form-group"><label for="login">Login</label>'
            . '<div><input id="login" class="form-control" name="login" type="text" value="oldlogin"></div></div>';

        $this->assertSame($expected, (string) BF::text('login'));
    }

    public function test_old_input_repopulates_textarea(): void
    {
        $this->withOldInput(['bio' => 'oldbio']);

        $expected = '<div id="bio-group" class="form-group"><label for="bio">Bio</label>'
            . '<div><textarea id="bio" class="form-control" name="bio" cols="50" rows="10">oldbio</textarea></div></div>';

        $this->assertSame($expected, (string) BF::textarea('bio'));
    }

    public function test_old_input_selects_option(): void
    {
        $this->withOldInput(['color_pick' => 'red']);

        $expected = '<div id="color_pick-group" class="form-group"><label for="color_pick">Color pick</label>'
            . '<div><select id="color_pick" class="form-control" name="color_pick">'
            . '<option value="red" selected="selected">Red</option><option value="blue">Blue</option></select></div></div>';

        $this->assertSame($expected, (string) BF::select('color_pick', null, ['red' => 'Red', 'blue' => 'Blue']));
    }

    public function test_old_input_checks_checkbox(): void
    {
        $this->withOldInput(['accept' => '1']);

        $expected = '<div id="accept-group" class="form-group"><div><div class="form-check">'
            . '<input id="accept" class="form-check-input" checked="checked" name="accept" type="checkbox" value="1">'
            . '<label for="accept" class="form-check-label">Accept</label></div></div></div>';

        $this->assertSame($expected, (string) BF::checkbox('accept', 'Accept', 1));
    }

    public function test_old_input_checks_checkboxes_collection(): void
    {
        $this->withOldInput(['roles' => ['admin', 'editor']]);

        $expected = '<div id="roles-group" class="form-group"><label for="roles">Roles</label><div>'
            . '<div class="form-check"><input id="roles-admin" class="form-check-input" checked="checked" name="roles" type="checkbox" value="admin"><label for="roles-admin" class="form-check-label">Admin</label></div>'
            . '<div class="form-check"><input id="roles-editor" class="form-check-input" checked="checked" name="roles" type="checkbox" value="editor"><label for="roles-editor" class="form-check-label">Editor</label></div>'
            . '<div class="form-check"><input id="roles-guest" class="form-check-input" name="roles" type="checkbox" value="guest"><label for="roles-guest" class="form-check-label">Guest</label></div>'
            . '</div></div>';

        $this->assertSame($expected, (string) BF::checkboxes('roles', 'Roles', ['admin' => 'Admin', 'editor' => 'Editor', 'guest' => 'Guest']));
    }

    public function test_old_input_checks_radio(): void
    {
        $this->withOldInput(['gender' => 'f']);

        $expected = '<div id="gender-group" class="form-group"><div><div class="form-check">'
            . '<input id="gender" class="form-check-input" checked="checked" name="gender" type="radio" value="f">'
            . '<label for="gender" class="form-check-label">Female</label></div></div></div>';

        $this->assertSame($expected, (string) BF::radio('gender', 'Female', 'f'));
    }

    ### MODEL BINDING ##########################################################

    public function test_model_binds_text_value(): void
    {
        $model = new ValueBindingUser(['login' => 'jdoe']);

        BF::open(['model' => $model, 'url' => '/foo']);
        $expected = '<div id="login-group" class="form-group"><label for="login">Login</label>'
            . '<div><input id="login" class="form-control" name="login" type="text" value="jdoe"></div></div>';
        $this->assertSame($expected, (string) BF::text('login'));
        BF::close();
    }

    public function test_model_binds_textarea_value(): void
    {
        $model = new ValueBindingUser(['bio' => 'Hello']);

        BF::open(['model' => $model, 'url' => '/foo']);
        $expected = '<div id="bio-group" class="form-group"><label for="bio">Bio</label>'
            . '<div><textarea id="bio" class="form-control" name="bio" cols="50" rows="10">Hello</textarea></div></div>';
        $this->assertSame($expected, (string) BF::textarea('bio'));
        BF::close();
    }

    public function test_model_binds_checkbox_state(): void
    {
        $model = new ValueBindingUser(['accept' => 1]);

        BF::open(['model' => $model, 'url' => '/foo']);
        $expected = '<div id="accept-group" class="form-group"><div><div class="form-check">'
            . '<input id="accept" class="form-check-input" checked="checked" name="accept" type="checkbox" value="1">'
            . '<label for="accept" class="form-check-label">Accept</label></div></div></div>';
        $this->assertSame($expected, (string) BF::checkbox('accept', 'Accept', 1));
        BF::close();
    }

    ### SELECT SELECTED ########################################################

    public function test_select_selected_scalar(): void
    {
        $expected = '<div id="c-group" class="form-group"><label for="c">C</label>'
            . '<div><select id="c" class="form-control" name="c">'
            . '<option value="a">A</option><option value="b" selected="selected">B</option></select></div></div>';

        $this->assertSame($expected, (string) BF::select('c', null, ['a' => 'A', 'b' => 'B'], 'b'));
    }

    public function test_select_selected_array_multiple(): void
    {
        $expected = '<div id="c-group" class="form-group"><label for="c">C</label>'
            . '<div><select multiple id="c" class="form-control" name="c">'
            . '<option value="a" selected="selected">A</option><option value="b">B</option>'
            . '<option value="d" selected="selected">D</option></select></div></div>';

        $this->assertSame($expected, (string) BF::select('c', null, ['a' => 'A', 'b' => 'B', 'd' => 'D'], ['a', 'd'], ['multiple' => true]));
    }

    public function test_select_placeholder(): void
    {
        $expected = '<div id="c-group" class="form-group"><label for="c">C</label>'
            . '<div><select id="c" class="form-control" name="c">'
            . '<option selected="selected" value="">Pick one</option><option value="a">A</option></select></div></div>';

        $this->assertSame($expected, (string) BF::select('c', null, ['a' => 'A'], null, ['placeholder' => 'Pick one']));
    }
}

/**
 * Minimal Eloquent model fixture for model-binding characterization.
 */
class ValueBindingUser extends Model
{
    protected $guarded = [];
    public $timestamps = false;
}
