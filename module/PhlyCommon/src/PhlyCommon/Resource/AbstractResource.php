<?php
namespace PhlyCommon\Resource;

use PhlyCommon\Resource,
    PhlyCommon\ResourceCollection,
    PhlyCommon\DataSource,
    PhlyCommon\DataSource\Query,
    ArrayObject,
    DomainException,
    InvalidArgumentException,
    Zend\Acl\Resource as AclResource,
    Zend\EventManager\EventCollection as Events,
    Zend\EventManager\EventManager;

abstract class AbstractResource implements Resource, AclResource
{
    protected $entityClass;
    protected $collectionClass = 'PhlyCommon\Resource\Collection';
    protected $dataSource;
    protected $events;

    /**
     * Event manager for entity resource
     *
     * Allows injecting an event manager, or retrieving the event manager for
     * the purpose of connecting handlers.
     * 
     * @param  null|Events $events 
     * @return Events
     */
    public function events(Events $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        } elseif (null === $this->events) {
            $this->events = new EventManager(array(get_called_class(), __CLASS__));
        }
        return $this->events;
    }

    /**
     * Get all entries
     *
     * Emits two signals:
     * - get-all.pre: Receives instance of resource as sole argument. If a 
     *   signal returns a collection, the method returns it immediately.
     * - get-all.post: Receives two arguments, the items as a ResourceCollection 
     *   object, and the resource.
     * 
     * @return ResourceCollection
     */
    public function getAll()
    {
        $results = $this->events()->triggerUntil(__FUNCTION__ . '.pre', $this, array(), function($result) {
            return ($result instanceof ResourceCollection);
        });
        if ($results->stopped()) {
            return $results->last();
        }

        $items = $this->getDataSource()->query(new Query());
        $this->events()->trigger(__FUNCTION__ . '.post-query', $this, array('items' => $items));
        if (is_scalar($items) && empty($items)) {
            $items = array();
        }

        $items =  new $this->collectionClass($items, $this->entityClass);

        $this->events()->trigger(__FUNCTION__ . '.post', $this, array('items' => $items));
        return $items;
    }

    /**
     * Get a single Entity by ID
     *
     * Emits two signals:
     * - get.pre: Receives two arguments, the current resource instance and id. 
     *   If a signal returns an Entity object, the method returns it 
     *   immediately.
     * - get.post: Receives two arguments, the retrieved Entity, and the 
     *   resource.
     * 
     * @param  string|int $id
     * @return null|Entity
     */
    public function get($id)
    {
        $entityClass = $this->entityClass;
        $results = $this->events()->triggerUntil(__FUNCTION__ . '.pre', $this, array('id' => $id), function($result) use ($entityClass) {
            return ($result instanceof $entityClass);
        });
        if ($results->stopped()) {
            return $results->last();
        }

        $data = $this->getDataSource()->get($id);
        if (empty($data)) {
            return null;
        }
        $entity = new $entityClass();
        $entity->fromArray($data);

        $this->events()->trigger(__FUNCTION__ . '.post', $this, array('entity' => $entity));

        return $entity;
    }

    /**
     * Create a new entity
     *
     * Emits two signals:
     * - create.pre: emitted after verifying we have an appropriate spec, but
     *   prior to validation and passing to the data source. Receives the spec,
     *   which will be an Entity object at this point, and the resource object.
     * - create.post: emitted after creation of the spec; receives the created
     *   Entity object.
     * 
     * @param  array|Entity $spec 
     * @return Entity
     * @throws InvalidArgumentException
     */
    public function create($spec)
    {
        if (is_array($spec)) {
            $entity = new $this->entityClass();
            $entity->fromArray($spec);
            $spec = $entity;
        }
        if (!$spec instanceof $this->entityClass) {
            throw new InvalidArgumentException(sprintf(
                'Expected an array or object of type "%s"; received "%s"',
                $this->entityClass,
                (is_object($spec) ? get_class($spec) : gettype($spec))
            ));
        }

        $this->events()->trigger(__FUNCTION__ . '.pre', $this, array('spec' => $spec));

        if (!$spec->isValid()) {
            return $spec->getInputFilter();
        }

        $result = $this->getDataSource()->create($spec->toArray());
        $spec->fromArray($result);

        $this->events()->trigger(__FUNCTION__ . '.post', $this, array('entity' => $spec));

        return $spec;
    }

    /**
     * Update an existing Entity
     *
     * Emits two signals:
     * - update.pre: executed after verification that the entity exists, and that
     *   we have an appropriate spec, but prior to validation and passing to the
     *   data source. Receives the $id, $spec, and resource object. $spec will 
     *   be an ArrayObject, allowing signal handlers to update the 
     *   specification.
     * - update.post: executed after succesful updating of the data source.
     *   Receives the updated Entity object.
     * 
     * @param  string $id 
     * @param  array|Entity $spec 
     * @return Entity|InputFilter
     * @throws DomainException|InvalidArgumentException
     */
    public function update($id, $spec)
    {
        // Does the entity exist?
        if (null === ($entity = $this->get($id))) {
            throw new DomainException(sprintf(
                'Entity with id "%s" does not exist',
                $id
            ));
        }

        // Do we have a specification we recognize?
        if ($spec instanceof $this->entityClass) {
            $spec = $spec->toArray();
        } elseif (!is_array($spec)) {
            throw new InvalidArgumentException(sprintf(
                'Expected an array or object of class %s; received "%s"',
                $this->entityClass,
                (is_object($spec) ? get_class($spec) : gettype($spec))
            ));
        }

        // Cast the specification to an ArrayObject to send to signal handlers
        $spec = new ArrayObject($spec);
        $this->events()->trigger(__FUNCTION__ . '.pre', $this, array('id' => $id, 'spec' => $spec));
        $spec = $spec->getArrayCopy();

        // Update the entity from the spec and see if validations pass
        $entity->fromArray($spec);
        if (!$entity->isValid()) {
            return $entity->getInputFilter();
        }

        // Update the data source, and populate the entity from the returned data
        $spec = $this->getDataSource()->update($id, $spec);
        $entity->fromArray($spec);

        // Emit signals
        $this->events()->trigger(__FUNCTION__ . '.post', $this, array('entity' => $entity));

        // Return the entity
        return $entity;
    }

    /**
     * Delete an entity
     *
     * Emits two signals:
     * - delete.pre: emitted after verifying the entity exists, but before 
     *   deletion from the data source. Receives the entity and the resource
     *   object as arguments. The first handler to emit a boolean return value
     *   will short-circuit deletion.
     * - delete.post: emitted after deletion of the entity from the data source.
     *   Receives the entity id.
     * 
     * @param  string|Entity $id 
     * @return bool
     */
    public function delete($id)
    {
        if ($id instanceof $this->entityClass) {
            $entity = $id;
            $id     = $entity->getId();
        } elseif (null === ($entity = $this->get($id))) {
            return false;
        }

        // Emit signals. If a handler returns a boolean value, return it.
        $response = $this->events()->triggerUntil('delete.pre', $this, array('entity' => $entity), function ($result) {
            return is_bool($result);
        });
        if ($response->stopped()) {
            return $response->last();
        }

        // Delete the item from the data source
        $this->getDataSource()->delete($id);

        // Emit post-deletion signals
        $this->events()->trigger('delete.post', $this, array('id' => $id, 'entity' => $entity));

        return true;
    }

    /**
     * Set data source object
     * 
     * @param  DataSource $dataSource 
     * @return AbstractResource
     */
    public function setDataSource(DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * Retrieve data source object
     * 
     * @return DataSource
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * Set collection class to utilize
     *
     * @param  string $class
     * @return AbstractResource
     */
    public function setCollectionClass($class)
    {
        $this->collectionClass = (string) $class;
        return $this;
    }
    
    /**
     * Get ollection class
     *
     * @return string
     */
    public function getCollectionClass()
    {
        return $this->collectionClass;
    }

    /**
     * Retrieve ACL resource identifier
     *
     * Defined by Zend\Acl\Resource
     * 
     * @return string
     */
    public function getResourceId()
    {
        return get_called_class();
    }
}

