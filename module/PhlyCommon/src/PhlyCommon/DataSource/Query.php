<?php
namespace PhlyCommon\DataSource;

use PhlyCommon\Query as Queryable;

class Query implements Queryable
{
    protected $where  = array();
    protected $limit  = false;
    protected $offset = 0;
    protected $sort   = false;

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

    /**
     * Set sort field and direction
     * 
     * @todo   Allow aggregating multiple sort statements
     * @param  string $key 
     * @param  string $direction 
     * @return Query
     */
    public function sort($key, $direction = 'ASC')
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, array('ASC', 'DESC'))) {
            $direction = 'ASC';
        }
        $this->sort = $key . ' ' . $direction;
        return $this;
    }

    /**
     * Get sort criteria
     * 
     * @return string|false
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Serialize to array
     * 
     * @return array
     */
    public function toArray()
    {
        $where = array();
        foreach ($this->getWhereClauses() as $clause) {
            $where[] = (array) $clause;
        }
        return array(
            'where'  => $where,
            'limit'  => $this->getLimit(),
            'offset' => $this->getOffset(),
            'sort'   => $this->getSort(),
        );
    }

    /**
     * Populate from array
     * 
     * @param  array $definition 
     * @return Query
     */
    public function fromArray(array $definition)
    {
        $offset = 0;
        $limit  = false;
        foreach ($definition as $key => $value) {
            switch (strtolower($key)) {
                case 'offset':
                    $offset = $value;
                    break;
                case 'limit':
                    $limit = $value;
                    break;
                case 'sort':
                    if (!$value) {
                        $this->sort = false;
                        break;
                    }
                    list($field, $direction) = explode(' ', $value);
                    $this->sort($field, $direction);
                    break;
                case 'where':
                    if (!is_array($value)) {
                        break;
                    }
                    foreach ($value as $args) {
                        if (is_array($args) || $args instanceof Where) {
                            if (is_array($args)) {
                                $args = (object) $args;
                            }
                            $args->type = $args->type ?: 'and';
                            switch (strtolower($args->type)) {
                                case 'or':
                                    $this->orWhere($args->key, $args->comparison, $args->value);
                                    break;
                                case 'and':
                                default:
                                    $this->where($args->key, $args->comparison, $args->value);
                                    break;
                            }
                        }
                    }
                    break;
            }
        }
        if (false !== $limit) {
            $this->limit($limit, $offset);
        }

        return $this;
    }
}
