<?php

namespace EasyCart\Database;

/**
 * QueryBuilder
 * 
 * Centralized dynamic query builder.
 * Uses __toString() for SQL generation.
 */
class QueryBuilder
{
    /**
     * SELECT columns
     * @var array
     */
    protected $select = ['*'];

    /**
     * FROM table
     * @var string
     */
    protected $from = '';

    /**
     * Table alias
     * @var string
     */
    protected $alias = '';

    /**
     * JOIN clauses
     * @var array
     */
    protected $joins = [];

    /**
     * WHERE conditions
     * @var array
     */
    protected $where = [];

    /**
     * ORDER BY clauses
     * @var array
     */
    protected $orderBy = [];

    /**
     * LIMIT value
     * @var int|null
     */
    protected $limit = null;

    /**
     * OFFSET value
     * @var int|null
     */
    protected $offset = null;

    /**
     * Bound parameters
     * @var array
     */
    protected $params = [];

    /**
     * Parameter counter for unique naming
     * @var int
     */
    protected $paramCounter = 0;

    /**
     * Set SELECT columns
     * @param string|array $columns
     * @return $this
     */
    public function select($columns): self
    {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        $this->select = $columns;
        return $this;
    }

    /**
     * Set FROM table
     * @param string $table
     * @param string $alias
     * @return $this
     */
    public function from(string $table, string $alias = ''): self
    {
        $this->from = $table;
        $this->alias = $alias;
        return $this;
    }

    /**
     * Add a JOIN clause
     * @param string $table
     * @param string $condition
     * @param string $type (INNER, LEFT, RIGHT)
     * @return $this
     */
    public function join(string $table, string $condition, string $type = 'INNER'): self
    {
        $this->joins[] = strtoupper($type) . " JOIN {$table} ON {$condition}";
        return $this;
    }

    /**
     * Add LEFT JOIN
     * @param string $table
     * @param string $condition
     * @return $this
     */
    public function leftJoin(string $table, string $condition): self
    {
        return $this->join($table, $condition, 'LEFT');
    }

    /**
     * Add a WHERE condition
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @return $this
     */
    public function where(string $column, $value, string $operator = '='): self
    {
        $paramName = ':p' . (++$this->paramCounter);
        $this->where[] = "{$column} {$operator} {$paramName}";
        $this->params[$paramName] = $value;
        return $this;
    }

    /**
     * Add a raw WHERE condition
     * @param string $condition
     * @return $this
     */
    public function whereRaw(string $condition): self
    {
        $this->where[] = $condition;
        return $this;
    }

    /**
     * Add ORDER BY
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "{$column} " . strtoupper($direction);
        return $this;
    }

    /**
     * Set LIMIT
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set OFFSET
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Get bound parameters
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Reset the builder
     * @return $this
     */
    public function reset(): self
    {
        $this->select = ['*'];
        $this->from = '';
        $this->alias = '';
        $this->joins = [];
        $this->where = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        $this->params = [];
        $this->paramCounter = 0;
        return $this;
    }

    /**
     * Build and return SQL string
     * @return string
     */
    public function __toString(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->select);

        $sql .= ' FROM ' . $this->from;
        if ($this->alias) {
            $sql .= ' AS ' . $this->alias;
        }

        foreach ($this->joins as $join) {
            $sql .= ' ' . $join;
        }

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }

        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }
}
