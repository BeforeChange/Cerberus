<?php

namespace Elegance\IdentityProvider\Services;

use PDO;
use Psr\Log\LoggerInterface;

/**
 * Class Service
 *
 * Base service class that provides access to the PDO database connection.
 * All other service classes (e.g., UserService) should extend this class.
 */
abstract class Service
{
    /**
     * Service constructor
     *
     * @param PDO $pdo PDO instance to be used for database access
     */
    public function __construct(protected PDO $db, protected LoggerInterface $logger) {}
}
