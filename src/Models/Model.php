<?php

namespace Elegance\IdentityProvider\Models;

use PDO;
use PDOException;
use Psr\Log\LoggerInterface;

/**
 * Class Model
 *
 * Base model class providing simple CRUD operations.
 * Handles table name derivation, property casting, and basic persistence.
 */
abstract class Model
{
    /**
     * Model ID (primary key)
     *
     * @var int|null
     */
    public ?int $id = null;

    /**
     * Property casting rules
     *
     * Key = property name, Value = cast type (e.g., 'datetime')
     *
     * @var array
     */
    public array $casts = [];

    /**
     * Constructor
     *
     * @param PDO $db PDO instance for database operations
     */
    public function __construct(protected PDO $db, protected LoggerInterface $logger) {}

    /**
     * Get the table name for this model
     *
     * Converts class name to lowercase plural form
     *
     * @return string Table name
     */
    protected function table(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        return strtolower($className) . 's';
    }

    /**
     * Get all records for this model
     *
     * @return array Array of model instances
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM " . $this->table());
        $results = [];

        foreach ($stmt->fetchAll() as $row) {
            $results[] = (new static($this->db, $this->logger))->fill($row);
        }

        return $results;
    }

    /**
     * Fill model properties from array
     *
     * Handles casting (e.g., DateTime)
     *
     * @param array $data Key-value pairs of property values
     * @return static Filled model instance
     */
    public function fill(array $data): static
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if (isset($this->casts[$key])) {
                    switch ($this->casts[$key]) {
                        case 'datetime':
                            $value = new \DateTime($value);
                            break;
                    }
                }
                $this->$key = $value;
            }
        }

        return $this;
    }

    /**
     * Save the model
     *
     * Calls insert() or update() depending on whether the model has an ID
     *
     * @return bool True on success, false on failure
     */
    public function save(): bool
    {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    /**
     * Update an existing record
     *
     * @return bool True on success, false on failure
     * @throws \Exception If model has no ID
     */
    protected function update(): bool
    {
        if (!$this->id) {
            throw new \Exception("Cannot update a model without an ID.");
        }

        $table = $this->table();
        $props = $this->getPersistableProps();

        if (empty($props)) return false;

        foreach ($props as $k => $v) {
            if ($v instanceof \DateTime) $props[$k] = $v->format('Y-m-d H:i:s');
        }

        $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($props)));

        $result = $this->executeQuery("UPDATE {$table} SET $set WHERE id = :id", array_merge($props, ['id' => $this->id]));

        return $result;
    }

    /**
     * Insert a new record
     *
     * @return bool True on success, false on failure
     * @throws \Exception If no properties to insert
     */
    protected function insert(): bool
    {
        $table = $this->table();
        $props = $this->getPersistableProps();

        if (empty($props)) {
            throw new \Exception("No properties to insert for {$table}");
        }

        foreach ($props as $k => $v) {
            if ($v instanceof \DateTime) $props[$k] = $v->format('Y-m-d H:i:s');
        }

        $columns = implode(', ', array_keys($props));
        $placeholders = implode(', ', array_map(fn($k) => ":$k", array_keys($props)));

        $result = $this->executeQuery("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})", $props);

        if (!$result) return false;

        $this->id = (int)$this->db->lastInsertId();
        return true;
    }

    /**
     * Delete the current record
     *
     * @throws \Exception If model has no ID
     */
    public function delete(): void
    {
        if (!$this->id) {
            throw new \Exception("Cannot delete a model without an ID.");
        }

        $table = $this->table();
        $stmt = $this->executeQuery("DELETE FROM {$table} WHERE id = :id", ['id' => $this->id]);

        $this->id = null;
    }

    /**
     * Find a record by ID
     *
     * @param int $id Record ID
     * @return static|null Found model or null if not found
     */
    public function find(int $id): ?static
    {
        $stmt = $this->executeQuery("SELECT * FROM " . $this->table() . " WHERE id = :id LIMIT 1", ['id' => $id]);
        $data = $stmt->fetch();

        return $data ? (new static($this->db, $this->logger))->fill($data) : null;
    }

    /**
     * Executes a SQL query with optional parameters and logs the execution.
     *
     * This method prepares and executes a SQL statement using PDO, and automatically logs:
     *   - The SQL query being executed
     *   - The bound parameters
     *   - Any errors that occur during execution
     *
     * @param string $sql The SQL query to execute
     * @param array $params Optional array of parameters to bind to the query
     * @return bool|\PDOStatement Returns the PDOStatement on success, or false on failure
     * @throws \PDOException Rethrows any PDO exceptions encountered during execution
     */
    protected function executeQuery(string $sql, array $params = []): bool|\PDOStatement
    {
        try {
            // Log the SQL + parameters
            $this->logger?->debug('[SQL]', [
                'query' => $sql,
                'params' => $params
            ]);

            // Prepare & execute
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt;
        } catch (PDOException $e) {
            // Log the error details
            $this->logger?->error('[SQL ERROR]', [
                'query' => $sql,
                'params' => $params,
                'message' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Starts a new database transaction.
     *
     * Transactions allow multiple queries to be executed atomically.
     */
    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    /**
     * Rolls back the current database transaction.
     *
     * Cancels all queries executed within the current transaction.
     */
    public function rollBack(): void
    {
        $this->db->rollBack();
    }

    /**
     * Commits the current database transaction.
     *
     * Makes all queries executed within the current transaction permanent.
     */
    public function commit(): void
    {
        $this->db->commit();
    }

    /**
     * Get all model properties that can be persisted to the database.
     *
     * Excludes internal properties like PDO, logger, casts, and ID.
     * Filters out any non-scalar values except for DateTime objects.
     *
     * @return array Associative array of property names and values ready for SQL operations
     */
    protected function getPersistableProps(): array
    {
        // Get all object properties
        $props = get_object_vars($this);

        // Properties that should never be persisted
        $exclude = ['db', 'casts', 'id', 'logger'];

        // Remove excluded properties
        foreach ($exclude as $field) {
            unset($props[$field]);
        }

        // Keep only scalar values (string, int, float, null) and DateTime objects
        return array_filter($props, fn($v) => is_scalar($v) || $v instanceof \DateTime);
    }
}
