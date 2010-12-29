<?php
namespace mwop\DataSource;

use mwop\Stdlib\Query as Queryable;

class Query implements Queryable
{
    protected $where  = array();
    protected $limit  = false;
    protected $offset = 0;

    /**
     * Add a where clause
     * 
     * @param  string $key 
     * @param  string $comparison 
     * @param  mixed $value 
     * @return Query
     */
    public function where($key, $comparison, $value = null)
    {
        $this->where[] = new Where(
            'and',
            $key,
            $comparison,
            $value
        );
        return $this;
    }

    /**
     * Add an OR'd where clause
     * 
     * @param  string $key 
     * @param  string $comparison 
     * @param  mixed $value 
     * @return Query
     */
    public function orWhere($key, $comparison, $value = null)
    {
        $this->where[] = new Where(
            'or',
            $key,
            $comparison,
            $value
        );
        return $this;
    }

    /**
     * Set a limit and optionally offset (for pagination)
     * 
     * @param  int $count 
     * @param  int $offset 
     * @return Query
     */
    public function limit($count, $offset = 0)
    {
        $this->limit  = $count;
        $this->offset = $offset;
        return $this;
    }

    /**
     * Get all where clauses, in order
     * 
     * @return Where[]
     */
    public function getWhereClauses()
    {
        return $this->where;
    }

    /**
     * Get limit
     * 
     * @return false|int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get offset
     * 
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }
}
