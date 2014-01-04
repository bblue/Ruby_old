<?php
namespace App\Entity;
use App\Proxy;

class Entity extends Proxy implements LoadableInterface
{
    protected $_entity;
   
    /**
     * Load an entity via the 'findById()' method of the injected mapper
     */
    public function load()
    {
        if ($this->_entity === null) {
            $this->_entity = $this->_mapper->findById($this->_aCriterias);
            if (!$this->_entity instanceof Model\AbstractEntity) {
                throw new \RunTimeException('Unable to load the specified entity.');
            }
        }
        return $this->_entity;
    }  
}