<?php
namespace mwop\Resource;

use mwop\DataSource\Query,
    mwop\Stdlib\ResourceCollection,
    DateTime,
    DateInterval;

class EntryResource extends AbstractResource
{
    protected $entityClass = 'mwop\Entity\Entry';

    public function getEntries($offset = 0, $limit = 15)
    {
        $params  = compact('offset', 'limit');
        $results = $this->events()->triggerUntil(__FUNCTION__ . '.pre', $this, $params, function($result) {
            return ($result instanceof ResourceCollection);
        });
        if ($results->stopped()) {
            return $results->last();
        }

        $query = $this->getQuery();
        $query->where('created', '<=', $_SERVER['REQUEST_TIME'])
              ->limit($limit, $offset);
        $entries = $this->getDataSource()->query($query);
        $collection = new $this->collectionClass($entries, $this->entityClass);

        $params['__RESULT__'] = $collection;
        $this->events()->trigger(__FUNCTION__ . '.post', $this, $params);

        return $collection;
    }

    public function getEntriesByYear($year, $offset = 0, $limit = 15)
    {
        $params = compact('year', 'offset', 'limit');
        $results = $this->events()->triggerUntil(__FUNCTION__ . '.pre', $this, $params, function($result) {
            return ($result instanceof ResourceCollection);
        });
        if ($results->stopped()) {
            return $results->last();
        }

        $query = $this->getQuery();
        $start = new DateTime($year . '-01-01');
        $end   = clone $start;
        $end->add(new DateInterval('P1Y'));
        $this->createDateRange($query, $start, $end);
        $query->limit($limit, $offset);

        $entries = $this->getDataSource()->query($query);
        $collection = new $this->collectionClass($entries, $this->entityClass);

        $params['__RESULT__'] = $collection;
        $this->events()->trigger(__FUNCTION__ . '.post', $this, $params);

        return $collection;
    }

    public function getEntriesByMonth($month, $year, $offset = 0, $limit = 15)
    {
        $params  = compact('month', 'year', 'offset', 'limit');
        $results = $this->events()->triggerUntil(__FUNCTION__ . '.pre', $this, $params, function($result) {
            return ($result instanceof ResourceCollection);
        });
        if ($results->stopped()) {
            return $results->last();
        }

        $query = $this->getQuery();
        $month = ($month < 9) ? '0' . $month : $month;
        $start = new DateTime($year . '-' . $month . '-01');
        $end   = clone $start;
        $end->add(new DateInterval('P1M'));
        $this->createDateRange($query, $start, $end);
        $query->limit($limit, $offset);

        $entries = $this->getDataSource()->query($query);
        $collection = new $this->collectionClass($entries, $this->entityClass);

        $params['__RESULT__'] = $collection;
        $this->events()->trigger(__FUNCTION__ . '.post', $this, $params);

        return $collection;
    }

    public function getEntriesByDay($day, $month, $year, $offset = 0, $limit = 15)
    {
        $params  = compact('day', 'month', 'year', 'offset', 'limit');
        $results = $this->events()->triggerUntil(__FUNCTION__ . '.pre', $this, $params, function($result) {
            return ($result instanceof ResourceCollection);
        });
        if ($results->stopped()) {
            return $results->last();
        }

        $query = $this->getQuery();
        $month = ($month < 9) ? '0' . $month : $month;
        $day   = ($day < 9) ? '0' . $day : $day;
        $start = new DateTime($year . '-' . $month . '-' . $day);
        $end   = clone $start;
        $end->add(new DateInterval('P1D'));
        $this->createDateRange($query, $start, $end);
        $query->limit($limit, $offset);

        $entries = $this->getDataSource()->query($query);
        $collection = new $this->collectionClass($entries, $this->entityClass);

        $params['__RESULT__'] = $collection;
        $this->events()->trigger(__FUNCTION__ . '.post', $this, $params);

        return $collection;
    }

    public function getEntriesByTag($tag, $offset = 0, $limit = 15)
    {
        $params  = compact('tag', 'offset', 'limit');
        $results = $this->events()->triggerUntil(__FUNCTION__ . '.pre', $this, $params, function($result) {
            return ($result instanceof ResourceCollection);
        });
        if ($results->stopped()) {
            return $results->last();
        }

        $query = $this->getQuery();
        $query->where('tags', '=', $tag)
              ->where('created', '<=', $_SERVER['REQUEST_TIME'])
              ->limit($limit, $offset);

        $entries = $this->getDataSource()->query($query);
        $collection = new $this->collectionClass($entries, $this->entityClass);

        $params['__RESULT__'] = $collection;
        $this->events()->trigger(__FUNCTION__ . '.post', $this, $params);

        return $collection;
    }

    protected function getQuery()
    {
        $query = new Query();
        $query->where('is_draft', '=', false)
              ->where('is_public', '=', true)
              ->sort('created', 'DESC');
        return $query;
    }

    protected function createDateRange($query, DateTime $start, DateTime $end)
    {
        if ($end->getTimestamp() > $_SERVER['REQUEST_TIME']) {              
            $end = new DateTime('@' . $_SERVER['REQUEST_TIME']);           
        }                                                                       
                                                                                
        $query->where('created', '>=', $start->getTimestamp())
              ->where('created', '<', $end->getTimestamp());
        return $query;                                                        
    }
}
