<?php

namespace EasyCart\Database;

use EasyCart\Core\Database;
use PDO;

/**
 * QueryBuilder — Dynamic SQL Builder
 * 
 * Centralized query system for SELECT / INSERT / UPDATE / DELETE.
 * Uses fluent interface and __toString() for dynamic query building.
 * Table and column names are variables for maximum flexibility.
 * 
 * Usage:
 *   $qb = QueryBuilder::select('catalog_product_entity', ['entity_id', 'name', 'price'])
 *       ->where('entity_id', '=', 5)
 *       ->orderBy('name', 'ASC')
 *       ->limit(10);
 *   echo $qb;  // outputs complete SQL via __toString()
 *   $results = $qb->execute();
 */
class QueryBuilder
{
    /** @var string SQL operation: SELECT, INSERT, UPDATE, DELETE */
    private $operation;

    /** @var string Target table name */
    private $table;

    /** @var string|null Table alias */
    private $alias;

    /** @var array Columns for SELECT or INSERT */
    private $columns = [];

    /** @var array WHERE conditions as [column, operator, placeholder] */
    private $conditions = [];

    /** @var array Bound parameter values */
    private $bindings = [];

    /** @var int Auto-incrementing binding counter */
    private $bindingCounter = 0;

    /** @var array JOIN clauses */
    private $joins = [];

    /** @var array ORDER BY clauses */
    private $orderByClauses = [];

    /** @var array GROUP BY columns */
    private $groupByColumns = [];

    /** @var string|null HAVING clause */
    private $havingClause = null;

    /** @var int|null LIMIT value */
    private $limitValue = null;

    /** @var int|null OFFSET value */
    private $offsetValue = null;

    /** @var array Data for INSERT/UPDATE as [column => value] */
    private $data = [];

    /** @var string|null Raw SQL override for complex subqueries */
    private $rawSql = null;

    /** @var array Raw SQL bindings */
    private $rawBindings = [];

    /** @var bool Whether DISTINCT is applied */
    private $distinct = false;

    // =========================================================================
    // Factory Methods (Static Constructors)
    // =========================================================================

    /**
     * Start a SELECT query
     * 
     * @param string $table Table name
     * @param array $columns Column list (default: ['*'])
     * @return self
     */
    public static function select(string $table, array $columns = ['*']): self
    {
        $qb = new self();
        $qb->operation = 'SELECT';
        $qb->table = $table;
        $qb->columns = $columns;
        return $qb;
    }

    /**
     * Start an INSERT query
     * 
     * @param string $table Table name
     * @param array $data Associative array [column => value]
     * @return self
     */
    public static function insert(string $table, array $data): self
    {
        $qb = new self();
        $qb->operation = 'INSERT';
        $qb->table = $table;
        $qb->data = $data;
        return $qb;
    }

    /**
     * Start an UPDATE query
     * 
     * @param string $table Table name
     * @param array $data Associative array [column => value]
     * @return self
     */
    public static function update(string $table, array $data): self
    {
        $qb = new self();
        $qb->operation = 'UPDATE';
        $qb->table = $table;
        $qb->data = $data;
        return $qb;
    }

    /**
     * Start a DELETE query
     * 
     * @param string $table Table name
     * @return self
     */
    public static function delete(string $table): self
    {
        $qb = new self();
        $qb->operation = 'DELETE';
        $qb->table = $table;
        return $qb;
    }

    /**
     * Create from raw SQL (for complex queries that can't be built dynamically)
     * 
     * @param string $sql Raw SQL query
     * @param array $bindings Named parameter bindings
     * @return self
     */
    public static function raw(string $sql, array $bindings = []): self
    {
        $qb = new self();
        $qb->rawSql = $sql;
        $qb->rawBindings = $bindings;
        return $qb;
    }

    // =========================================================================
    // Fluent Interface — Chainable Methods
    // =========================================================================

