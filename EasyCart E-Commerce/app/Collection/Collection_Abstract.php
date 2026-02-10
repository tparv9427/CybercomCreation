<?php

namespace EasyCart\Collection;

use EasyCart\Database\QueryBuilder;

/**
 * Collection_Abstract — Base Collection Class
 * 
 * Handles joins, filters, complex queries, and multi-record retrieval.
 * Each Collection subclass defines its specific query patterns.
 */
abstract class Collection_Abstract
{
    /** @var string Main table name */
    protected $table = '';

    /** @var string Table alias */
    protected $alias = '';

    /** @var string Primary key */
    protected $primaryKey = 'entity_id';

    /** @var array Default columns to select */
    protected $defaultColumns = ['*'];

    /**
     * Create a new query builder for this collection's table
     * 
     * @param array|null $columns Override default columns
     * @return QueryBuilder
     */
    protected function query(?array $columns = null): QueryBuilder
    {
        $cols = $columns ?? $this->defaultColumns;
        $qb = QueryBuilder::select($this->table, $cols);
        if ($this->alias) {
            $qb->alias($this->alias);
        }
        return $qb;
    }

    /**
     * Get all records
     * @return array
     */
    public function getAll(): array
    {
        return $this->query()->fetchAll();
    }

    /**
     * Get all records with pagination
     * 
     * @param int $page 1-indexed page
     * @param int $perPage Items per page
     * @return array
     */
    public function paginate(int $page = 1, int $perPage = 20): array
    {
        return $this->query()
            ->paginate($page, $perPage)
            ->fetchAll();
    }

    /**
     * Count total records
     * @return int
     */
    public function count(): int
    {
        $result = QueryBuilder::select($this->table, ['COUNT(*) as total'])
            ->fetchOne();
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Find records by a field value
     * 
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function findBy(string $field, $value): array
    {
        return $this->query()
            ->where($field, '=', $value)
            ->fetchAll();
    }

    /**
     * Find a single record by a field value
     * 
     * @param string $field
     * @param mixed $value
     * @return array|null
     */
    public function findOneBy(string $field, $value): ?array
    {
        return $this->query()
            ->where($field, '=', $value)
            ->fetchOne();
    }

    /**
     * Process a single result row (override for transformations)
     * 
     * @param array $row
     * @return array
     */
    protected function processRow(array $row): array
    {
        return $row;
    }

    /**
     * Process multiple result rows
     * 
     * @param array $rows
     * @return array
     */
    protected function processRows(array $rows): array
    {
        return array_map([$this, 'processRow'], $rows);
    }
}
