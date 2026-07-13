<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support\Drivers;

use Bgaze\BootstrapForm\Support\Html;

/**
 * Bootstrap 4 class vocabulary and structural rendering.
 * Reproduces the historical output of the package (the non-regression baseline).
 */
class Bootstrap4Driver extends VersionDriver
{
    public function formGroupClass(): string
    {
        return 'form-group';
    }

    public function labelClass(): string
    {
        return '';
    }

    public function selectClass(bool $custom): string
    {
        return $custom ? 'custom-select' : 'form-control';
    }

    public function selectSizeClass(bool $custom, string $size): string
    {
        return ($custom ? 'custom-select' : 'form-control') . '-' . $size;
    }

    public function rangeClass(bool $custom): string
    {
        return $custom ? 'custom-range' : 'form-control-range';
    }

    public function formLayoutClass(string $layout): string
    {
        return $layout === 'vertical' ? '' : 'form-' . $layout;
    }

    public function checkClasses(string $tag, bool $custom, bool $switch, bool $inline, bool $labelless): array
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

    public function inputGroup(Html $html, string $prepend, string $input, string $append, ?string $size): string
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

    public function usesCustomFile(): bool
    {
        return true;
    }

    public function fileInputClass(): string
    {
        return '';
    }

    public function customFileInputClass(): string
    {
        return 'custom-file-input';
    }

    public function customFileLabelClass(): string
    {
        return 'custom-file-label';
    }

    public function customFileWrapperClass(bool $inline): string
    {
        return $inline ? 'custom-file w-auto' : 'custom-file';
    }
}
