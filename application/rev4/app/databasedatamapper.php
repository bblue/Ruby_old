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
		$sSelect	= implode(', ', $this->_acceptedFields);
		$sWhere		= $this->compileClauseStrings(array(
							$this->getClauseStrings($aCriterias),
							$this->compileClauseStrings($this->_cascadeFields)
					));

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
		$sSelect	= implode(', ', $this->_acceptedFields);
		$sWhere 	= $this->compileClauseStrings($this->_cascadeFields);
		
		$this->_adapter->select($aTables, $sWhere, $sSelect);
		
		$collection = $this->buildCollection();
		
		while($data = $this->_adapter->fetch())
		{
			$collection->add(null, $this->buildEntity($data));
		}
		
		return $collection;
	}

	/** Find all the entities that match the specified criteria */
	public function find($aCriterias = array(), $entity = null, $aInjectedClauses = array())
	{	
		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields, array_keys($aInjectedClauses));
		
		$aTables	= $this->extractTables($aTableSource);
		$sWhere		= $this->compileClauseStrings(array(
							$this->getClauseStrings($aCriterias),
							$this->getClauseStrings($aInjectedClauses, true),
							$this->compileClauseStrings($this->_cascadeFields)
					));
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
		$sWhere		= $this->compileClauseStrings(array(
							$this->getClauseStrings($aCriterias),
							$this->compileClauseStrings($this->_cascadeFields)
					));
		
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
		$sWhere		= $this->compileClauseStrings(array(
							$this->getClauseStrings($aCriterias),
							$this->compileClauseStrings($this->_cascadeFields)
					));
					
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
		//$array = explode('.', $sTableSource);
		//return $array[0];
		$pattern = "/([\w]+)\./";
		preg_match_all($pattern, $sTableSource, $matches);
		
		return $matches[0];
	}
	
	private function extractTables(array $aTablesSource)
	{
		$aTables = array();
		$pattern = "/([\w]+)\./";
		foreach($aTablesSource as $sTableSource)
		{
			preg_match_all($pattern, $sTableSource, $matches);
			
			foreach($matches[1] as $match)
			{
				$aTables[] = $match;
			}
		}
		return array_unique($aTables);
	}

	private function operatorIsValid($operator)
	{
		$aValidOperators = array('<', '>', '=<', '>=', '!=', '=');
		
		return in_array($operator, $aValidOperators);
	}
	
	private function compileClauseStrings(array $aClauseStrings)
	{	
		// Remove empty arrays
		$aClauseStrings = array_filter($aClauseStrings);
		
		// Compile all clauses into a single string for the database
		$sClauseString = implode(' AND ', $aClauseStrings);

		// Return the result array
		return $sClauseString;		
	}
	
	private function getClauseStrings($aClauses, $bIsInjected = false)
	{	
		if(empty($aClauses) || !is_array($aClauses))
		{
			return null;
		}
		
		$aClauseStrings = array();
		
		// Prepare the criterias
		foreach($aClauses as $sField => $aCriterias)
		{
			if(!is_array($aCriterias) || empty($aCriterias))
			{
				throw new \Exception('Error in ' . __METHOD__  . ': Error in parsing the criterias');
			}
			
			if(!$bIsInjected)
			{
				// Confirm the field can be handled by the datamapper 
				if(!isset($this->_acceptedFields[$sField]))
				{
					throw new \Exception('Error in ' . __METHOD__  . ': ' . $sField . ' is not accepted by this datamapper');
				}
				// Get the database field from fieldname
				$sField = $this->_acceptedFields[$sField];			
			}
			
			if($sCriteriaStrings = $this->compileCriteriaStrings($aCriterias, $sField))
			{
				$aClauseStrings[] = $sCriteriaStrings;
			}
		}

		if(empty($aClauseStrings))
		{
			throw new \Exception('Error in ' . __METHOD__  . ': can not return empty clause string');
		}
		
		$string = implode(' OR ', $aClauseStrings);

		return $string;
	}
	
	private function compileCriteriaStrings(array $aCriterias, $sDatabaseField)
	{
		$array = array();
		
		// Handle each criteria
		foreach($aCriterias as $aCriteria)
		{
			if(!is_array($aCriteria) || empty($aCriteria))
			{
				throw new \Exception('Error in ' . __METHOD__  . ': Error in parsing the criteria');
			}
			
			// Check the operator is valid
			if(!$this->operatorIsValid($aCriteria['operator']))
			{
				throw new \Exception('Error in ' . __METHOD__  . ': Criteria operator is invalid (' . $aCriteria['operator'] . ')');
			}

			// Compile the criteria string
			$array[] = $sDatabaseField . $aCriteria['operator'] . '"'. $aCriteria['value'] . '"';
		}
		// Compile all criterias into a single string
		$string = (empty($array)) ? null : '(' . implode(' OR ', $array) . ')';

		return $string;
	}
}