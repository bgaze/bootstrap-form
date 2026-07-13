<?php

declare(strict_types=1);

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

    public function inputClass(): string
    {
        return 'form-control';
    }

    public function inputSizeClass(string $size): string
    {
        return 'form-control-' . $size;
    }

    public function colClass(): string
    {
        return 'col';
    }

    public function colFormLabelClass(): string
    {
        return 'col-form-label';
    }

    public function helpClass(): string
    {
        return 'form-text';
    }

    /**
     * @param  bool  $block  Force display (used with input groups and choice collections).
     */
    public function feedbackClass(bool $block): string
    {
        return $block ? 'invalid-feedback d-block' : 'invalid-feedback';
    }

    public function invalidClass(): string
    {
        return 'is-invalid';
    }

    public function validClass(): string
    {
        return 'is-valid';
    }

    /**
     * @param  bool  $block  Force display (used with input groups and choice collections).
     */
    public function validFeedbackClass(bool $block): string
    {
        return $block ? 'valid-feedback d-block' : 'valid-feedback';
    }

    public function buttonBaseClass(): string
    {
        return 'btn btn-';
    }

    public function rowClass(): string
    {
        return 'row';
    }

    public function checkChoiceLabelClass(): string
    {
        return 'pt-0';
    }

    ### VERSION DELTAS #########################################################

    abstract public function formGroupClass(): string;

    abstract public function labelClass(): string;

    abstract public function selectClass(bool $custom): string;

    abstract public function selectSizeClass(bool $custom, string $size): string;

    abstract public function rangeClass(bool $custom): string;

    /**
     * @param  string  $layout  vertical | horizontal | inline
     */
    abstract public function formLayoutClass(string $layout): string;

    /**
     * Resolve the classes (and extra input attributes) for a checkbox / radio / switch.
     *
     * @param  bool  $labelless  Whether the control is rendered without a label.
     * @return array{wrapper: string, input: string, label: string, input_attributes: array}
     */
    abstract public function checkClasses(string $tag, bool $custom, bool $switch, bool $inline, bool $labelless): array;

    /**
     * Wrap an input with its prepend / append addons into an input group.
     */
    abstract public function inputGroup(Html $html, string $prepend, string $input, string $append, ?string $size): string;

    /**
     * Whether the version renders custom-styled file inputs (dedicated markup).
     */
    abstract public function usesCustomFile(): bool;

    abstract public function fileInputClass(): string;

    abstract public function customFileInputClass(): string;

    abstract public function customFileLabelClass(): string;

    abstract public function customFileWrapperClass(bool $inline): string;

    /**
     * Whether this version supports floating labels (the .form-floating component).
     * When false, the "floating" layout degrades to vertical rendering.
     */
    public function supportsFloating(): bool
    {
        return false;
    }

    /**
     * Wrap a floatable control and its label into the version's floating-label markup.
     * The base (no floating support) is never reached via the floating layout; it just
     * returns the control followed by its label defensively.
     */
    public function floatingGroup(Html $html, string $input, string $label): string
    {
        return $input . $label;
    }

    ### SHARED HELPERS #########################################################

    protected function inputGroupClass(?string $size): string
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
