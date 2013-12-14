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
		'bIsEnabled'	=> 'routes.bIsEnabled',
		'sCommand'		=> 'routes.sCommand'
	);	
	protected $_cascadeField = '';
    
    public function fetch(AbstractEntity $route)
    {
    	// Check if ID has been set
    	if(isset($route->id))
    	{
    		$this->findById($route->id, $route);
    	}
    	elseif(isset($route->url))
    	{
	    	$aCriterias['url'] = array(
	    		array(
	    			'operator' 	=> '=',
	    			'value' 	=> $route->url
	    		)
	    	);
	
	    	$this->find($aCriterias, $route);
	    	
    	}
    	return $route;
    }
    
    protected function setEntitySpecificData(AbstractEntity $route)
    {
    	//@todo: Denne er halvferdig og kun basert på en copy-paste fra userentity
		//$user->users = new CollectionProxy($this->_dataMapperFactory->build('user'), array('route_id' => array(array('operator' => '=', 'value' => $route->id))));
		//$user->usergroups = new CollectionProxy($this->_dataMapperFactory->build('usergroup'), array('route_id' => array(array('operator' => '=', 'value' => $route->id))));
    }
}