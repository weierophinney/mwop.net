<?php
namespace Blog\Controller;

use mwop\Controller\Restful as RestfulController,
    mwop\DataSource\Mongo as MongoDataSource,
    mwop\Stdlib\Resource,
    mwop\Resource\EntryResource,
    Mongo;

class Entry extends RestfulController
{
    protected $views = array(
        'getList' => 'Blog\View\Entries',
        'get'     => 'Blog\View\Entry',
    );

    public function resource(Resource $resource = null)
    {
        if (null !== $resource) {
            if (!$resource instanceof EntryResource) {
                throw new \DomainException('Entry controller expects an Entry resource');
            }
            $this->resource = $resource;
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
