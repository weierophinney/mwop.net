<?php
namespace Blog\Controller;

use mwop\Controller\Restful as RestfulController,
    mwop\DataSource\Mongo as MongoDataSource,
    mwop\Stdlib\Resource,
    mwop\Resource\EntryResource,
    Mongo;

class Entry extends RestfulController
{
    public function resource(Resource $resource = null)
    {
        $params = compact('resource');
        $this->events()->trigger(__FUNCTION__ . '.pre', $this, $params);
        if (null !== $resource) {
            $this->events()->trigger(__FUNCTION__ . '.set', $this, $params);
            $this->resource = $resource;
        } elseif (null === $this->resource) {
            $this->events()->trigger(__FUNCTION__ . '.init', $this, $params);
            $this->resource = new EntryResource();
            $mongo      = new Mongo();
            $mongoDb    = $mongo->mwoptest;
            $collection = $mongoDb->entries;
            $dataSource = new MongoDataSource($collection);
            $this->resource->setDataSource($dataSource)
                           ->setCollectionClass('mwop\Resource\MongoCollection');
        }
        return $this->resource;
    }

    public function createAction()
    {
        $request = $this->getRequest();
        if (!$request->isGet()) {
            $response = $this->getResponse();
            $response->headers()->setStatusCode(405);
            $response->setContent('<h2>Illegal Method</h2>');
            return $response;
        }
        return array('url' => '/blog');
    }
}
