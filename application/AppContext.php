<?php

use Zend\Di\ServiceLocator;

class AppContext extends ServiceLocator
{

    public function get($name, array $params = array())
    {
        switch ($name) {
            case 'router':
            case 'mwop\Mvc\Router':
                if (isset($this->services['mwop\Mvc\Router'])) {
                    return $this->services['mwop\Mvc\Router'];
                }
        
                $object = new mwop\Mvc\Router();
                $object->addRoutes(array (
          'blog-create-form' => 
          array (
            'params' => 
            array (
              0 => '#^/(?P<controller>blog)/admin/(?P<action>create)#',
              1 => '/blog/admin/create',
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
        
            case 'mongocxn':
            case 'Mongo':
                if (isset($this->services['Mongo'])) {
                    return $this->services['Mongo'];
                }
        
                $object = new Mongo();
                $this->services['Mongo'] = $object;
                return $object;
        
            case 'mongodb':
            case 'MongoDB':
                if (isset($this->services['MongoDB'])) {
                    return $this->services['MongoDB'];
                }
        
                $object = new MongoDB($this->get('mongocxn'), 'mwoptest');
                $this->services['MongoDB'] = $object;
                return $object;
        
            case 'mongo-collection-entries':
            case 'MongoCollection':
                if (isset($this->services['MongoCollection'])) {
                    return $this->services['MongoCollection'];
                }
        
                $object = new MongoCollection($this->get('mongodb'), 'entries');
                $this->services['MongoCollection'] = $object;
                return $object;
        
            case 'data-source':
            case 'mwop\DataSource\Mongo':
                if (isset($this->services['mwop\DataSource\Mongo'])) {
                    return $this->services['mwop\DataSource\Mongo'];
                }
        
                $object = new mwop\DataSource\Mongo($this->get('mongo-collection-entries'));
                $this->services['mwop\DataSource\Mongo'] = $object;
                return $object;
        
            case 'resource-entry':
            case 'mwop\Resource\EntryResource':
                if (isset($this->services['mwop\Resource\EntryResource'])) {
                    return $this->services['mwop\Resource\EntryResource'];
                }
        
                $object = new mwop\Resource\EntryResource();
                $object->setDataSource($this->get('data-source'));
                $object->setCollectionClass('mwop\\Resource\\MongoCollection');
                $this->services['mwop\Resource\EntryResource'] = $object;
                return $object;
        
            case 'Blog\Controller\Entry':
                if (isset($this->services['Blog\Controller\Entry'])) {
                    return $this->services['Blog\Controller\Entry'];
                }
        
                $object = new Blog\Controller\Entry();
                $object->resource($this->get('resource-entry'));
                $this->services['Blog\Controller\Entry'] = $object;
                return $object;
        
            default:
                return parent::get($name, $params);
        }
    }

    public function getRouter()
    {
        return $this->get('mwop\Mvc\Router');
    }

    public function getMongocxn()
    {
        return $this->get('Mongo');
    }

    public function getMongodb()
    {
        return $this->get('MongoDB');
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

