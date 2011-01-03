<?php
namespace mwop\Resource;

use mwop\Stdlib\Resource,
    mwop\Stdlib\DataSource,
    mwop\DataSource\Query,
    ArrayObject,
    DomainException,
    InvalidArgumentException,
    Zend\Acl\Resource as AclResource,
    Zend\SignalSlot\Signals;

abstract class AbstractResource implements Resource, AclResource
{
    protected $entityClass;
    protected $dataSource;
    protected static $signals;

    /**
     * Signal manager for entity resource
     *
     * Allows injecting a signal handler, or retrieving the signal handler for
     * the purpose of connecting handlers.
     * 
     * @param  null|Signals $signals 
     * @return Signals
     */
    public static function signals(Signals $signals = null)
    {
        if (null !== $signals) {
            static::$signals = $signals;
        } elseif (null === static::$signals) {
            static::$signals = new Signals();
        }
        return static::$signals;
    }

    /**
     * Reset signals
     *
     * Clears all signal handlers
     * 
     * @return void
     */
    public static function resetSignals()
    {
        static::$signals = null;
    }

    /**
     * Get all entries
     *
     * Emits two signals:
     * - get-all.pre: Receives instance of resource as sole argument. If a 
     *   signal returns a collection, the method returns it immediately.
     * - get-all.post: Receives two arguments, the items as a Collection object, 
     *   and the resource.
     * 
     * @return Collection
     */
    public function getAll()
    {
        $results = static::signals()->emitUntil(function($result) {
            return ($result instanceof Collection);
        }, 'get-all.pre', $this);
        $collection = $results->last();
        if ($collection instanceof Collection) {
            return $collection;
        }

        $items = $this->getDataSource()->query(new Query());
        if (empty($items)) {
            $items = array();
        }

        $items =  new Collection($items, $this->entityClass);

        static::signals()->emit('get-all.post', $items, $this);
        return $items;
    }

    /**
     * Get a single Entity by ID
     *
     * Emits two signals:
     * - get.pre: Receives two arguments, the id and the current resource 
     *   instance. If a signal returns an Entity object, the method returns it 
     *   immediately.
     * - get-all.post: Receives two arguments, the retrieved Entity, and the 
     *   resource.
     * 
     * @param  string|int $id
     * @return null|Entity
     */
    public function get($id)
    {
        $entityClass = $this->entityClass;
        $results = static::signals()->emitUntil(function($result) use ($entityClass) {
            return ($result instanceof $entityClass);
        }, 'get.pre', $id, $this);
        $entity = $results->last();
        if ($entity instanceof $entityClass) {
            return $entity;
        }

        $data = $this->getDataSource()->get($id);
        if (empty($data)) {
            return null;
        }
        $entity = new $entityClass();
        $entity->fromArray($data);

        static::signals()->emit('get.post', $entity, $this);

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

        static::signals()->emit('create.pre', $spec, $this);

        if (!$spec->isValid()) {
            return $spec->getInputFilter();
        }

        $result = $this->getDataSource()->create($spec->toArray());
        $spec->fromArray($result);

        static::signals()->emit('create.post', $spec);

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
        static::signals()->emit('update.pre', $id, $spec, $this);
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
        static::signals()->emit('update.post', $entity);

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
        $response = static::signals()->emitUntil(function ($result) {
            return is_bool($result);
        }, 'delete.pre', $entity, $this);
        $last = $response->last();
        if (is_bool($last)) {
            return $last;
        }

        // Delete the item from the data source
        $this->getDataSource()->delete($id);

        // Emit post-deletion signals
        static::signals()->emit('delete.post', $id);

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

