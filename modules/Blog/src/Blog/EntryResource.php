<?php
namespace Blog;

use CommonResource\DataSource\Query,
    CommonResource\ResourceCollection,
    CommonResource\Resource\AbstractResource,
    DateTime,
    DateInterval,
    MongoCode;

class EntryResource extends AbstractResource
{
    protected $entityClass = 'Blog\EntryEntity';

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
        $query->where('created', '<=', $_SERVER['REQUEST_TIME']);
        if ($limit) {
            $query->limit($limit, $offset);
        }

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
        if (false !== $offset && $limit) {
            $query->limit($limit, $offset);
        }

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
        $start = new DateTime($year . '-' . $month . '-' . $day);
        $end   = clone $start;
        $end->add(new DateInterval('P1D'));
        $this->createDateRange($query, $start, $end);
        if (false !== $offset && $limit) {
            $query->limit($limit, $offset);
        }

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
              ->where('created', '<=', $_SERVER['REQUEST_TIME']);
        if (false !== $offset && $limit) {
            $query->limit($limit, $offset);
        }


        $entries = $this->getDataSource()->query($query);
        $collection = new $this->collectionClass($entries, $this->entityClass);

        $params['__RESULT__'] = $collection;
        $this->events()->trigger(__FUNCTION__ . '.post', $this, $params);

        return $collection;
    }

    public function getTagCloud()
    {
        $params  = array();
        $results = $this->events()->triggerUntil(__FUNCTION__ . '.pre', $this, $params, function($result) {
            return ($result instanceof ResourceCollection);
        });
        if ($results->stopped()) {
            return $results->last();
        }

        $dataSource = $this->getDataSource();
        switch (get_class($dataSource)) {
            case 'CommonResource\DataSource\Mongo':
                $data = $this->getTagCloudFromMongo($dataSource);
                break;
            default:
                $data = array();
                break;
        }

        $params['__RESULT__'] = $data;
        $this->events()->trigger(__FUNCTION__ . '.post', $this, $params);

        return $data;
    }

    protected function getTagCloudFromMongo($ds)
    {
        $db = $ds->getConnection()->db;
        $map = new MongoCode("function() { 
    if (!this.tags) {
        return;
    }

    for (index in this.tags) {
        emit(this.tags[index], 1);
    }
}");

        $reduce = new MongoCode("function(previous, current) {
    var count = 0;

    for (index in current) {
        count += current[index];
    }

    return count;
}");

        $result = $db->command(array(
            "mapreduce" => "entries", 
            "map"       => $map, 
            "reduce"    => $reduce, 
            "out"       => array("inline" => 1),
        ));

        $tags = array();
        foreach ($result['results'] as $tag) {
            $tags[] = array(
                'title'  => $tag['_id'],
                'weight' => $tag['value'],
            );
        }
        return $tags;
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
