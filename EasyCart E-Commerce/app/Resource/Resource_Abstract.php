<?php

namespace EasyCart\Resource;

use EasyCart\Core\Database;
use EasyCart\Database\QueryBuilder;
use PDO;

/**
 * Resource_Abstract — Base Resource Class
 * 
 * Handles DB config: table name, primary key, column definitions.
 * Provides basic CRUD operations using QueryBuilder.
 * Each entity Resource subclass defines its own table/columns.
 */
abstract class Resource_Abstract
{
    /** @var PDO Database connection */
    protected $pdo;

    /** @var string Table name (set in subclass) */
    protected $table = '';

    /** @var string Primary key column */
    protected $primaryKey = 'entity_id';

    /** @var array Column definitions (set in subclass) */
    protected $columns = [];

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Get table name
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
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
     * Get column list
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Load a single record by primary key
     * 
     * @param mixed $id
     * @return array|null
     */
    public function load($id): ?array
    {
        return QueryBuilder::select($this->table, ['*'])
            ->where($this->primaryKey, '=', $id)
            ->fetchOne();
    }

    /**
     * Save a record (INSERT)
     * 
     * @param array $data Column => value pairs
     * @return string Inserted ID
     */
    public function save(array $data): string
    {
        // Filter to only allowed columns
        $filtered = array_intersect_key($data, array_flip($this->columns));
        return QueryBuilder::insert($this->table, $filtered)
            ->executeInsert($this->table . '_' . $this->primaryKey . '_seq');
    }

    /**
     * Update a record by primary key
     * 
     * @param mixed $id
     * @param array $data Column => value pairs
     * @return int Affected rows
     */
    public function update($id, array $data): int
    {
        $filtered = array_intersect_key($data, array_flip($this->columns));
        return QueryBuilder::update($this->table, $filtered)
            ->where($this->primaryKey, '=', $id)
            ->execute();
    }

    /**
     * Delete a record by primary key
     * 
     * @param mixed $id
     * @return int Affected rows
     */
    public function delete($id): int
    {
        return QueryBuilder::delete($this->table)
            ->where($this->primaryKey, '=', $id)
            ->execute();
    }

    /**
     * Check if a record exists
     * 
     * @param mixed $id
     * @return bool
     */
    public function exists($id): bool
    {
        $result = QueryBuilder::select($this->table, ['COUNT(*) as cnt'])
            ->where($this->primaryKey, '=', $id)
            ->fetchOne();
        return ($result['cnt'] ?? 0) > 0;
    }

    /**
     * Get raw PDO connection for complex operations
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
