<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;

/**
 * Laravel's mergeConfigFrom() merges only the top level of a config file, so an
 * application that published config/bootstrap_form.php replaces a whole "bootstrapN"
 * section. The package defaults must therefore act as a floor under each section:
 * a config published before a key existed keeps rendering with that key's default.
 */
class VersionSectionMergeTest extends TestCase
{
    protected function defineEnvironment($app)
    {
        // A config file published before the version sections carried every key: only
        // one option survives, all the others are missing.
        $app['config']->set('bootstrap_form.bootstrap5', ['left_class' => 'col-4']);
        $app['config']->set('bootstrap_form.bootstrap4', ['left_class' => 'col-4']);
    }

    public function test_missing_keys_fall_back_to_the_package_defaults(): void
    {
        BF::inline(['url' => '/foo']);
        $html = (string) BF::text('login');
        BF::close();

        // hspace (me-3), vspace (my-1) and lspace (me-2) are absent from the published
        // section, yet still applied.
        $expected = '<div id="login-group" class="mb-3 me-3 my-1">'
            .'<label for="login" class="form-label me-2">Login</label>'
            .'<div><input id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, $html);
    }

    public function test_published_value_still_wins_over_the_package_default(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::text('login');
        BF::close();

        $this->assertStringContainsString('class="col-form-label col-4"', $html);
        $this->assertStringNotContainsString('col-lg-2 col-xl-3', $html);
    }

    public function test_the_floor_applies_to_the_bootstrap4_section_too(): void
    {
        BF::inline(['url' => '/foo', 'bootstrap_version' => 4]);
        $html = (string) BF::text('login');
        BF::close();

        $expected = '<div id="login-group" class="form-group mr-3 my-1">'
            .'<label for="login" class="mr-2">Login</label>'
            .'<div><input id="login" class="form-control" name="login" type="text"></div></div>';

        $this->assertSame($expected, $html);
    }
}
