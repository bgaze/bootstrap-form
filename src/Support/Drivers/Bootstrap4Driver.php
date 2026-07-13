<?php

namespace Bgaze\BootstrapForm\Support\Drivers;

use Bgaze\BootstrapForm\Support\Html;

/**
 * Bootstrap 4 class vocabulary and structural rendering.
 * Reproduces the historical output of the package (the non-regression baseline).
 */
class Bootstrap4Driver extends VersionDriver
{

    /**
     * {@inheritdoc}
     */
    public function formGroupClass()
    {
        return 'form-group';
    }

    /**
     * {@inheritdoc}
     */
    public function labelClass()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function selectClass($custom)
    {
        return $custom ? 'custom-select' : 'form-control';
    }

    /**
     * {@inheritdoc}
     */
    public function selectSizeClass($custom, $size)
    {
        return ($custom ? 'custom-select' : 'form-control') . '-' . $size;
    }

    /**
     * {@inheritdoc}
     */
    public function rangeClass($custom)
    {
        return $custom ? 'custom-range' : 'form-control-range';
    }

    /**
     * {@inheritdoc}
     */
    public function formLayoutClass($layout)
    {
        return $layout === 'vertical' ? '' : 'form-' . $layout;
    }

    /**
     * {@inheritdoc}
     */
    public function checkClasses($tag, $custom, $switch, $inline, $labelless)
    {
        $input = $custom ? 'custom-control-input' : 'form-check-input';
        if ($labelless && !$custom) {
            $input .= ' position-static';
        }

        $wrapper = $custom ? 'custom-control' : 'form-check';
        if ($inline) {
            $wrapper .= $custom ? ' custom-control-inline' : ' form-check-inline';
        }
        if ($switch) {
            $wrapper .= ' custom-switch';
        } elseif ($custom) {
            $wrapper .= " custom-{$tag}";
        }

        return [
            'wrapper' => $wrapper,
            'input' => $input,
            'label' => $custom ? 'custom-control-label' : 'form-check-label',
            'input_attributes' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function inputGroup(Html $html, $prepend, $input, $append, $size)
    {
        $prependHtml = ($prepend !== '')
            ? $html->tag('div', $prepend, ['class' => 'input-group-prepend'])->toHtml()
            : '';

        $appendHtml = ($append !== '')
            ? $html->tag('div', $append, ['class' => 'input-group-append'])->toHtml()
            : '';

        return $html->tag('div', $prependHtml . $input . $appendHtml, [
            'class' => $this->inputGroupClass($size),
        ])->toHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function usesCustomFile()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fileInputClass()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function customFileInputClass()
    {
        return 'custom-file-input';
    }

    /**
     * {@inheritdoc}
     */
    public function customFileLabelClass()
    {
        return 'custom-file-label';
    }

    /**
     * {@inheritdoc}
     */
    public function customFileWrapperClass($inline)
    {
        return $inline ? 'custom-file w-auto' : 'custom-file';
    }
}
