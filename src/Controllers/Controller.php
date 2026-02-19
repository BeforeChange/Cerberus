<?php

namespace Elegance\IdentityProvider\Controllers;

use Slim\Views\PhpRenderer;

/**
 * Class Controller
 *
 * Base controller class for the application.
 * Provides shared services like view rendering and logging for all controllers.
 */
abstract class Controller
{
    /**
     * PhpRenderer instance
     *
     * Used to render templates in the derived controllers.
     *
     * @var PhpRenderer
     */
    protected PhpRenderer $view;

    /**
     * Controller constructor
     *
     * Initializes the base controller with shared services.
     *
     * @param PhpRenderer $view   PhpRenderer instance for template rendering
     */
    public function __construct(PhpRenderer $view)
    {
        $this->view = $view;
    }
}
