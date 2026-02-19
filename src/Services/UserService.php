<?php

namespace Elegance\IdentityProvider\Services;

use Elegance\IdentityProvider\Models\User;
use PDO;
use Psr\Log\LoggerInterface;

/**
 * Class UserService
 *
 * Handles user-related operations such as registration, authentication,
 * and session management.
 */
class UserService extends Service
{
    /**
     * User model instance
     *
     * @var User
     */
    protected User $user;

    /**
     * UserService constructor
     *
     * @param PDO $pdo PDO instance for database operations
     * @param LoggerInterface $logger PSR-3 logger instance
     * @param User $user User model instance
     */
    public function __construct(PDO $pdo, LoggerInterface $logger, User $user)
    {
        parent::__construct($pdo, $logger);
        $this->user = $user;
        $this->logger->info("UserService initialized");
    }

    /**
     * Check if a user exists by email
     *
     * @param string $email Email to check
     * @return bool True if user exists, false otherwise
     */
    public function existsByEmail(string $email): bool
    {
        $this->logger->debug("Checking if user exists by email", ['email' => $email]);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $exists = (int)$stmt->fetchColumn() > 0;

        $this->logger->info("User exists check", ['email' => $email, 'exists' => $exists]);
        return $exists;
    }

    /**
     * Create a new user
     *
     * Checks if the email is already in use before creating.
     *
     * @param string $email User email
     * @param string $password Plain-text password
     * @return bool True on success, false on failure
     */
    public function create(string $email, string $password): bool
    {
        $this->logger->info("Attempting to create user", ['email' => $email]);

        // Check if email already exists
        if ($this->existsByEmail($email)) {
            $this->logger->warning("Cannot create user, email already in use", ['email' => $email]);
            return false;
        }

        $user = new User($this->db, $this->logger);
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);

        $success = $user->save();
        if ($success) {
            $this->logger->info("User successfully created", ['email' => $email, 'user_id' => $user->id]);
        } else {
            $this->logger->error("Failed to create user", ['email' => $email]);
        }

        return $success;
    }

    /**
     * Log out the current user
     *
     * Clears the user session.
     */
    public function logout(): void
    {
        $this->logger->info("Logging out user", ['user_id' => $_SESSION['user_id'] ?? null]);
        unset($_SESSION['user_id']);
    }

    /**
     * Authenticate a user by email and password
     *
     * @param string $email
     * @param string $password
     * @return User|null Returns the authenticated User instance or null if invalid
     */
    public function authenticate(string $email, string $password): ?User
    {
        $this->logger->info("Authenticating user", ['email' => $email]);

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();

        if (!$data) {
            $this->logger->warning("Authentication failed: user not found", ['email' => $email]);
            return null;
        }

        $user = (new User($this->db, $this->logger))->fill($data);

        if (password_verify($password, $user->password ?? '')) {
            $this->logger->info("Authentication successful", ['email' => $email, 'user_id' => $user->id]);
            return $user;
        }

        $this->logger->warning("Authentication failed: incorrect password", ['email' => $email, 'user_id' => $user->id]);
        return null;
    }

    /**
     * Log in a user
     *
     * Sets the user ID in the session.
     *
     * @param User $user Authenticated user
     */
    public function loginUser(User $user): void
    {
        $_SESSION['user_id'] = $user->id;
        $this->logger->info("User logged in", ['user_id' => $user->id, 'email' => $user->email]);
    }
}
