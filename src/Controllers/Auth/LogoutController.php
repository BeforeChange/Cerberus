<?php

namespace Elegance\IdentityProvider\Controllers\Auth;

use Elegance\IdentityProvider\Controllers\Controller;
use Elegance\IdentityProvider\Services\UserService;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class LogoutController
 *
 * Handles user logout operations.
 * Inherits from base Controller and uses UserService for user session management.
 */
class LogoutController extends Controller
{
    /**
     * User service used for logout operations
     *
     * @var UserService
     */
    protected UserService $userService;

    /**
     * LogoutController constructor
     *
     * @param PhpRenderer $view Template renderer for responses
     * @param UserService $userService Service handling user sessions
     */
    public function __construct(PhpRenderer $view, UserService $userService)
    {
        parent::__construct($view);
        $this->userService = $userService;
    }

    /**
     * Log the user out
     *
     * Destroys the user session and optionally redirects or renders a view.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args Route arguments
     * @return Response
     */
    public function logout(Request $request, Response $response, array $args): Response
    {
        // Destroy user session (or token) using UserService
        $this->userService->logout();

        // Redirect to homepage or login page
        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }
}
