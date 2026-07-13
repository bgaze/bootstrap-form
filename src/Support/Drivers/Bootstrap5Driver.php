<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support\Drivers;

use Bgaze\BootstrapForm\Support\Html;

/**
 * Bootstrap 5 class vocabulary and structural rendering.
 *
 * Custom controls were merged into the default styles in Bootstrap 5, so the `custom`
 * flag is a no-op here: checkboxes/radios/switches, selects, ranges and file inputs all
 * render with their single unified class set.
 */
class Bootstrap5Driver extends VersionDriver
{
    public function formGroupClass(): string
    {
        return 'mb-3';
    }

    public function labelClass(): string
    {
        return 'form-label';
    }

    public function selectClass(bool $custom): string
    {
        return 'form-select';
    }

    public function selectSizeClass(bool $custom, string $size): string
    {
        return 'form-select-'.$size;
    }

    public function rangeClass(bool $custom): string
    {
        return 'form-range';
    }

    /**
     * Bootstrap 5 dropped the form-horizontal / form-inline element classes: horizontal
     * layout is carried by the grid classes on each group, inline is best-effort.
     */
    public function formLayoutClass(string $layout): string
    {
        return '';
    }

    public function checkClasses(string $tag, bool $custom, bool $switch, bool $inline, bool $labelless): array
    {
        $input = 'form-check-input';
        if ($labelless) {
            $input .= ' position-static';
        }

        $wrapper = 'form-check';
        if ($switch) {
            $wrapper .= ' form-switch';
        }
        if ($inline) {
            $wrapper .= ' form-check-inline';
        }

        return [
            'wrapper' => $wrapper,
            'input' => $input,
            'label' => 'form-check-label',
            'input_attributes' => $switch ? ['role' => 'switch'] : [],
        ];
    }

    /**
     * Bootstrap 5 removed the .input-group-prepend / .input-group-append wrappers:
     * addons sit as direct children of the input group.
     */
    public function inputGroup(Html $html, string $prepend, string $input, string $append, ?string $size): string
    {
        return $html->tag('div', $prepend.$input.$append, [
            'class' => $this->inputGroupClass($size),
        ])->toHtml();
    }

    public function supportsFloating(): bool
    {
        return true;
    }

    /**
     * Floating labels: the control comes first, the label after, inside a .form-floating
     * wrapper (the label floats over the control when it is empty and unfocused).
     */
    public function floatingGroup(Html $html, string $input, string $label): string
    {
        return $html->tag('div', $input.$label, ['class' => 'form-floating'])->toHtml();
    }

    public function usesCustomFile(): bool
    {
        return false;
    }

    public function fileInputClass(): string
    {
        return 'form-control';
    }

    public function customFileInputClass(): string
    {
        return '';
    }

    public function customFileLabelClass(): string
    {
        return '';
    }

    public function customFileWrapperClass(bool $inline): string
    {
        return '';
    }
}