    /**
     * Set table alias
     * @param string $alias
     * @return self
     */
    public function alias(string $alias): self
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * Apply DISTINCT to SELECT
     * @return self
     */
    public function distinct(): self
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * Add a WHERE condition
     * 
     * @param string $column Column name
     * @param string $operator Comparison operator (=, !=, <, >, <=, >=, LIKE, IN, IS NULL, IS NOT NULL)
     * @param mixed $value Value to bind (null for IS NULL / IS NOT NULL)
     * @return self
     */
    public function where(string $column, string $operator = '=', $value = null): self
    {
        $operator = strtoupper(trim($operator));

        if ($operator === 'IS NULL' || $operator === 'IS NOT NULL') {
            $this->conditions[] = ['type' => 'AND', 'clause' => "{$column} {$operator}"];
        } elseif ($operator === 'IN') {
            $placeholders = [];
            foreach ((array) $value as $val) {
                $placeholder = ':qb_' . $this->bindingCounter++;
                $placeholders[] = $placeholder;
                $this->bindings[$placeholder] = $this->formatValue($val);
            }
            $this->conditions[] = ['type' => 'AND', 'clause' => "{$column} IN (" . implode(', ', $placeholders) . ")"];
        } else {
            $placeholder = ':qb_' . $this->bindingCounter++;
            $this->bindings[$placeholder] = $this->formatValue($value);
            $this->conditions[] = ['type' => 'AND', 'clause' => "{$column} {$operator} {$placeholder}"];
        }

        return $this;
    }

    /**
     * Add an OR WHERE condition
     * 
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return self
     */
    public function orWhere(string $column, string $operator = '=', $value = null): self
    {
        $operator = strtoupper(trim($operator));

        if ($operator === 'IS NULL' || $operator === 'IS NOT NULL') {
            $this->conditions[] = ['type' => 'OR', 'clause' => "{$column} {$operator}"];
        } else {
            $placeholder = ':qb_' . $this->bindingCounter++;
            $this->bindings[$placeholder] = $this->formatValue($value);
            $this->conditions[] = ['type' => 'OR', 'clause' => "{$column} {$operator} {$placeholder}"];
        }

        return $this;
    }

    /**
     * Add a raw WHERE clause
     * 
     * @param string $clause Raw SQL condition
     * @param array $bindings Named bindings for the clause
     * @return self
     */
    public function whereRaw(string $clause, array $bindings = []): self
    {
        $this->conditions[] = ['type' => 'AND', 'clause' => $clause];
        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }

    /**
     * Add a JOIN clause
     * 
     * @param string $table Table to join
     * @param string $on ON condition
     * @param string $type JOIN type (INNER, LEFT, RIGHT)
     * @return self
     */
    public function join(string $table, string $on, string $type = 'INNER'): self
    {
        $this->joins[] = strtoupper($type) . " JOIN {$table} ON {$on}";
        return $this;
    }

    /**
     * Add a LEFT JOIN
     * @param string $table
     * @param string $on
     * @return self
     */
    public function leftJoin(string $table, string $on): self
    {
        return $this->join($table, $on, 'LEFT');
    }

    /**
     * Add ORDER BY clause
     * 
     * @param string $column Column name
     * @param string $direction ASC or DESC
     * @return self
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderByClauses[] = "{$column} {$direction}";
        return $this;
    }

    /**
     * Add GROUP BY clause
     * 
     * @param string ...$columns
     * @return self
     */
    public function groupBy(string ...$columns): self
    {
        $this->groupByColumns = array_merge($this->groupByColumns, $columns);
        return $this;
    }

    /**
     * Add HAVING clause
     * 
     * @param string $clause
     * @return self
     */
    public function having(string $clause): self
    {
        $this->havingClause = $clause;
        return $this;
    }

    /**
     * Set LIMIT
     * @param int $limit
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limitValue = $limit;
        return $this;
    }

    /**
     * Set OFFSET
     * @param int $offset
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offsetValue = $offset;
        return $this;
    }

    /**
     * Convenience: set both limit and offset for pagination
     * 
     * @param int $page 1-indexed page number
     * @param int $perPage Items per page
     * @return self
     */
    public function paginate(int $page, int $perPage = 20): self
    {
        $this->limitValue = $perPage;
        $this->offsetValue = ($page - 1) * $perPage;
        return $this;
    }

    // =========================================================================
    // SQL Generation
    // =========================================================================

    /**
     * Build the SQL string dynamically
     * This is the mandatory __toString() that outputs the final query.
     * 
     * @return string
     */
    public function __toString(): string
    {
        if ($this->rawSql !== null) {
            return $this->rawSql;
        }

        switch ($this->operation) {
            case 'SELECT':
                return $this->buildSelect();
            case 'INSERT':
                return $this->buildInsert();
            case 'UPDATE':
                return $this->buildUpdate();
            case 'DELETE':
                return $this->buildDelete();
            default:
                return '';
        }
    }

