<?php
namespace Model\Mappers;

use App\Factories\Entity;

use App\Factories\Collection;

use
	App\Boot\SessionHandler,
	App\AbstractDataMapper,
	App\AbstractEntity,
	App\CollectionFactory,
	App\EntityFactory,
	Model\Domain\Visitor\Visitor,
	Model\Domain\Model\Model,
	Model\Domain\Recipe\Recipe;
;

final class SessionMapper extends AbstractDataMapper
{
	public function  __construct(SessionHandler $session, Collection $collectionFactory, Entity $entityFactory)
	{
		$this->_adapter = $session;
		$this->_entityFactory = $entityFactory;
		$this->_collectionFactory = $collectionFactory;
	}

	public function fetch(AbstractEntity $entity)
	{
		if($entity instanceof Visitor){
			$entity->id = $this->_adapter->getSessionID();
			return $entity;
		}

		if($entity instanceof Recipe){
			$aRecipeValues = is_array($this->_adapter->getVar('aRecipeValues')) ? $this->_adapter->getVar('aRecipeValues') : array();
			foreach($aRecipeValues as $key => $value) {
				$entity->$key = $value;
			}
			return $entity;
		}

		throw new \Exception('Entity (' . (is_object($entity) ? get_class($entity) : $entity) . ') cannot be mapped by this datamapper (' . __CLASS__ . ')');
	}

	public function store(AbstractEntity $entity)
	{
		throw new \Exception('Unsupported');
	}
}