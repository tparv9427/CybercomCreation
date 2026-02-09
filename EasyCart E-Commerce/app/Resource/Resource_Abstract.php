<?php

namespace EasyCart\Resource;

use EasyCart\Core\Database;

/**
 * Resource_Abstract
 * 
 * Base class for all Resource classes.
 * Resources define database table configuration (table name, primary key, columns).
 */
abstract class Resource_Abstract
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = '';

    /**
     * Primary key column name
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * List of table columns
     * @var array
     */
    protected $columns = [];

    /**
     * PDO connection
     * @var \PDO
     */
    protected $pdo;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Get table name
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Get primary key column name
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * Get all column names
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get PDO connection
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Find a single record by primary key
     * @param int|string $id
     * @return array|null
     */
    public function load($id): ?array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Save (insert or update) a record
     * @param array $data
     * @return int|null Returns inserted/updated ID
     */
    public function save(array $data): ?int
    {
        $id = $data[$this->primaryKey] ?? null;

        if ($id) {
            // Update
            $setClauses = [];
            $params = [':id' => $id];
            foreach ($data as $col => $val) {
                if ($col !== $this->primaryKey && in_array($col, $this->columns)) {
                    $setClauses[] = "{$col} = :{$col}";
                    $params[":{$col}"] = $val;
                }
            }
            if (empty($setClauses)) {
                return $id;
            }
            $sql = "UPDATE {$this->tableName} SET " . implode(', ', $setClauses) . " WHERE {$this->primaryKey} = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $id;
        } else {
            // Insert
            $cols = [];
            $placeholders = [];
            $params = [];
            foreach ($data as $col => $val) {
                if (in_array($col, $this->columns) && $col !== $this->primaryKey) {
                    $cols[] = $col;
                    $placeholders[] = ":{$col}";
                    $params[":{$col}"] = $val;
                }
            }
            $sql = "INSERT INTO {$this->tableName} (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $placeholders) . ") RETURNING {$this->primaryKey}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        }
    }

    /**
     * Delete a record by primary key
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
