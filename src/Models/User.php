<?php

namespace Elegance\IdentityProvider\Models;

use DateTime;
use Elegance\IdentityProvider\Utils\Uuid;
use Psr\Log\LoggerInterface;

/**
 * Class User
 *
 * Represents a user in the system.
 * Extends the base Model class and handles:
 *   - UUID generation (guaranteed unique)
 *   - Email and password storage
 *   - Automatic creation timestamp
 */
class User extends Model
{
    /**
     * Universally unique identifier for the user
     *
     * @var string|null
     */
    public ?string $uuid;

    /**
     * User email
     *
     * @var string|null
     */
    public ?string $email;

    /**
     * User hashed password
     *
     * @var string|null
     */
    public ?string $password;

    /**
     * Timestamp when the user was created
     *
     * @var DateTime|null
     */
    public ?DateTime $created_at;

    /**
     * Property casting rules
     *
     * @var array
     */
    public array $casts = [
        'created_at' => 'datetime'
    ];

    /**
     * User constructor
     *
     * Generates a unique UUID when creating a new user instance.
     *
     * @param \PDO $db PDO instance for database operations
     */
    public function __construct(\PDO $db, LoggerInterface $logger)
    {
        parent::__construct($db, $logger);
        $this->uuid = $this->generateUniqueUuid();
    }

    /**
     * Generate a unique UUID
     *
     * Loops until a UUID is found that does not exist in the users table.
     *
     * @return string Unique UUID
     */
    private function generateUniqueUuid(): string
    {
        do {
            $uuid = Uuid::generate();

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE uuid = :uuid");
            $stmt->execute(['uuid' => $uuid]);
            $count = (int)$stmt->fetchColumn();

        } while ($count > 0);

        return $uuid;
    }

    /**
     * Insert a new user record
     *
     * Sets the creation timestamp before inserting into the database.
     *
     * @return bool True on success, false on failure
     */
    public function insert(): bool
    {
        $this->created_at = new DateTime();
        return parent::insert();
    }
}
