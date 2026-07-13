<?php

declare(strict_types=1);

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\View\Factory;

/**
 * Per-form binding state: the bound model, the CSRF token and the framework services
 * (URL generator, view factory, session) needed to render and repopulate a form.
 */
class FormContext
{
    protected mixed $model = null;

    public function __construct(
        protected readonly UrlGenerator $url,
        protected readonly Factory $view,
        protected readonly Session $session,
        protected readonly ?string $csrfToken,
    ) {
    }

    public function url(): UrlGenerator
    {
        return $this->url;
    }

    public function view(): Factory
    {
        return $this->view;
    }

    public function session(): Session
    {
        return $this->session;
    }

    public function csrfToken(): ?string
    {
        return $this->csrfToken;
    }

    public function setModel(mixed $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): mixed
    {
        return $this->model;
    }

    /**
     * Clear per-form state (called on form close).
     */
    public function reset(): void
    {
        $this->model = null;
    }
}
