<?php
namespace Model\Mappers;
use
	Model\Domain\Route\Route,
	App\DatabaseDataMapper,
	App\AbstractEntity,
	App\CollectionProxy;

final class RouteControllerMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'Route';

	protected $_acceptedFields = array(
		'bIsEnabled'				=> 'areas.bIsEnabled',
		'id'						=> 'areas.a_id',
		'sResourceName'				=> 'areas.name',
		'bCanBypassForcedLogin' 	=> 'commands.bCanBypassForcedLogin',
		'sCommand'                	=> 'commands.sName'
	);
	protected $_cascadeFields = array('commands.a_id = areas.a_id');

    public function fetch(AbstractEntity $route)
    {
    	$this->injectedEntity = $route;
    	// Check if ID has been set
    	if(isset($route->id)) {
    		$this->findById($route->id, $route);
    	} elseif(!empty($route->sResourceName)) {
    		$this->addFilter('sResourceName', $route->sResourceName);
    		$this->addFilter('sCommand', $route->getCommand());
    		$this->findSingleEntity($this->getFilters(), $route);
    	}
    	return $route;
    }
}