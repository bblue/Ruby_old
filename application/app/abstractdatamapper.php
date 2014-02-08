<?php
namespace App;

use App\Entity\Collection;

abstract class AbstractDataMapper
{
    protected $_adapter;
    protected $_collection;
    protected $_entityClass;
    protected $_entityFactory;
    protected $_collectionFactory;
    protected $_dataMapperFactory;

    /** Get the collection */
    protected function buildCollection()
    {
    	return $this->_collectionFactory->build($this->_entityClass);
    }

    protected function isValidEntity($entity)
    {
    	if(!$entity instanceof AbstractEntity)
    	{
    		return false;
    	}
 		
    	$class = explode('\\', get_class($entity));
 		$name = strtolower(end($class));
    	
 		if($name == strtolower($this->_entityClass))
    	{
    		return true;
    	}
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
        foreach($this->_acceptedFields as $entityKey => $dbFieldName)
    	{
    		$arr = explode('.', $dbFieldName);
    		$dbKey = $arr[1];
    		$aConvertedKeys[$dbKey] = $entityKey;
    	}
    	
		foreach($data as $dbKey => $value)
		{
			$entity->$aConvertedKeys[$dbKey] = $value;
		}
	
		if(!isset($entity->id))
		{	
			return $entity;
			//throw new \Exception('Failed to set id for entity ' . $this->_entityClass . ' with mapper ' . get_called_class());
		}

		$this->setEntitySpecificData($entity);
		
		return $entity;
    }
    
    protected function filterOnlyAcceptedFields($data)
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