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
		'sCommand'		=> 'routes.sCommand',
		'a_id'			=> 'areas.a_id'
	);
	protected $_cascadeFields = array(
		'routes.sResourceName = areas.name'
	);
    
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
		// Find the users that have access to the area in this route
		//$route->users = new CollectionProxy($this->_dataMapperFactory->build('user'), array('a_id' => array(array('operator' => '=', 'value' => $route->a_id))));
		
    	// Find the usergroups that have access to the area in this route
		$route->usergroups = new CollectionProxy(
			$this->_dataMapperFactory->build('usergroup'),
			null,
			array(
				'cascade_a_usergroups.a_id' => array(array('operator' => '=','value' => $route->a_id)),
				'id' 						=> array(array('operator' => '=','value' => 'cascade_a_usergroups.ug_id', 'tablevalue' => true))
			)
		);
		
    }
}