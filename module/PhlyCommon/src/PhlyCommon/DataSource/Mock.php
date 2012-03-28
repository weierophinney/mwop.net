<?php
namespace PhlyCommon\DataSource;

use PhlyCommon\DataSource,
    PhlyCommon\Query as QueryDefinition,
    DomainException;

class Mock implements DataSource
{
    protected $items;
    protected $queries = array();

    public function when(QueryDefinition $query, array $return)
    {
        $key = serialize($query->toArray());
        $this->queries[$key] = $return;
        return $this;
    }

    public function query(QueryDefinition $query)
    {
        $key = serialize($query->toArray());
        if (isset($this->queries[$key])) {
            return $this->queries[$key];
        }
        return array();
    }

    public function get($id)
    {
        if (!isset($this->items[$id])) {
            return null;
        }
        return $this->items[$id];
    }

    public function create(array $definition)
    {
        if (!isset($definition['id'])) {
            $definition['id'] = uniqid();
        }
        $id = $definition['id'];
        if (isset($this->items[$id])) {
            throw new DomainException(sprintf(
                'An item with id "%s" already exists; cannot create',
                $id
            ));
        }
        $this->items[$id] = $definition;
        return $this->items[$id];
    }

    public function update($id, array $fields)
    {
        if (!isset($this->items[$id])) {
            throw new DomainException(sprintf(
                'An item with id "%s" does not yet exist; cannot update',
                $id
            ));
        }
        $this->items[$id] = array_merge($this->items[$id], $fields);
        return $this->items[$id];
    }

    public function delete($id)
    {
        if (!isset($this->items[$id])) {
            return;
        }
        unset($this->items[$id]);
    }
}
