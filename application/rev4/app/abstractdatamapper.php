<?php
namespace Model;

use Lib\Boot\Db\DatabaseAdapterInterface;

abstract class AbstractDataMapper
{
    protected $_adapter;
    protected $_collection;
    protected $_entityClass;
    protected $_entityFactory;
    protected $_collectionFactory;
    protected $_dataMapperFactory;
    protected $_acceptedFields = array();

    /** Get the collection */
    protected function buildCollection()
    {
    	return $this->_collectionFactory->build($this->_entityClass);
    }

    protected function isValidEntity($entity)
    {
    	if($entity instanceof AbstractEntity)
    	{
	 		$class = explode('\\', get_class($entity));
	 		$name = strtolower(end($class));
	    	if($name == strtolower($this->_entityClass))
	    	{
	    		return true;
	    	}		
    	}
		return false;
    }
    
    protected function buildEntity(array $data, $entity = null)
    {
    	if(!$entity instanceof AbstractEntity)
    	{
			$entity = $this->_entityFactory->build($this->_entityClass);
		}
		$this->setEntityData($entity, $data);
		
		return $entity;
    }
    
    protected function setEntitySpecificData(AbstractEntity $entity){}
    
    protected function setEntityData(AbstractEntity $entity, array $data = array())
    {
		foreach($data as $key => $value)
		{
			$entity->$key = $value;
		}
	
		if(!isset($data['id']))
		{	
			throw new \Exception('Failed to set id for entity ' . $this->_entityClass);
		}

		$this->setEntitySpecificData($entity);
		
		return $entity;
    }
    
    protected function filterData($data)
    {
		foreach($data as $key => $value)
		{
			if(!array_key_exists($key, $this->_acceptedFields))
			{
				unset($data[$key]);
			}
		}
		return $data;
    }
    
    public function fetch(AbstractEntity $entity)
    {
    	throw new \Exception(get_class($entity) . ' is not a valid entity to be mapped by ' . get_called_class());
    }
    
    public function store(AbstractEntity $entity)
    {
    	throw new \Exception(get_class($entity) . ' is not a valid entity to be stored by ' . get_called_class());
    }
}