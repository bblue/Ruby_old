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
    	// Check if ID has been set
    	if(isset($route->id)) {
    		$this->findById($route->id, $route);
    	} elseif(!empty($route->sResourceName)) {
	    	$aCriterias['sResourceName'] = array( 
	    		array(
	    			'operator' 	=> '=',
	    			'value' 	=> $route->sResourceName
	    		)
	    	);
	    	$aCriterias['sCommand'] = array(
	    		array(
	    			'operator'	=> '=',
	    			'value'		=> $route->getCommand()
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
			array('active' => array(array('operator' => '=','value' => 1))),
			array(
				'cascade_a_usergroups.a_id' => array(array('operator' => '=','value' => $route->id)),
				'id' 						=> array(array('operator' => '=','value' => 'cascade_a_usergroups.ug_id', 'tablevalue' => true))
			)
		);
		
    }
}