<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\View\Components;

use Bgaze\BootstrapForm\Support\Facades\BF;

/**
 * The <x-bf::link> component. Delegates to BF::link(). The title is given via the title
 * attribute or as the default slot: <x-bf::link href="/go">Go</x-bf::link>.
 */
class Link extends BootstrapComponent
{
    public function __construct(
        public string $href,
        public ?string $title = null,
    ) {}

    protected function renderHtml(array $data): string
    {
        $title = $this->title ?? $this->slotContent($data);

        return (string) BF::link($this->href, $title, $this->bootstrapOptions());
    }
}
