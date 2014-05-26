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

	private $aFilters= array();
	private $aFilterMap = array();

	/** Constructor */
	public function  __construct(DatabaseAdapterInterface $db, Collection $collectionFactory, Entity $entityFactory, DataMapper $dataMapperFactory)
	{
		$this->_adapter = $db;
		$this->_entityFactory = $entityFactory;
		$this->_collectionFactory = $collectionFactory;
		$this->_dataMapperFactory = $dataMapperFactory;

		$this->setupFilterArray();
	}

	/** Find all the entities that match the specified criteria */
	public function count($aFilters = array())
	{
		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);

		$sSelect	= implode(', ', $this->_acceptedFields); //@todo: Denne burde kun velge id
		$aTables	= $this->extractTables($aTableSource);
		$sWhere		= $this->buildCriteriaString($aFilters);

		return $this->_adapter->countTableRows($aTables, $sWhere);
	}

	/** Count number of rows */
	public function countAll()
	{
		return $this->count();
	}

	/** Find all the entities that match the specified filters */
	public function find($aFilters = array(), $order = '', $limit = null, $offset = null)
	{
		$collection = $this->buildCollection();

		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);

		$sSelect	= implode(', ', $this->_acceptedFields);
		$aTables	= $this->extractTables($aTableSource);
		$sWhere		= $this->buildCriteriaString($aFilters);

		if($this->_adapter->select($aTables, $sWhere, $sSelect, $order, $limit, $offset)) {
			while($data = $this->_adapter->fetch()) {
				$entity = $this->buildEntity($data);
				$collection->add($entity->id, $entity);
			}
		}

		return $collection;
	}

	/** Find all the entities */
	public function findAll()
	{
		return $this->find();
	}

	/** Find an entity by its ID */
	public function findById($id, $entity = null)
	{
		$this->addFilter('id', $id);
		return $this->findSingleEntity($this->getFilters(), $entity);
	}

	public function findSingleEntity($aFilters = array(), $entity = null)
	{
		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);

		$sSelect	= implode(', ', $this->_acceptedFields);
		$aTables	= $this->extractTables($aTableSource);
		$sWhere		= $this->buildCriteriaString($aFilters);

		$data = ($this->_adapter->select($aTables, $sWhere, $sSelect)) ? $this->_adapter->fetch() : null;

		return $this->buildEntity($data, $entity);
	}

	/** Search for entities that mach the string */
	public function match($aMatch, $sAgainst = '*', $aFilters = array(), $order = '', $limit = null, $offset = null)
	{
		if(!$aMatch) {
			throw new \Exception('Match method requires some match fields to be selected');
		}
		$collection = $this->buildCollection();

		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);

		$sSelect	= implode(', ', $this->_acceptedFields);
		$aTables	= $this->extractTables($aTableSource);
		$sWhere		= $this->buildCriteriaString($aFilters);
		$sMatch		= implode(', ', $aMatch);
		$sAgainst	= $sAgainst;

		if($this->_adapter->match($sMatch, $sAgainst, $aTables, $sWhere, $sSelect, $order, $limit, $offset)) {
			while($data = $this->_adapter->fetch()) {
				$entity = $this->buildEntity($data);
				$collection->add($entity->id, $entity);
			}
		}

		return $collection;
	}


	/** Insert a new row in the table corresponding to the specified entity */
	public function insert(AbstractEntity $entity)
	{
		if(!$this->isValidEntity($entity)) {
			throw new \Exception('The specified entity ('. get_class($entity) .') is not allowed for this mapper ('. get_called_class() .')');
		}

		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);

		$aTables	= $this->extractTables($aTableSource);
		$aData		= $this->filterOnlyAcceptedFields($entity->toArray());

		$insertId = $this->_adapter->insert($aTables, $aData);

		if($insertId) {
			$entity->id = $insertId;
		}

		if($this->_adapter->getAffectedRows() > 0) {
			return $entity;
		} else {
			throw new \Exception('Insertion to database for entity ('. get_class($entity) . ') failed');
		}
	}

	/** Update the row in the table corresponding to the specified entity */
	public function update(AbstractEntity $entity)
	{
		if(!$this->isValidEntity($entity)) {
			throw new \Exception('The specified entity ('. get_class($entity) .') is not allowed for this mapper ('. get_called_class() .')');
		}

		if(empty($entity->id)) {
			throw new \Exception('Can not update entity with no ID');
		}

		$this->addFilter('id', $entity->id);

		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);

		$aTables	= $this->extractTables($aTableSource);
		$aData		= $this->filterOnlyAcceptedFields($entity->toArray());
					  unset($aData['id']);

					  if(empty($aData)) {
					  	throw new \Exception('No fields to update. Data array is empty.');
					  }

		$sWhere		= $this->buildCriteriaString($this->getFilters());

		return $this->_adapter->update($aTables, $aData, $sWhere);
	}

	/** Delete the row in the table corresponding to the specified entity or ID */
	public function delete(AbstractEntity $entity)
	{
		if(!$this->isValidEntity($entity)) {
			throw new \Exception('The specified entity is not allowed for this mapper. (' . get_class($entity) . ')');
		}

		$this->addFilter('id', $entity->id);

		$aTableSource = array_merge($this->_acceptedFields, $this->_cascadeFields);

		$aTables	= $this->extractTables($aTableSource);
		$sWhere		= $this->buildCriteriaString($this->getFilters());

		return $this->_adapter->delete($aTables, $sWhere);
	}

	public function store(AbstractEntity $entity)
	{
		if($this->update($entity)){
			return $entity;
		}

		// Make sure we can not find this specific entity before inserting
		$this->addFilter('id', $entity->id);
		if($this->count($this->getFilters())) {
			return $entity;
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
		foreach($aTablesSource as $sTableSource) {
			preg_match_all($pattern, $sTableSource, $matches);

			foreach($matches[1] as $match) {
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

	private function filterlinkOperatorIsValid($operator)
	{
		$aValidOperators = array('AND', 'OR');

		return in_array(strtoupper($operator), $aValidOperators);
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


	public function addFilter($sField, $value, $iFilterLink = 0, $sOperator = '=', $sFilterLinkOperator = 'AND')
	{
		$this->aFilters[] = array(
			'field' 	=> $sField,
			'operator'	=> $sOperator,
			'value'		=> $value,
			'filterlink'=> $iFilterLink,
			'filteroperator'=>$sFilterLinkOperator,
			'db_field'	=> null,
		);
		end($this->aFilters);
		$id = key($this->aFilters);

		return $id;
	}

	private function sanitizeFilters($aFilters)
	{
		foreach($aFilters as $iFilterId => $aFilter) {
			if($iFilterId === 0) {
				continue;
			}

			// Check the operator is valid
			if(!$this->operatorIsValid($aFilter['operator'])) {
				throw new \Exception('Error in ' . __METHOD__  . ': Filter operator is invalid (' . $aFilter['operator'] . ')');
			}

			// Check the filterlink operator is valid
			if(!$this->filterlinkOperatorIsValid($aFilter['filteroperator'])) {
				throw new \Exception('Error in ' . __METHOD__  . ': Filterlink operator is invalid (' . $aFilter['filteroperator'] . ')');
			}

			// Confirm the filter can be handled by mapper
			if(!isset($this->_acceptedFields[$aFilter['field']])) {
				throw new \Exception('Error in ' . __METHOD__  . ': ' . $aFilter['field'] . ' is not accepted by this datamapper');
			}

			// Get the database field from fieldname
			$aFilters[$iFilterId]['db_field'] = $this->_acceptedFields[$aFilter['field']];

			// Make the query safe
			$aFilter['value'] = $this->_adapter->quoteValue($aFilter['value']);
		}
		return $aFilters;
	}

	public function getFilters()
	{
		$aFilters = $this->aFilters;
		$this->resetFilters();
		return $aFilters;
	}

	public function resetFilters()
	{
		$this->aFilters = array();
		$this->aFilterMap = array();
		$this->setupFilterArray();
	}

	private function setupFilterArray()
	{
		$this->aFilters[] = array(
			'field' 	=> '',
			'operator'	=> '',
			'value'		=> '',
			'filterlink'=> false,
			'filteroperator'=>'',
			'db_field'	=> null,
		);
	}

	private function buildCriteriaString($aFilters = array())
	{
		// Ensure the filter array is valid
		$aFilters = $this->sanitizeFilters($aFilters);

		$str = '';

		// Build the string from filter array
		if(!empty($aFilters)) {
			$aFilterMap = $this->createFilterMap($aFilters);
			$str = $this->buildCriteriaStringSection($aFilters, $aFilterMap);
		}

		// Add cascade fields at the end of the array, and check if we need to merge with 'AND'
		if(!empty($this->_cascadeFields)) {
			$str .= empty($str) ? '' :' AND ';
			$str .= '(' . implode(' AND ', $this->_cascadeFields) . ')';
		}

		// Clean the start of string for operators
		$str = ltrim($str, 'AND= ');

		return $str;
	}

	private function buildCriteriaStringSection($aFilters, $aFilterMap, $iFilterID = 0)
	{
		$bChildren = isset($aFilterMap[$iFilterID]);
		$str = $aFilters[$iFilterID]['filteroperator'];
		$str .= (($bChildren && $iFilterID != 0) ? '(' : '');
		if($iFilterID != 0) {
			$str .= ' '. $aFilters[$iFilterID]['db_field'] . $aFilters[$iFilterID]['operator'] . (is_int($aFilters[$iFilterID]['value']) ? $aFilters[$iFilterID]['value'] : '"'.$aFilters[$iFilterID]['value'].'"').' ';
		}
		if($bChildren) {
			foreach($aFilterMap[$iFilterID] as $key => $iChildFilterId) {
				$str .= $this->buildCriteriaStringSection($aFilters, $aFilterMap, $iChildFilterId);
			}
		}
		return $str .= ($bChildren && $iFilterID != 0 ? ')' : '');
	}

	private function createFilterMap($aFilters = array())
	{
		$aFilterMap = array();
		foreach($aFilters as $iFilterId => $aFilter) {
			if($iFilterId > 0) {
				$aFilterMap[$aFilter['filterlink']][] = $iFilterId;
			}
		}
		return $aFilterMap;
	}
}