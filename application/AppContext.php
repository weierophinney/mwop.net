<?php

use Zend\Di\DependencyInjectionContainer;

class AppContext extends DependencyInjectionContainer
{

    public function get($name, array $params = array())
    {
        switch ($name) {
            case 'router':
            case 'mwop\Mvc\Router':
                return $this->getMwopMvcRouter();
        
            case 'mongocxn':
            case 'Mongo':
                return $this->getMongo();
        
            case 'MongoDB':
                return $this->getMongoDB();
        
            case 'mongo-collection-entries':
            case 'MongoCollection':
                return $this->getMongoCollection();
        
            case 'data-source':
            case 'mwop\DataSource\Mongo':
                return $this->getMwopDataSourceMongo();
        
            case 'resource-entry':
            case 'mwop\Resource\EntryResource':
                return $this->getMwopResourceEntryResource();
        
            case 'Blog\Controller\Entry':
                return $this->getBlogControllerEntry();
        
            default:
                return parent::get($name, $params);
        }
    }

    public function getMwopMvcRouter()
    {
        if (isset($this->services['mwop\Mvc\Router'])) {
            return $this->services['mwop\Mvc\Router'];
        }
        
        $object = new \mwop\Mvc\Router();
        $object->addRoutes(array (
          'home' => 
          array (
            'class' => 'mwop\\Mvc\\Router\\StaticRoute',
            'params' => 
            array (
              0 => '/',
              1 => 
              array (
                'controller' => 'page',
                'page' => 'home',
              ),
            ),
          ),
          'comics' => 
          array (
            'class' => 'mwop\\Mvc\\Router\\StaticRoute',
            'params' => 
            array (
              0 => '/comics',
              1 => 
              array (
                'controller' => 'page',
                'page' => 'comics',
              ),
            ),
          ),
          'blog-create-form' => 
          array (
            'class' => 'mwop\\Mvc\\Router\\StaticRoute',
            'params' => 
            array (
              0 => '/blog/admin/create',
              1 => 
              array (
                'controller' => 'blog',
                'action' => 'create',
              ),
            ),
          ),
          'blog-tag' => 
          array (
            'params' => 
            array (
              0 => '#^/(?P<controller>blog)/(?P<action>tag)/(?P<tag>[^/]+)#',
              1 => '/blog/tag/{tag}',
            ),
          ),
          'blog-year' => 
          array (
            'params' => 
            array (
              0 => '#^/(?P<controller>blog)/(?P<action>year)/(?P<year>\\d{4})#',
              1 => '/blog/year/{year}',
            ),
          ),
          'blog-month' => 
          array (
            'params' => 
            array (
              0 => '#^/(?P<controller>blog)/(?P<action>month)/(?P<year>\\d{4})/(?P<month>\\d{2})#',
              1 => '/blog/month/{year}/{month}',
            ),
          ),
          'blog-day' => 
          array (
            'params' => 
            array (
              0 => '#^/(?P<controller>blog)/(?P<action>day)/(?P<year>\\d{4})/(?P<month>\\d{2})/(?P<day>\\d{2})#',
              1 => '/blog/day/{year}/{month}/{day}',
            ),
          ),
          'blog' => 
          array (
            'params' => 
            array (
              0 => '#^/(?P<controller>blog)(/(?P<id>[^/]+))?#',
              1 => '/blog/{id}',
            ),
          ),
        ));
        $this->services['mwop\Mvc\Router'] = $object;
        return $object;
    }

    public function getMongo()
    {
        if (isset($this->services['Mongo'])) {
            return $this->services['Mongo'];
        }
        
        $object = new \Mongo();
        $this->services['Mongo'] = $object;
        return $object;
    }

    public function getMongoDB()
    {
        if (isset($this->services['MongoDB'])) {
            return $this->services['MongoDB'];
        }
        
        $object = new \MongoDB($this->getMongocxn(), 'mwoptest');
        $this->services['MongoDB'] = $object;
        return $object;
    }

    public function getMongoCollection()
    {
        if (isset($this->services['MongoCollection'])) {
            return $this->services['MongoCollection'];
        }
        
        $object = new \MongoCollection($this->getMongoDB(), 'entries');
        $this->services['MongoCollection'] = $object;
        return $object;
    }

    public function getMwopDataSourceMongo()
    {
        if (isset($this->services['mwop\DataSource\Mongo'])) {
            return $this->services['mwop\DataSource\Mongo'];
        }
        
        $object = new \mwop\DataSource\Mongo($this->getMongoCollectionEntries());
        $this->services['mwop\DataSource\Mongo'] = $object;
        return $object;
    }

    public function getMwopResourceEntryResource()
    {
        if (isset($this->services['mwop\Resource\EntryResource'])) {
            return $this->services['mwop\Resource\EntryResource'];
        }
        
        $object = new \mwop\Resource\EntryResource();
        $object->setDataSource($this->getDataSource());
        $object->setCollectionClass('mwop\\Resource\\MongoCollection');
        $this->services['mwop\Resource\EntryResource'] = $object;
        return $object;
    }

    public function getBlogControllerEntry()
    {
        if (isset($this->services['Blog\Controller\Entry'])) {
            return $this->services['Blog\Controller\Entry'];
        }
        
        $object = new \Blog\Controller\Entry();
        $object->resource($this->getResourceEntry());
        $this->services['Blog\Controller\Entry'] = $object;
        return $object;
    }

    public function getRouter()
    {
        return $this->get('mwop\Mvc\Router');
    }

    public function getMongocxn()
    {
        return $this->get('Mongo');
    }

    public function getMongoCollectionEntries()
    {
        return $this->get('MongoCollection');
    }

    public function getDataSource()
    {
        return $this->get('mwop\DataSource\Mongo');
    }

    public function getResourceEntry()
    {
        return $this->get('mwop\Resource\EntryResource');
    }


}

