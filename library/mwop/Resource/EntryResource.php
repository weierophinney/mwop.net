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
        $results = static::signals()->emitUntil(function($result) {
            return ($result instanceof ResourceCollection);
        }, 'get-entries.pre', $this, $offset, $limit);
        if ($results->stopped()) {
            return $results->last();
        }

        $query = $this->getQuery();
        $query->where('created', '<=', $_SERVER['REQUEST_TIME'])
              ->limit($limit, $offset);
        $entries = $this->getDataSource()->query($query);
        $collection = new $this->collectionClass($entries, $this->entityClass);

        static::signals()->emit('get-entries.post', $collection, $this, $offset, $limit);

        return $collection;
    }

    public function getEntriesByYear($year, $offset = 0, $limit = 15)
    {
        $results = static::signals()->emitUntil(function($result) {
            return ($result instanceof ResourceCollection);
        }, 'get-entries-by-year.pre', $this, $year, $offset, $limit);
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

        static::signals()->emit('get-entries-by-year.post', $collection, $this, $year, $offset, $limit);

        return $collection;
    }

    public function getEntriesByMonth($month, $year, $offset = 0, $limit = 15)
    {
        $results = static::signals()->emitUntil(function($result) {
            return ($result instanceof ResourceCollection);
        }, 'get-entries-by-month.pre', $this, $month, $year, $offset, $limit);
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

        static::signals()->emit('get-entries-by-month.post', $collection, $this, $month, $year, $offset, $limit);

        return $collection;
    }

    public function getEntriesByDay($day, $month, $year, $offset = 0, $limit = 15)
    {
        $results = static::signals()->emitUntil(function($result) {
            return ($result instanceof ResourceCollection);
        }, 'get-entries-by-day.pre', $this, $day, $month, $year, $offset, $limit);
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

        static::signals()->emit('get-entries-by-day.post', $collection, $this, $day, $month, $year, $offset, $limit);

        return $collection;
    }

    public function getEntriesByTag($tag, $offset = 0, $limit = 15)
    {
        $results = static::signals()->emitUntil(function($result) {
            return ($result instanceof ResourceCollection);
        }, 'get-entries-by-tag.pre', $this, $tag, $offset, $limit);
        if ($results->stopped()) {
            return $results->last();
        }

        $query = $this->getQuery();
        $query->where('tags', '=', $tag)
              ->where('created', '<=', $_SERVER['REQUEST_TIME'])
              ->limit($limit, $offset);

        $entries = $this->getDataSource()->query($query);
        $collection = new $this->collectionClass($entries, $this->entityClass);

        static::signals()->emit('get-entries-by-tag.post', $collection, $this, $tag, $offset, $limit);

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
