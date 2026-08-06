<?php

namespace Bgaze\BootstrapForm\Tests;

use BF;
use Illuminate\Support\Facades\Blade;

/**
 * The two utility classes that used to be hardcoded in the version drivers are settings
 * of the version config sections: group_class (the form group wrapper) and
 * choices_label_class (the global label of a choice collection, horizontal layout).
 * Both follow the regular cascade — config, then form, then field — and false disables.
 */
class ConfigurableClassesTest extends TestCase
{
    // ## GROUP CLASS ############################################################

    public function test_group_class_defaults_to_the_version_section_value(): void
    {
        $this->assertStringContainsString('<div id="login-group" class="mb-3">', (string) BF::text('login'));

        BF::open(['bootstrap_version' => 4]);
        $html = (string) BF::text('login');
        BF::close();

        $this->assertStringContainsString('<div id="login-group" class="form-group">', $html);
    }

    public function test_group_class_is_overridable_in_the_config(): void
    {
        config()->set('bootstrap_form.bootstrap5.group_class', 'mb-5');

        $html = (string) BF::text('login');

        $this->assertStringContainsString('<div id="login-group" class="mb-5">', $html);
        $this->assertStringNotContainsString('mb-3', $html);
    }

    public function test_group_class_is_overridable_per_form(): void
    {
        BF::open(['group_class' => 'mb-4']);
        $html = (string) BF::text('login');
        BF::close();

        $this->assertStringContainsString('<div id="login-group" class="mb-4">', $html);
        $this->assertStringNotContainsString('mb-3', $html);
    }

    public function test_group_class_is_overridable_per_field(): void
    {
        BF::open(['group_class' => 'mb-4']);
        $inherited = (string) BF::text('login');
        $overridden = (string) BF::text('email', null, null, ['group_class' => 'mb-0 border']);
        BF::close();

        $this->assertStringContainsString('<div id="login-group" class="mb-4">', $inherited);
        $this->assertStringContainsString('<div id="email-group" class="mb-0 border">', $overridden);
    }

    public function test_group_class_false_leaves_the_wrapper_without_a_class(): void
    {
        $html = (string) BF::text('login', null, null, ['group_class' => false]);

        $this->assertStringContainsString('<div id="login-group">', $html);
        $this->assertStringNotContainsString('mb-3', $html);
    }

    public function test_group_class_is_a_setting_never_an_html_attribute(): void
    {
        $html = (string) BF::text('login', null, null, ['group_class' => 'mb-4']);

        $this->assertStringNotContainsString('group_class', $html);
    }

    public function test_group_attributes_add_to_the_group_class_they_never_replace_it(): void
    {
        $html = (string) BF::text('login', null, null, ['group' => ['class' => 'border']]);

        $this->assertStringContainsString('<div class="border mb-3" id="login-group">', $html);
    }

    public function test_group_false_still_drops_the_wrapper_entirely(): void
    {
        $html = (string) BF::text('login', null, null, ['group' => false]);

        $this->assertStringNotContainsString('login-group', $html);
        $this->assertStringNotContainsString('mb-3', $html);
    }

    public function test_group_class_survives_the_horizontal_and_inline_extra_classes(): void
    {
        BF::inline(['url' => '/foo', 'group_class' => 'mb-4']);
        $html = (string) BF::text('login');
        BF::close();

        $this->assertStringContainsString('<div id="login-group" class="mb-4 me-3 my-1">', $html);
    }

    // ## CHOICES LABEL CLASS ####################################################

    public function test_choices_label_class_defaults_to_pt0_in_horizontal_layout(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::checkboxes('tags', null, ['a' => 'A']);
        BF::close();

        $this->assertStringContainsString('<label for="tags" class="pt-0 col-form-label col-lg-2 col-xl-3">', $html);
    }

    public function test_choices_label_class_is_overridable_per_form(): void
    {
        BF::horizontal(['url' => '/foo', 'choices_label_class' => 'pt-1']);
        $html = (string) BF::checkboxes('tags', null, ['a' => 'A']);
        BF::close();

        $this->assertStringContainsString('<label for="tags" class="pt-1 col-form-label col-lg-2 col-xl-3">', $html);
    }

    public function test_choices_label_class_is_overridable_per_field(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::radios('tags', null, ['a' => 'A'], null, ['choices_label_class' => 'pt-2']);
        BF::close();

        $this->assertStringContainsString('<label for="tags" class="pt-2 col-form-label col-lg-2 col-xl-3">', $html);
    }

    public function test_choices_label_class_false_drops_the_alignment_class(): void
    {
        BF::horizontal(['url' => '/foo', 'choices_label_class' => false]);
        $html = (string) BF::checkboxes('tags', null, ['a' => 'A']);
        BF::close();

        $this->assertStringContainsString('<label for="tags" class="col-form-label col-lg-2 col-xl-3">', $html);
        $this->assertStringNotContainsString('pt-0', $html);
    }

    public function test_choices_label_class_only_applies_to_the_horizontal_layout(): void
    {
        $html = (string) BF::checkboxes('tags', null, ['a' => 'A']);

        $this->assertStringContainsString('<label for="tags" class="form-label">', $html);
        $this->assertStringNotContainsString('pt-0', $html);
    }

    public function test_choices_label_class_is_a_setting_never_an_html_attribute(): void
    {
        BF::horizontal(['url' => '/foo']);
        $html = (string) BF::checkboxes('tags', null, ['a' => 'A'], null, ['choices_label_class' => 'pt-2']);
        BF::close();

        $this->assertStringNotContainsString('choices_label_class', $html);
    }

    // ## X-COMPONENT PROJECTION #################################################

    public function test_components_project_the_kebab_case_settings(): void
    {
        $this->assertSame(
            (string) BF::text('login', null, null, ['group_class' => 'mb-4']),
            trim(Blade::render('<x-bf::text name="login" group-class="mb-4"/>'))
        );

        $this->assertSame(
            (string) BF::text('login', null, null, ['group_class' => false]),
            trim(Blade::render('<x-bf::text name="login" :group-class="false"/>'))
        );

        BF::horizontal(['url' => '/foo']);
        $expected = (string) BF::checkboxes('tags', null, ['a' => 'A'], null, ['choices_label_class' => 'pt-2']);
        $rendered = trim(Blade::render('<x-bf::checkboxes name="tags" :choices="[\'a\' => \'A\']" choices-label-class="pt-2"/>'));
        BF::close();

        $this->assertSame($expected, $rendered);
    }
}
