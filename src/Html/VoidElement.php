<?php

namespace Bgaze\BootstrapForm\Html;

class VoidElement extends HtmlElement
{
    public function toHtml(): string
    {
        return sprintf('<%s>', trim(sprintf('%s %s', $this->tag, $this->attributes)));
    }

}
