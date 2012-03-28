<?php
namespace PhlyCommon\DataSource\Mongo;

use PhlyCommon\Query;

class QueryParser
{
    protected $criteria = array();
    protected $sort     = false;
    protected $skip     = false;
    protected $limit    = false;

    public function __construct(Query $query)
    {
        $this->parse($query);
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function getSkip()
    {
        return $this->skip;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Parse the query object into Mongo-compatible structures
     * 
     * @param  Query $query 
     * @return void
     */
    protected function parse(Query $query)
    {
        $this->parseWhere($query->getWhereClauses());
        $this->parseSort($query->getSort());
        $this->skip  = $query->getOffset();
        $this->limit = $query->getLimit();
    }

    /**
     * Parse query clauses
     *
     * Parses query clauses into a MongoCollection::find()-compatible data 
     * structure.
     * 
     * @todo   Implement all criteria types
     * @todo   Better "OR" handling
     * @param  array $clauses 
     * @return void
     */
    protected function parseWhere(array $clauses)
    {
        $andCriteria = array();
        $orCriteria  = array();
        foreach ($clauses as $clause) {
            $field      = $clause->key;
            $comparison = false;
            switch ($clause->comparison) {
                case '<':
                    $comparison = '$lt';
                    break;
                case '<=':
                    $comparison = '$lte';
                    break;
                case '>':
                    $comparison = '$gt';
                    break;
                case '>=':
                    $comparison = '$gte';
                    break;
                case '!=':
                    $comparison = '$ne';
                    break;
                case '=':
                default:
                    break;
            }

            switch ($clause->type) {
                case 'OR':
                    $criteria =& $orCriteria;
                    break;
                case 'AND':
                default:
                    $criteria =& $andCriteria;
                    break;
            }
            $key = $clause->key;
            if (!$comparison) {
                if (array_key_exists($key, $criteria)) {
                    // what do we do here? $in?
                    if (is_array($criteria[$key])) {
                        if (array_key_exists('$in', $criteria[$key])) {
                            $criteria[$key]['$in'][] = $clause->value;
                        } else {
                            $criteria[$key]['$in'] = array($clause->value);
                        }
                    }
                } else {
                    $criteria[$key] = $clause->value;
                }
            } else {
                if (array_key_exists($key, $criteria)) {
                    if (is_array($criteria[$key])) {
                        $criteria[$key][$comparison] = $clause->value;
                    } else {
                        // what do we do here? $in?
                        $criteria[$key] = array(
                            '$in'       => array($criteria[$key]), 
                            $comparison => $clause->value,
                        );
                    }
                } else {
                    $criteria[$key] = array($comparison => $clause->value);
                }
            }
        }
        if (!empty($orCriteria)) {
            $andCriteria['$or'] = $orCriteria;
        }
        $this->criteria = $andCriteria;
    }

    /**
     * Parse a sort statement
     *
     * Parses a sort statement into a MongoCursor-compatible sort array.
     * 
     * @todo   Allow aggregating multiple sort statements
     * @param  string $sort 
     * @return void
     */
    protected function parseSort($sort)
    {
        if (!$sort) {
            return;
        }

        list($field, $direction) = explode(' ', $sort, 2);
        $sort = array();
        switch ($direction) {
            case 'DESC':
                $sort[$field] = -1;
                break;
            case 'ASC':
            default:
                $sort[$field] = 1;
                break;
        }
        $this->sort = $sort;
    }
}
