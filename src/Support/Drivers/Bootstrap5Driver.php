<?php

namespace Bgaze\BootstrapForm\Support\Drivers;

use Collective\Html\HtmlBuilder;

/**
 * Bootstrap 5 class vocabulary and structural rendering.
 *
 * Custom controls were merged into the default styles in Bootstrap 5, so the `custom`
 * flag is a no-op here: checkboxes/radios/switches, selects, ranges and file inputs all
 * render with their single unified class set.
 */
class Bootstrap5Driver extends VersionDriver
{

    /**
     * {@inheritdoc}
     */
    public function formGroupClass()
    {
        return 'mb-3';
    }

    /**
     * {@inheritdoc}
     */
    public function labelClass()
    {
        return 'form-label';
    }

    /**
     * {@inheritdoc}
     */
    public function selectClass($custom)
    {
        return 'form-select';
    }

    /**
     * {@inheritdoc}
     */
    public function selectSizeClass($custom, $size)
    {
        return 'form-select-' . $size;
    }

    /**
     * {@inheritdoc}
     */
    public function rangeClass($custom)
    {
        return 'form-range';
    }

    /**
     * {@inheritdoc}
     *
     * Bootstrap 5 dropped the form-horizontal / form-inline element classes: horizontal
     * layout is carried by the grid classes on each group, inline is best-effort.
     */
    public function formLayoutClass($layout)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function checkClasses($tag, $custom, $switch, $inline, $labelless)
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
     * {@inheritdoc}
     *
     * Bootstrap 5 removed the .input-group-prepend / .input-group-append wrappers:
     * addons sit as direct children of the input group.
     */
    public function inputGroup(HtmlBuilder $html, $prepend, $input, $append, $size)
    {
        return $html->tag('div', $prepend . $input . $append, [
            'class' => $this->inputGroupClass($size),
        ])->toHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function usesCustomFile()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function fileInputClass()
    {
        return 'form-control';
    }

    /**
     * {@inheritdoc}
     */
    public function customFileInputClass()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function customFileLabelClass()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function customFileWrapperClass($inline)
    {
        return '';
    }
}
