<?php

namespace Bgaze\BootstrapForm\Support;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\View\Factory;

/**
 * Per-form binding state: the currently bound model, the CSRF token and the framework
 * services (URL generator, view factory, session) needed to render and repopulate a
 * form. Replaces the instance state that used to live on the Collective FormBuilder.
 */
class FormContext
{
    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var Factory
     */
    protected $view;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var string
     */
    protected $csrfToken;

    /**
     * The model currently bound to the form (null when none).
     *
     * @var mixed
     */
    protected $model = null;

    public function __construct(UrlGenerator $url, Factory $view, Session $session, $csrfToken)
    {
        $this->url = $url;
        $this->view = $view;
        $this->session = $session;
        $this->csrfToken = $csrfToken;
    }

    public function url()
    {
        return $this->url;
    }

    public function view()
    {
        return $this->view;
    }

    public function session()
    {
        return $this->session;
    }

    public function csrfToken()
    {
        return $this->csrfToken;
    }

    /**
     * Bind a model to the form.
     *
     * @param  mixed  $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Clear per-form state (called on form close).
     */
    public function reset()
    {
        $this->model = null;
    }
}