    private function buildSelect(): string
    {
        $distinctStr = $this->distinct ? 'DISTINCT ' : '';
        $cols = implode(', ', $this->columns);
        $tableRef = $this->alias ? "{$this->table} {$this->alias}" : $this->table;

        $sql = "SELECT {$distinctStr}{$cols} FROM {$tableRef}";
        $sql .= $this->buildJoins();
        $sql .= $this->buildWhere();
        $sql .= $this->buildGroupBy();
        $sql .= $this->buildHaving();
        $sql .= $this->buildOrderBy();
        $sql .= $this->buildLimit();

        return $sql;
    }

    private function buildInsert(): string
    {
        $cols = implode(', ', array_keys($this->data));
        $placeholders = [];
        foreach ($this->data as $col => $val) {
            $placeholder = ':ins_' . $col;
            $placeholders[] = $placeholder;
            $this->bindings[$placeholder] = $this->formatValue($val);
        }
        return "INSERT INTO {$this->table} ({$cols}) VALUES (" . implode(', ', $placeholders) . ")";
    }

    private function buildUpdate(): string
    {
        $sets = [];
        foreach ($this->data as $col => $val) {
            $placeholder = ':upd_' . $col;
            $sets[] = "{$col} = {$placeholder}";
            $this->bindings[$placeholder] = $this->formatValue($val);
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
        $sql .= $this->buildWhere();
        return $sql;
    }

    private function buildDelete(): string
    {
        $sql = "DELETE FROM {$this->table}";
        $sql .= $this->buildWhere();
        return $sql;
    }

    private function buildJoins(): string
    {
        return empty($this->joins) ? '' : ' ' . implode(' ', $this->joins);
    }

    private function buildWhere(): string
    {
        if (empty($this->conditions)) {
            return '';
        }

        $parts = [];
        foreach ($this->conditions as $i => $cond) {
            if ($i === 0) {
                $parts[] = $cond['clause'];
            } else {
                $parts[] = $cond['type'] . ' ' . $cond['clause'];
            }
        }
        return ' WHERE ' . implode(' ', $parts);
    }

    private function buildGroupBy(): string
    {
        return empty($this->groupByColumns)
            ? ''
            : ' GROUP BY ' . implode(', ', $this->groupByColumns);
    }

    private function buildHaving(): string
    {
        return $this->havingClause ? " HAVING {$this->havingClause}" : '';
    }

    private function buildOrderBy(): string
    {
        return empty($this->orderByClauses)
            ? ''
            : ' ORDER BY ' . implode(', ', $this->orderByClauses);
    }

    private function buildLimit(): string
    {
        $sql = '';
        if ($this->limitValue !== null) {
            $sql .= " LIMIT {$this->limitValue}";
        }
        if ($this->offsetValue !== null) {
            $sql .= " OFFSET {$this->offsetValue}";
        }
        return $sql;
    }

    // =========================================================================
    // Execution
    // =========================================================================

    /**
     * Get all bound parameters
     * @return array
     */
    public function getBindings(): array
    {
        if ($this->rawSql !== null) {
            return $this->rawBindings;
        }
        // Force SQL build to populate insert/update bindings
        $this->__toString();
        return $this->bindings;
    }

    /**
     * Execute the query and return all results (for SELECT)
     * 
     * @return array
     */
    public function fetchAll(): array
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = (string) $this;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($this->getBindings());
        return $stmt->fetchAll();
    }

    /**
     * Execute the query and return a single row
     * 
     * @return array|null
     */
    public function fetchOne(): ?array
    {
        if ($this->limitValue === null) {
            $this->limitValue = 1;
        }
        $pdo = Database::getInstance()->getConnection();
        $sql = (string) $this;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($this->getBindings());
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Execute the query and return a single column value
     * 
     * @return mixed
     */
    public function fetchColumn()
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = (string) $this;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($this->getBindings());
        return $stmt->fetchColumn();
    }

    /**
     * Execute INSERT/UPDATE/DELETE and return affected row count
     * 
     * @return int
     */
    public function execute(): int
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = (string) $this;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($this->getBindings());
        return $stmt->rowCount();
    }

    /**
     * Execute INSERT and return last inserted ID
     * 
     * @param string|null $sequence PostgreSQL sequence name (optional)
     * @return string
     */
    public function executeInsert(?string $sequence = null): string
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = (string) $this;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($this->getBindings());
        return $pdo->lastInsertId($sequence);
    }

    /**
     * Get the built SQL string (for debugging)
     * @return string
     */
    public function toSql(): string
    {
        return (string) $this;
    }

    /**
     * Format value for binding
     * Converts booleans to integers (0/1) for PostgreSQL compatibility
     * 
     * @param mixed $value
     * @return mixed
     */
    private function formatValue($value)
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }
        return $value;
    }
}
