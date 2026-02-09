<?php

namespace EasyCart\Collection;

use EasyCart\Core\Database;
use EasyCart\Database\QueryBuilder;

/**
 * Collection_Abstract
 * 
 * Base class for all Collection classes.
 * Collections handle complex queries, joins, and filtering.
 */
abstract class Collection_Abstract
{
    /**
     * Associated Resource class name
     * @var string
     */
    protected $resourceClass = '';

    /**
     * Model class name for hydration
     * @var string
     */
    protected $modelClass = '';

    /**
     * Resource instance
     * @var \EasyCart\Resource\Resource_Abstract
     */
    protected $resource;

    /**
     * Query builder instance
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * PDO connection
     * @var \PDO
     */
    protected $pdo;

    /**
     * Loaded items
     * @var array
     */
    protected $items = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();

        if ($this->resourceClass) {
            $this->resource = new $this->resourceClass();
        }

        $this->queryBuilder = new QueryBuilder();
        $this->initSelect();
    }

    /**
     * Initialize the SELECT statement - override in child classes
     */
    protected function initSelect(): void
    {
        if ($this->resource) {
            $this->queryBuilder
                ->from($this->resource->getTableName())
                ->select('*');
        }
    }

    /**
     * Add a WHERE filter
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @return $this
     */
    public function addFilter(string $column, $value, string $operator = '='): self
    {
        $this->queryBuilder->where($column, $value, $operator);
        return $this;
    }

    /**
     * Add ORDER BY
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function setOrder(string $column, string $direction = 'ASC'): self
    {
        $this->queryBuilder->orderBy($column, $direction);
        return $this;
    }

    /**
     * Set LIMIT
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): self
    {
        $this->queryBuilder->limit($limit);
        return $this;
    }

    /**
     * Set OFFSET
     * @param int $offset
     * @return $this
     */
    public function setOffset(int $offset): self
    {
        $this->queryBuilder->offset($offset);
        return $this;
    }

    /**
     * Load the collection from database
     * @return $this
     */
    public function load(): self
    {
        $sql = (string) $this->queryBuilder;
        $params = $this->queryBuilder->getParams();

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $this->items = $stmt->fetchAll();
        return $this;
    }

    /**
     * Get all items as arrays
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get count of items
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get first item
     * @return array|null
     */
    public function getFirstItem(): ?array
    {
        return $this->items[0] ?? null;
    }

    /**
     * Reset query builder for new query
     * @return $this
     */
    public function reset(): self
    {
        $this->queryBuilder = new QueryBuilder();
        $this->items = [];
        $this->initSelect();
        return $this;
    }
}
