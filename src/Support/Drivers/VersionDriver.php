<?php

namespace Bgaze\BootstrapForm\Support\Drivers;

use Bgaze\BootstrapForm\Support\Html;

/**
 * Provides the Bootstrap class vocabulary and the structural rendering that differs
 * between Bootstrap versions. Component classes are native and fixed (not configurable):
 * only layout-level options live in the config file.
 *
 * Shared tokens (identical across versions) are implemented here; each concrete driver
 * overrides only the genuine version deltas.
 */
abstract class VersionDriver
{

    ### SHARED TOKENS (identical across Bootstrap versions) #####################

    /**
     * Class for text-like inputs and textareas.
     */
    public function inputClass()
    {
        return 'form-control';
    }

    /**
     * Size modifier class for text-like inputs ("sm" | "lg").
     */
    public function inputSizeClass($size)
    {
        return 'form-control-' . $size;
    }

    /**
     * Grid class for the right (fields) column of horizontal groups when pulling right.
     */
    public function colClass()
    {
        return 'col';
    }

    /**
     * Label class in horizontal layout.
     */
    public function colFormLabelClass()
    {
        return 'col-form-label';
    }

    /**
     * Help text class.
     */
    public function helpClass()
    {
        return 'form-text';
    }

    /**
     * Validation feedback container class.
     *
     * @param  bool  $block  Force display (used with input groups and choice collections).
     */
    public function feedbackClass($block)
    {
        return $block ? 'invalid-feedback d-block' : 'invalid-feedback';
    }

    /**
     * Class flagging an invalid input / group.
     */
    public function invalidClass()
    {
        return 'is-invalid';
    }

    /**
     * Base class prefix for buttons ("btn btn-" + style).
     */
    public function buttonBaseClass()
    {
        return 'btn btn-';
    }

    /**
     * Grid row class for horizontal groups.
     */
    public function rowClass()
    {
        return 'row';
    }

    /**
     * Label class for a choices collection (checkboxes / radios) in horizontal layout.
     */
    public function checkChoiceLabelClass()
    {
        return 'pt-0';
    }

    ### VERSION DELTAS #########################################################

    /**
     * Form group wrapper class.
     */
    abstract public function formGroupClass();

    /**
     * Label class in vertical / inline layout (empty when the version has none).
     */
    abstract public function labelClass();

    /**
     * Select class.
     *
     * @param  bool  $custom
     */
    abstract public function selectClass($custom);

    /**
     * Select size modifier class ("sm" | "lg").
     *
     * @param  bool  $custom
     * @param  string  $size
     */
    abstract public function selectSizeClass($custom, $size);

    /**
     * Range input class.
     *
     * @param  bool  $custom
     */
    abstract public function rangeClass($custom);

    /**
     * Form element class for a given layout (empty when the version has none).
     *
     * @param  string  $layout  vertical | horizontal | inline
     */
    abstract public function formLayoutClass($layout);

    /**
     * Resolve the classes (and extra input attributes) for a checkbox / radio / switch.
     *
     * @param  string  $tag  checkbox | radio
     * @param  bool  $custom
     * @param  bool  $switch
     * @param  bool  $inline
     * @param  bool  $labelless  Whether the control is rendered without a label.
     * @return array{wrapper:string,input:string,label:string,input_attributes:array}
     */
    abstract public function checkClasses($tag, $custom, $switch, $inline, $labelless);

    /**
     * Wrap an input with its prepend / append addons into an input group.
     *
     * @param  Html  $html
     * @param  string  $prepend  Prepend content (empty string when none).
     * @param  string  $input  The input HTML.
     * @param  string  $append  Append content (empty string when none).
     * @param  string|null  $size  "sm" | "lg" | null
     */
    abstract public function inputGroup(Html $html, $prepend, $input, $append, $size);

    /**
     * Whether the version renders custom-styled file inputs (dedicated markup).
     */
    abstract public function usesCustomFile();

    /**
     * Class for a plain (non custom) file input.
     */
    abstract public function fileInputClass();

    /**
     * Class for the input of a custom file control.
     */
    abstract public function customFileInputClass();

    /**
     * Class for the label of a custom file control.
     */
    abstract public function customFileLabelClass();

    /**
     * Class for the wrapper of a custom file control.
     *
     * @param  bool  $inline
     */
    abstract public function customFileWrapperClass($inline);

    ### SHARED HELPERS #########################################################

    /**
     * Compute the input group wrapper class including the optional size modifier.
     *
     * @param  string|null  $size
     */
    protected function inputGroupClass($size)
    {
        $class = 'input-group';

        if ($size === 'sm') {
            $class .= ' input-group-sm';
        } elseif ($size === 'lg') {
            $class .= ' input-group-lg';
        }

        return $class;
    }
}
