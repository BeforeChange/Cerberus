<?php

namespace Elegance\IdentityProvider\Controllers\Auth;

use Elegance\IdentityProvider\Controllers\Controller;
use Elegance\IdentityProvider\Services\UserService;
use Elegance\IdentityProvider\Utils\Lang;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

/**
 * Class LoginController
 *
 * Handles displaying the login form and processing login requests.
 */
class LoginController extends Controller
{
    /**
     * User service used for authentication
     *
     * @var UserService
     */
    protected UserService $userService;

    /**
     * LoginController constructor
     *
     * @param PhpRenderer $view Template renderer
     * @param UserService $userService Service handling user authentication
     */
    public function __construct(PhpRenderer $view, UserService $userService)
    {
        parent::__construct($view);
        $this->userService = $userService;
    }

    /**
     * Display the login form
     *
     * @param Request $request PSR-7 request object
     * @param Response $response PSR-7 response object
     * @param array $args Route parameters
     * @return Response Rendered login page
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->view->render($response, 'login.php', [
            'withMenu' => false
        ]);
    }

    /**
     * Handle user login
     *
     * Validates input, authenticates the user, and returns appropriate response
     * with errors or redirects on success.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function login(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody() ?? [];
        $errors = [];

        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = Lang::get('validation.EMAIL_REQUIRED');
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = Lang::get('validation.EMAIL_INVALID');
        }

        // Validate password
        if (empty($data['password'])) {
            $errors['password'] = Lang::get('validation.PASSWORD_REQUIRED');
        }

        // Return errors if any
        if (!empty($errors)) {
            return $this->view->render($response, 'login.php', [
                'data' => $data,
                'errors' => $errors
            ]);
        }

        // Authenticate user
        $user = $this->userService->authenticate($data['email'], $data['password']);

        if (!$user) {
            // Invalid credentials
            $errors['general'] = Lang::get('auth.INVALID_CREDENTIALS');
            return $this->view->render($response, 'login.php', [
                'data' => $data,
                'errors' => $errors
            ]);
        }

        // Successful login: set session
        $this->userService->loginUser($user);

        // Redirect to home
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }
}
