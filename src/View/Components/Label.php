<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * The <x-bf::label> standalone component. Delegates to BF::label(). The target is given via
 * name (or the "for" attribute), the text via value or the default slot.
 */
class Label extends BootstrapComponent
{
    public function __construct(
        public ?string $name = null,
        public mixed $value = null,
    ) {}

    protected function renderHtml(array $data): string
    {
        $options = $this->bootstrapOptions();

        // Accept the HTML idiom `for` as an alias of the name/target ("for" is a reserved
        // word, so it cannot be a constructor property).
        $name = $this->name;
        if ($name === null && isset($options['for'])) {
            $name = (string) $options['for'];
            unset($options['for']);
        }

        $value = $this->value ?? $this->slotContent($data);

        return (string) BF::label((string) $name, $value, $options);
    }
}
