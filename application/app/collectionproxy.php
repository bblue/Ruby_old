<?php
namespace App;
use App\Entity\Collection as EntityCollection;

final class CollectionProxy implements LoadableInterface, \Countable, \IteratorAggregate
{
	protected $_mapper;
	protected $_aCriterias;
	protected $_aInjectedClauses;

	protected $_collection;

	/**
	 * Constructor
	 */
	public function __construct(AbstractDataMapper $mapper, $aCriterias, $aInjectedClauses = array())
	{
		$this->_mapper = $mapper;
		$this->_aCriterias = $aCriterias;
		$this->_aInjectedClauses = $aInjectedClauses;
	}

	/**
	 * Load explicitly a collection of entities via the 'find()' method of the injected mapper
	 */
	public function load()
	{
		if($this->_collection === null)
		{
			$this->_collection = $this->_mapper->find($this->_aCriterias, null, $this->_aInjectedClauses);

			if (!$this->_collection instanceof EntityCollection) {
				throw new \Exception('Unable to load the specified collection.');
			}
		}
		return $this->_collection;
	}

	/**
	 * Count the entities in the collection after lazy-loading them
	 */
	public function count()
	{
		return count($this->load());
	}

	/**
	 * Load a collection of entities via the 'find()' method of the injected mapper
	 * when called within a 'foreach' construct
	 */
	public function getIterator()
	{
		return $this->load();
	}

	 /**
	 * Used when expecting only to load a collection with a single entity via the 'find()' method of the injected mapper
	 * when called like $entity1->entity2->entity2variable
	 */
	public function getEntity()
	{
		$collection = $this->load();

		$count = $collection->count();

		if($count === 1) {
			return $collection->get(0);
		}

		if($count > 1) {
			throw new \Exception('EntityCollection contains more than 1 entity (' . $count . ')');
		}

		if($count === 0) {
			throw new \Exception('EntityCollection contains 0 entites');
		}
	}
}