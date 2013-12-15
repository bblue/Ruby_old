<?php
namespace App;

use 
	Lib\Db\DatabaseAdapterInterface;
use 
	App\Factories\Collection,
	App\Factories\Entity, 
	App\Factories\DataMapper
;

abstract class DatabaseDataMapper extends AbstractDataMapper implements DataMapperInterface
{	
	/** Constructor */
	public function  __construct(DatabaseAdapterInterface $db, Collection $collectionFactory, Entity $entityFactory, DataMapper $dataMapperFactory)
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
		
		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);
		
		$aTables	= $this->extractTables($aTableSource);
		$sWhere 	= $this->prepareCriterias($aCriterias);
		$sSelect	= implode(', ', $this->_acceptedFields);
				
		$this->_adapter->select($aTables, $sWhere, $sSelect);
		
		$data = $this->_adapter->fetch() ? : array();
		
		if(is_object($entity) || $data)
		{
			if(!$this->isValidEntity($entity))
			{
				throw new \Exception('The specified entity ('. get_class($entity) .') is not allowed for this mapper ('. get_called_class() .')');
			}
			return $this->buildEntity($data, $entity);
		}
		return null;
	}

	/** Find all the entities */
	public function findAll()
	{
		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);
		
		$aTables	= $this->extractTables($aTableSource);
		$sWhere 	= implode(', ', $this->_cascadeFields);
		$sSelect	= implode(', ', $this->_acceptedFields);
		
		$this->_adapter->select($aTables, $sWhere, $sSelect);
		
		$collection = $this->buildCollection();
		
		while($data = $this->_adapter->fetch())
		{
			$collection->add(null, $this->buildEntity($data));
		}
		
		return $collection;
	}

	/** Find all the entities that match the specified criteria */
	public function find(array $aCriterias, $entity = null, $aInjectedClauses = array())
	{	
		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields, $aInjectedClauses);
		
		$aTables	= $this->extractTables($aTableSource);
		$sWhere		= $this->prepareCriterias($aCriterias, $aInjectedClauses);
		$sSelect	= implode(', ', $this->_acceptedFields);	
		
		$this->_adapter->select($aTables, $sWhere, $sSelect);
		
		$collection = $this->buildCollection();
		
		while($data = $this->_adapter->fetch())
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
		if(!$this->isValidEntity($entity))
		{
			throw new \Exception('The specified entity ('. get_class($entity) .') is not allowed for this mapper ('. get_called_class() .')');
		}
		
		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);
		
		$aTables	= $this->extractTables($aTableSource);
		$aData		= $this->filterOnlyAcceptedFields($entity->toArray());
		
		$insertId = $this->_adapter->insert($aTables, $aData);
		
		if($insertId)
		{
			$entity->id = $insertId;
		}
		
		if($this->_adapter->getAffectedRows() > 0)
		{
			return $entity;
		} else {
			throw new \Exception('Insertion to database for entity ('. get_class($entity) . ') failed');
		}
	}

	/** Update the row in the table corresponding to the specified entity */
	public function update(AbstractEntity $entity)
	{
		if(!$this->isValidEntity($entity))
		{
			throw new \Exception('The specified entity ('. get_class($entity) .') is not allowed for this mapper ('. get_called_class() .')');
		}
		
		$aData = $this->filterOnlyAcceptedFields($entity->toArray());
		unset($aData['id']);
		
		if(empty($aData))
		{
			throw new \Exception('No fields to update. Data array is empty.');
		}
		
		$aCriterias['id'][] = array(
			'operator'	=> '=',
			'value'		=> $entity->id
		);
		
		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);
		
		$aTables	= $this->extractTables($aTableSource);
		$aData		= $this->filterOnlyAcceptedFields($entity->toArray());		
		$sWhere		= $this->prepareCriterias($aCriterias);
		
		return $this->_adapter->update($aTables, $aData, $sWhere);
	}
 
	/** Delete the row in the table corresponding to the specified entity or ID */
	public function delete(AbstractEntity $entity)
	{
		if(!$this->isValidEntity($entity))
		{
			throw new \Exception('The specified entity is not allowed for this mapper. (' . get_class($entity) . ')');
		}
		
		$aCriterias['id'][] = array(
			'operator'	=> '=',
			'value'		=> $entity->id
		);
		
		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);
		
		$aTables	= $this->extractTables($aTableSource);	
		$sWhere		= $this->prepareCriterias($aCriterias);
					
		return $this->_adapter->delete($aTables, $sWhere);
	}
	
	public function store(AbstractEntity $entity)
	{
		if($this->update($entity)){
			return $entity;
		}

		// Make sure we cannot find this specific entity before inserting
		if($this->findById($entity->id, $entity))
		{
			if($this->_adapter->getAffectedRows())
			{
				return $entity;
			}
		}
		return $this->insert($entity);
	}	
	
	### Internal functions ###
	
	private function extractTable($sTableSource)
	{
		$array = explode('.', $sTableSource);
		return $array[0];
	}
	
	private function extractTables($aTablesSource)
	{
		$aTables = array();
		foreach($aTablesSource as $sTableSource)
		{
			$aTables[] = $this->extractTable($sTableSource);
		}
		return array_unique($aTables);
	}
	
	private function prepareCriterias(array $aCriterias, $aInjectedClauses = array())
	{
		if(empty($aCriterias))
		{
			return null;
		}
		
		$aPreparedCriterias = array();
		
		// Prepare any potential injected clauses into the criterias
		if(!empty($aInjectedClauses))
		{
			array_merge($aPreparedCriterias, $aInjectedClauses);
		}
		
		// Prepare any potential cascaded data sections
		if(!empty($this->_cascadeFields))
		{
			array_merge($aPreparedCriterias, $this->_cascadeFields);
		}		
		
		// Prepare the criterias
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

		// Merge the injected clauses, the criteria and the cascaded fields
		if(!empty($aPreparedCriterias))
		{
			return implode(' AND ', $aPreparedCriterias);
		}
	}
}