<?php

namespace Elegance\IdentityProvider\Controllers\Auth;

use Elegance\IdentityProvider\Controllers\Controller;
use Elegance\IdentityProvider\Services\UserService;
use Elegance\IdentityProvider\Utils\Lang;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as v;
use Slim\Views\PhpRenderer;

/**
 * Class RegisterController
 *
 * Handles user registration: displaying the registration form
 * and processing user registration requests.
 */
class RegisterController extends Controller
{
    /**
     * User service used to create and manage users
     *
     * @var UserService
     */
    protected UserService $userService;

    /**
     * RegisterController constructor
     *
     * @param PhpRenderer $view Template renderer
     * @param UserService $userService Service for user management
     */
    public function __construct(PhpRenderer $view, UserService $userService)
    {
        parent::__construct($view);
        $this->userService = $userService;
    }

    /**
     * Display the registration form
     *
     * @param Request $request
     * @param Response $response
     * @param array $args Route arguments
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->view->render($response, 'register.php', [
            'withMenu' => false
        ]);
    }

    /**
     * Handle user registration
     *
     * Validates input, creates the user, and returns appropriate response
     * with errors or success message.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args Route arguments
     * @return Response
     */
    public function register(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody() ?? [];

        // Validators
        $emailValidator = v::notEmpty()->email();
        $passwordValidator = v::notEmpty()->length(8, null)
            ->regex('/[A-Z]/')->regex('/[a-z]/')
            ->regex('/[0-9]/')->regex('/[\W]/');

        $errors = [];

        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = Lang::get('validation.EMAIL_REQUIRED');

        } elseif (!$emailValidator->validate($data['email'])) {
            $errors['email'] = Lang::get('validation.EMAIL_INVALID');
        }

        // Validate password
        if (empty($data['password'])) {
            $errors['password'] = Lang::get('validation.PASSWORD_REQUIRED');

        } elseif (!$passwordValidator->validate($data['password'])) {
            $errors['password'] = Lang::get('validation.PASSWORD_TOO_WEAK');
        }

        // Validate password confirmation
        if (!isset($data['password_confirm']) || empty($data['password_confirm'])) {
            $errors['password_confirm'] = Lang::get('validation.PASSWORD_CONFIRM_REQUIRED');

        } elseif (($data['password'] ?? '') !== ($data['password_confirm'] ?? '')) {
            $errors['password_confirm'] = Lang::get('validation.PASSWORD_MISMATCH');
        }

        // Return errors if any
        if (!empty($errors)) {
            return $this->view->render($response, 'register.php', [
                'data' => $data,
                'errors' => $errors
            ]);
        }

        // Attempt to create the user
        $result = $this->userService->create($data['email'], $data['password']);

        if (!$result) {
            // Use the "users" key for existing user
            $errors['email'] = Lang::get('users.USER_ALREADY_EXISTS');
            return $this->view->render($response, 'register.php', [
                'data' => $data,
                'errors' => $errors
            ]);
        }

        // Success response
        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }
}