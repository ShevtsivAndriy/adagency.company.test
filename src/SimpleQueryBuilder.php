<?php
declare(strict_types=1);

require 'SimpleQueryBuilderInterface.php';

/**
 * Class SimpleQueryBuilder
 */
class SimpleQueryBuilder implements SimpleQueryBuilderInterface
{
    /**
     * @var array|string
     */
    protected $select;

    /**
     * @var string|SimpleQueryBuilderInterface|array<string|SimpleQueryBuilderInterface>
     */
    protected $fromTables;

    /**
     * @var string|array
     */
    protected $where;

    /**
     * @var string|array
     */
    protected $groupBy;

    /**
     * @var string|array
     */
    protected $having;

    /**
     * @var string|array
     */
    protected $orderBy;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @param array|string $fields
     * @return SimpleQueryBuilderInterface
     */
    public function select($fields): SimpleQueryBuilderInterface
    {
        $this->select = $fields;
        return $this;
    }

    /**
     * @param string|SimpleQueryBuilderInterface|array<string|SimpleQueryBuilderInterface> $tables
     * @return SimpleQueryBuilderInterface
     */
    public function from($tables): SimpleQueryBuilderInterface
    {
        $this->fromTables = $tables;
        return $this;
    }

    /**
     * @param string|array $conditions
     * @return SimpleQueryBuilderInterface
     */
    public function where($conditions): SimpleQueryBuilderInterface
    {
        $this->where = $conditions;
        return $this;
    }

    /**
     * @param string|array $fields
     * @return SimpleQueryBuilderInterface
     */
    public function groupBy($fields): SimpleQueryBuilderInterface
    {
        $this->groupBy = $fields;
        return $this;
    }

    /**
     * @param string|array $conditions
     * @return SimpleQueryBuilderInterface
     */
    public function having($conditions): SimpleQueryBuilderInterface
    {
        $this->having = $conditions;
        return $this;
    }

    /**
     * @param string|array $fields
     * @return SimpleQueryBuilderInterface
     */
    public function orderBy($fields): SimpleQueryBuilderInterface
    {
        $this->orderBy = $fields;
        return $this;
    }

    /**
     * @param int $limit
     * @return SimpleQueryBuilderInterface
     */
    public function limit($limit): SimpleQueryBuilderInterface
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int $offset
     * @return SimpleQueryBuilderInterface
     */
    public function offset($offset): SimpleQueryBuilderInterface
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @throws LogicException
     * @return string
     */
    public function build(): string
    {
        /**
         * Clear query builder string.
         */
        $query = '';

        /**
         * Prepare 'SELECT' part of SQL query.
         */
        if($this->select)
        {
            $select = is_array($this->select) ? implode(', ', $this->select) : $this->select;
            $query = "SELECT " . $select;
        }
        else
        {
            throw new \LogicException('Empty SELECT part.');
        }

        /**
         * Prepare 'FROM' part of SQL query.
         */
        if($this->fromTables)
        {
            if(is_string($this->fromTables))
            {
                $query .= " FROM " . $this->fromTables;
            }
            elseif ($this->fromTables instanceof SimpleQueryBuilderInterface)
            {
                $query .= " FROM (" . $this->fromTables->build() . ")";
            }
            elseif (is_array($this->fromTables))
            {
                $query .= " FROM ";
                foreach ($this->fromTables as $from)
                {
                    if(is_string($from))
                    {
                        $query .= $from;
                    }
                    elseif ($from instanceof SimpleQueryBuilderInterface)
                    {
                        $query .= "(" . $from->build() . ")";
                    }
                    $query .= (next($this->fromTables) === false) ? '' : ', ';
                }
            }
        }
        else
        {
            throw new \LogicException('Empty FROM part');
        }

        /**
         * Prepare "WHERE" part of SQL query.
         */
        if($this->where)
        {
            $where = (is_array($this->where)) ? implode(', ', $this->where) : $this->where;
            $query .= " WHERE " . $where;
        }

        /**
         * Prepare "GROUP BY" part of SQl query.
         */
        if($this->groupBy)
        {
            $groupBy = (is_array($this->groupBy)) ? implode(', ', $this->groupBy) : $this->groupBy;
            $query .= " GROUP BY " . $groupBy;
        }

        /**
         * Prepare "HAVING" part of SQL query.
         */
        if($this->having)
        {
            $having = (is_array($this->having)) ? implode(', ', $this->having) : $this->having;
            $query .= " HAVING " . $having;
        }

        /**
         * Prepare "ORDER BY" part of SQL query.
         */
        if($this->orderBy)
        {
            $orderBy = (is_array($this->orderBy)) ? implode(', ', $this->orderBy) : $this->orderBy;
            $query .= " ORDER BY " . $orderBy;
        }

        /**
         * Prepare "LIMIT" part of SQL query.
         */
        if($this->limit)
        {
            $query .= " LIMIT " . $this->limit;
        }

        /**
         * Prepare "OFFSET" part of SQL query.
         */
        if($this->offset)
        {
            $query .= " OFFSET " . $this->offset;
        }

        return $query;
    }

    /**
     * @throws LogicException
     * @return string
     */
    public function buildCount(): string
    {
        return "COUNT (" . $this->build() .")";
    }
}