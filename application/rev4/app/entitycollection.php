<?php
namespace App;

abstract class EntityCollection implements CollectionInterface
{
    protected $_entities = array();

    /**
     * Constructor
     */
    public function __construct(array $entities = array())
    {
        $this->_entities = $entities;
        $this->reset();
    }

    /**
     * Get the entities stored in the collection
     */
    public function toArray()
    {
        return $this->_entities;
    }

    /**
     * Clear the collection
     */
    public function clear()
    {
        $this->_entities = array();
    }
    
    /**
     * Rewind the collection
     */    
    public function reset()
    {
        reset($this->_entities);
    }

    /**
     * Add an entity to the collection
     */
    public function add($key, AbstractEntity $entity) {
        return $this->offsetSet($key, $entity);
    }

    /**
     * Get from the collection the entity with the specified key
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Remove from the collection the entity with the specified key
     */
    public function remove($key)
    {
        return $this->offsetUnset($key);
    }

    /**
     * Check if the entity with the specfied key exists in the collection
     */
    public function exists($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Count the number of entities in the collection
     */
    public function count()
    {
        return count($this->_entities);
    }

    /**
     * Get the external array iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * Add an entity to the collection
     */
    public function offsetSet($key, $entity)
    {
        if (!$entity instanceof AbstractEntity) {
            throw new \InvalidArgumentException('To add an entity to the collection, it must be an instance of AbstractEntity.');
        }
        if (!isset($key)) {
            $this->_entities[] = $entity;
        }
        else {
            $this->_entities[$key] = $entity;
        }
        return true;
    }
   
    /**
     * Remove an entity from the collection
     */
    public function offsetUnset($key)
    {
        if ($key instanceof \Model\AbstractEntity) {
            $this->_entities = array_filter($this->_entities, function ($v) use ($key) {
                return $v !== $key;
            });
            return true;
        }
        if (isset($this->_entities[$key])) {
            unset($this->_entities[$key]);
            return true;
        }
        return false;
    }
   
    /**
     * Get the specified entity in the collection
     */
    public function offsetGet($key)
    {
        return isset($this->_entities[$key])
            ? $this->_entities[$key] : null;
    } 
   
    /**
     * Check if the specified entity exists in the collection
     */    
    public function offsetExists($key)
    {
        return isset($this->_entities[$key]);
    }
}