<?php
namespace Blog\Controller;

use Blog\View\Entries as EntriesView,
    Blog\View\TagCloud,
    mwop\Controller\Restful as RestfulController,
    mwop\DataSource\Mongo as MongoDataSource,
    mwop\Stdlib\Resource,
    mwop\Resource\EntryResource,
    Phly\Mustache\Pragma\SubView,
    Mongo;

class Entry extends RestfulController
{
    protected $views = array(
        'getList' => 'Blog\View\Entries',
        'get'     => 'Blog\View\Entry',
    );

    public function __construct()
    {
        $this->events()->attach('dispatch.post', function($e) {
            $view       = $e->getParam('__RESULT__');
            $controller = $e->getTarget();
            $tags       = $controller->resource()->getTagCloud();
            $subView    = new SubView('tag-cloud', new TagCloud($tags));

            if (is_array($view)) {
                if (isset($view[ 'layout' ])) {
                    if (is_array($view[ 'layout' ])) {
                        $view[ 'layout' ]['footer']['tags']['cloud'] = $subView;
                    }
                } else {
                    $view[ 'layout' ] = array(
                        'footer' => array(
                            'tags' => array(
                                'cloud' => $subView,
                            ),
                        ),
                    );
                }
            } elseif (is_object($view)) {
                if (isset($view->layout)) {
                    if (is_array($view->layout)) {
                        $view->layout['footer']['tags']['cloud'] = $subView;
                    } elseif (is_object($view->layout)) {
                        if (isset($view->layout->footer)) {
                            $view->layout->footer['tags']['cloud'] = $subView;
                        } else {
                            $view->layout->footer = array('tags' => array('cloud' => $subView));
                        }
                    }
                } else {
                    $view->layout = array(
                        'footer' => array(
                            'tags' => array(
                                'cloud' => $subView,
                            ),
                        ),
                    );
                }
            }

            $e->setParam('__RESULT__', $view);
        });
    }

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

    public function getList()
    {
        $entries = $this->resource()->getEntries(0, false);
        return new EntriesView(array(
            'entities' => $entries,
            'sidebar'  => array(
                'cloud' => $this->resource()->getTagCloud(),
            ),
            'request'  => $this->getRequest(),
        ));
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
