<?php
namespace Model\Mappers;
use 
	Model\Domain\User\User,
	App\DatabaseDataMapper,
	App\AbstractEntity,
	App\CollectionProxy;

final class RouteMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'User';
	protected $_acceptedFields = array(

	);
	protected $_cascadeField = '';
    
    public function fetch(AbstractEntity $route)
    {
    	
    }
    
    protected function setEntitySpecificData(AbstractEntity $route)
    {
    	//@todo: Denne er halvferdig og kun basert på en copy-paste fra userentity
		$user->users = new CollectionProxy($this->_dataMapperFactory->build('user'), array('route_id' => array(array('operator' => '=', 'value' => $route->id))));
		$user->usergroups = new CollectionProxy($this->_dataMapperFactory->build('usergroup'), array('route_id' => array(array('operator' => '=', 'value' => $route->id))));
    }
}