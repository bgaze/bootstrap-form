<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Database\Eloquent\Model;

/**
 * Form-only options: the ones consumed by the form tag itself and never inherited by its fields.
 */
class FormOptionsTest extends TestCase
{
    protected function defineRoutes($router)
    {
        $router->post('/users', fn () => '')->name('users.store');
        $router->put('/users/{user}', fn () => '')->name('users.update');
    }

    public function test_files_adds_the_multipart_encoding(): void
    {
        $html = (string) BF::open(['url' => '/import', 'files' => true]);
        BF::close();

        $this->assertStringContainsString('enctype="multipart/form-data"', $html);
        $this->assertStringNotContainsString('files=', $html);
    }

    public function test_update_on_an_existing_model_spoofs_the_put_method(): void
    {
        $user = new FormOptionsUser(['login' => 'jdoe']);
        $user->exists = true;
        $user->id = 7;

        $html = (string) BF::open(['model' => $user, 'update' => 'users.update']);
        BF::close();

        $this->assertStringContainsString('method="POST"', $html);
        $this->assertStringContainsString('action="http://localhost/users/7"', $html);
        $this->assertStringContainsString('value="PUT"', $html);
    }

    public function test_store_on_a_new_model_keeps_a_post(): void
    {
        $html = (string) BF::open(['model' => new FormOptionsUser(['login' => 'jdoe']), 'store' => 'users.store']);
        BF::close();

        $this->assertStringContainsString('method="POST"', $html);
        $this->assertStringContainsString('action="http://localhost/users"', $html);
        $this->assertStringNotContainsString('_method', $html);
    }
}

class FormOptionsUser extends Model
{
    protected $guarded = [];

    public $timestamps = false;
}
