<?php

namespace Elegance\IdentityProvider\Infrastructure\Database;

use PDO;
use PDOException;

class Database extends PDO
{
    /**
     * Constructeur de la class "Database"
     * @param string $host
     * @param string $dbname
     * @param string $user
     * @param string $pass
     */
    public function __construct(string $host, string $dbname, string $user, string $pass) {
        try {
            parent::__construct(
                "mysql:host={$host};dbname={$dbname}", 
                $user, 
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }
}