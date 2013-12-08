<?php
namespace Model;

use 
	Lib\Boot\Db\DatabaseAdapterInterface;

abstract class DatabaseDataMapper extends AbstractDataMapper implements DataMapperInterface
{
	protected $_dbTables = array();
	
	private $sDefaultNamespace = '';
	
    /** Constructor */
    public function  __construct(DatabaseAdapterInterface $db, CollectionFactory $collectionFactory, EntityFactory $entityFactory, DataMapperFactory $dataMapperFactory)
    {
        $this->_adapter = $db;
        $this->_entityFactory = $entityFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_dataMapperFactory = $dataMapperFactory;
    }
    
    /** Find an entity by its ID */
    public function findById($id, $entity = null)
    {
        $aCriterias['id'][] = array(
        	'operator'	=> '=',
        	'value'		=> $id
        );
        $this->_adapter->select($this->getTables(), $this->prepareCriterias($aCriterias), implode(', ', $this->_acceptedFields));
        if ($data = $this->_adapter->fetch())
        {
        	if(is_object($entity))
        	{
        		if(!$this->isValidEntity($entity))
        		{
        			throw new \Exception('The specified entity ('. get_class($entity) .') is not allowed for this mapper ('. get_called_class() .')');
        		}
        	}
        	return $this->buildEntity($data, $entity);
        }
        return false;
    }

    /** Find all the entities */
    public function findAll()
    {
        $this->_adapter->select($this->getTables(), $this->_cascadeField, implode(', ', $this->_acceptedFields));
        $collection = $this->buildCollection();
        while ($data = $this->_adapter->fetch())
        {
            $collection->add(null, $this->buildEntity($data));
        }
        return $collection;
    }

    /** Find all the entities that match the specified criteria */
    public function find(array $aCriterias, $entity = null)
    {
        $this->_adapter->select($this->getTables(), $this->prepareCriterias($aCriterias), implode(', ', $this->_acceptedFields));
        $collection = $this->buildCollection();
        while ($data = $this->_adapter->fetch())
        {
        	if($entity instanceof AbstractEntity)
        	{
        		return $this->buildEntity($data, $entity);
        	}
        	$collection->add(null, $this->buildEntity($data));
        }
        return $collection;
    }

    /** Insert a new row in the table corresponding to the specified entity */
    public function insert(AbstractEntity $entity)
    {
        if ($this->isValidEntity($entity))
        {
        	$insertId = $this->_adapter->insert($this->getTables(), $this->filterData($entity->toArray()));
			
        	if($insertId)
			{
				$entity->id = $insertId;
			}
            if($this->_adapter->getAffectedRows() > 0)
            {
            	return $entity;
            }
			throw new \Exception('Insertion to database for entity ('. get_class($entity) . ') failed');
        }
        throw new \Exception('The specified entity ('. get_class($entity) .') is not allowed for this mapper ('. get_called_class() .')');
    }

    /** Update the row in the table corresponding to the specified entity */
    public function update(AbstractEntity $entity)
    {
        if ($this->isValidEntity($entity))
        {
            $data = $this->filterData($entity->toArray());
            unset($data['id']);
			if(empty($data))
			{
				throw new \Exception('No fields to update. Data array is empty.');
			}
	        $aCriterias['id'][] = array(
	        	'operator'	=> '=',
	        	'value'		=> $entity->id
	        );
	        
            return $this->_adapter->update($this->getTables(), $data, $this->prepareCriterias($aCriterias));
        }
        throw new \Exception('The specified entity ('. get_class($entity) .') is not allowed for this mapper ('. get_called_class() .')');
    }
 
    /** Delete the row in the table corresponding to the specified entity or ID */
    public function delete(AbstractEntity $entity)
    {
		if ($this->isValidEntity($entity))
        {
	        $aCriterias['id'][] = array(
	        	'operator'	=> '=',
	        	'value'		=> $entity->id
	        );
	        return $this->_adapter->delete($this->getTables(), $this->prepareCriterias($aCriterias));
        }
        throw new \Exception('The specified entity is not allowed for this mapper. (' . get_class($entity) . ')');
    }
    private function getTables()
    {
    	return (!empty($this->_dbTables)) ? $this->_dbTables : $this->extractTables();
    }
    
    private function extractTables()
    {
    	$aTables = array();
    	foreach($this->_acceptedFields as $entityField => $dbField)
    	{
    		$array = explode('.', $dbField);
    		$aTables[] = $array[0];
    	}
    	return array_unique($aTables);
    }
    
    private function prepareCriterias(array $aCriterias)
    {
    	if(empty($aCriterias))
    	{
    		return null;
    	}
    	
    	(!empty($this->_cascadeField)) ? $aPreparedCriterias[] = $this->_cascadeField : null;

    	foreach($aCriterias as $field => $criterias)
    	{
    		if(isset($this->_acceptedFields[$field]))
    		{
    			foreach($criterias as $criteria)
    			{
    				$array[] = $this->_acceptedFields[$field] . $criteria['operator'] . '"'. $criteria['value'] . '"';
    			}
    			$aPreparedCriterias[] = '(' . implode(' OR ', $array) . ')';
    		}
    	}

    	return (!empty($aPreparedCriterias)) ? implode(' AND ', $aPreparedCriterias) : null;   	
    }
    
    public function store(AbstractEntity $entity)
    {
    	if($this->update($entity)){
    		return $entity;
    	}
    	
    	// Make sure we cannot find this specific entity before inserting
    	if($this->findById($entity->id, $entity))
    	{
    		return $entity;
    	}
    	return $this->insert($entity);

    }
    
}