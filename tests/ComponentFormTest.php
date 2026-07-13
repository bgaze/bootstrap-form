<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;

/**
 * Characterization of the <x-bf::form> component: it wraps its fields exactly like the
 * open()/close() facade sequence, resolves layout shortcuts, and forwards form options
 * (novalidate, model binding, ...) to BF::open().
 */
class ComponentFormTest extends TestCase
{
    private function render(string $template, array $data = []): string
    {
        return trim(Blade::render($template, $data));
    }

    public function test_form_wraps_fields_like_the_facade_sequence(): void
    {
        $expected = BF::open(['url' => '/foo']).(string) BF::text('login').BF::close();

        $this->assertSame(
            $expected,
            $this->render('<x-bf::form url="/foo"><x-bf::text name="login"/></x-bf::form>')
        );
    }

    public function test_boolean_layout_shortcut_equals_layout_attribute(): void
    {
        $shortcut = $this->render('<x-bf::form horizontal url="/foo"><x-bf::text name="login"/></x-bf::form>');
        $explicit = $this->render('<x-bf::form layout="horizontal" url="/foo"><x-bf::text name="login"/></x-bf::form>');

        $this->assertSame($explicit, $shortcut);
        // And it does not leak the shortcut as an HTML attribute on the <form>.
        $this->assertStringNotContainsString('horizontal=', $shortcut);
    }

    public function test_novalidate_is_forwarded_to_the_form_tag(): void
    {
        $expected = BF::open(['url' => '/foo', 'novalidate' => true]).BF::close();

        $this->assertSame(
            $expected,
            $this->render('<x-bf::form url="/foo" novalidate></x-bf::form>')
        );
    }

    public function test_model_binding_is_forwarded(): void
    {
        $model = new ComponentFormUser(['login' => 'jdoe']);

        $expected = BF::open(['model' => $model, 'url' => '/foo']).(string) BF::text('login').BF::close();

        $this->assertSame(
            $expected,
            $this->render('<x-bf::form :model="$user" url="/foo"><x-bf::text name="login"/></x-bf::form>', ['user' => $model])
        );
    }
}

/**
 * Minimal Eloquent model fixture for the model-binding characterization.
 */
class ComponentFormUser extends Model
{
    protected $guarded = [];

    public $timestamps = false;
}
