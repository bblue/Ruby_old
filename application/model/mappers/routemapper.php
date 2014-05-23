<?php
namespace Model\Mappers;
use
	Model\Domain\Route\Route,
	App\DatabaseDataMapper,
	App\AbstractEntity,
	App\CollectionProxy;

final class RouteMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'Route';

	protected $_acceptedFields = array(
		'id'			=> 'routes.id',
		'url'			=> 'routes.url',
		'sResourceName'	=> 'routes.sResourceName',
		'bIsEnabled'	=> 'areas.bIsEnabled',
		'sCommand'		=> 'routes.sCommand',
		'a_id'			=> 'areas.a_id',
		'bCanBypassForcedLogin' => 'commands.bCanBypassForcedLogin',
	);
	protected $_cascadeFields = array(
		'routes.sResourceName = areas.name',
		'commands.sName = routes.sCommand',
		'commands.a_id = areas.a_id'
	);

    public function fetch(AbstractEntity $route)
    {
    	// Check if ID has been set
    	if(isset($route->id)) {
    		$this->findById($route->id, $route);
    	} elseif(isset($route->url)) {
    		$this->addFilter('url', $route->sResourceName .'/'. $route->getCommand());
			$this->findSingleEntity($this->getFilters(), $route);
    	}

    	return $route;
    }
}