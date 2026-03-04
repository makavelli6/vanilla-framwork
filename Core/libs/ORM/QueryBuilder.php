<?php

class QueryBuilder
{
    protected $db;
    protected $table;
    protected $conditions = [];
    protected $bindings = [];
    protected $limit = null;
    protected $offset = null;
    protected $sort = null;
    protected $populateFields = [];

    public function __construct($db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    public function find(array $conditions = [])
    {
        foreach ($conditions as $field => $value) {
            $this->parseCondition($field, $value);
        }
        return $this;
    }

    protected function parseCondition($field, $value)
    {
        if (is_array($value)) {
            // MongoDB syntax operators ($gt, $lt, etc.)
            foreach ($value as $operator => $val) {
                $paramName = "{$field}_" . str_replace('$', '', $operator) . "_" . count($this->bindings);
                $sqlOperator = $this->translateOperator($operator);
                
                if ($sqlOperator === 'IN' && is_array($val)) {
                    $inParams = [];
                    foreach ($val as $i => $v) {
                        $inParamName = "{$paramName}_{$i}";
                        $inParams[] = ":{$inParamName}";
                        $this->bindings[":{$inParamName}"] = $v;
                    }
                    $this->conditions[] = "`$field` IN (" . implode(', ', $inParams) . ")";
                } else {
                    $this->conditions[] = "`$field` $sqlOperator :$paramName";
                    $this->bindings[":$paramName"] = $val;
                }
            }
        } else {
            // Direct equality
            $paramName = "{$field}_eq_" . count($this->bindings);
            $this->conditions[] = "`$field` = :$paramName";
            $this->bindings[":$paramName"] = $value;
        }
    }

    protected function translateOperator($operator)
    {
        $map = [
            '$gt'  => '>',
            '$lt'  => '<',
            '$gte' => '>=',
            '$lte' => '<=',
            '$ne'  => '!=',
            '$in'  => 'IN',
            '$nin' => 'NOT IN'
        ];
        return $map[$operator] ?? '=';
    }

    public function sort(array $sort)
    {
        $sorts = [];
        foreach ($sort as $field => $direction) {
            $dir = ($direction == -1 || strtoupper($direction) === 'DESC') ? 'DESC' : 'ASC';
            $sorts[] = "`$field` $dir";
        }
        if (!empty($sorts)) {
            $this->sort = "ORDER BY " . implode(', ', $sorts);
        }
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    public function skip($offset)
    {
        $this->offset = (int) $offset;
        return $this;
    }

    public function populate($relation)
    {
        // For simplicity, we just store what fields should ideally be populated.
        // A full ORM populate requires knowing relation maps (HasOne, HasMany).
        $this->populateFields[] = $relation;
        return $this;
    }

    public function get()
    {
        $sql = "SELECT * FROM `{$this->table}`";
        
        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        if ($this->sort) {
            $sql .= " {$this->sort}";
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
            if ($this->offset !== null) {
                $sql .= " OFFSET {$this->offset}";
            }
        }

        return $this->db->select($sql, $this->bindings);
    }

    public function first()
    {
        $this->limit(1);
        $results = $this->get();
        return !empty($results) ? $results[0] : null;
    }
}
?>
