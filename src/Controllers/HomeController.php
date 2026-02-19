<?php

namespace Elegance\IdentityProvider\Controllers;

use Elegance\IdentityProvider\Controllers\Controller;
use Elegance\IdentityProvider\Utils\Lang;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class LogoutController
 *
 * Handles user logout operations.
 * Inherits from base Controller and uses UserService for user session management.
 */
class HomeController extends Controller
{
    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->view->render($response, 'home.php', [
            'extra_css' => ['home.css']
        ]);
    }
}
