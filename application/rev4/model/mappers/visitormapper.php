<?php
namespace Model\Mappers;

use
	Model\AbstractEntity,
	Model\DatabaseDataMapper,
	Model\Domain\Visitor\Visitor,
	Model\CollectionProxy;

final class VisitorMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'Visitor';
	
	protected $_acceptedFields = array(
		'id'					=> 'visitors.id',
		'timestamp'				=> 'visitors.timestamp',
		'controller'			=> 'visitors.controller',
		'method'				=> 'visitors.method',
		'user_id'				=> 'visitors.user_id',
		'remote_addr'			=> 'visitors.remote_addr',
		'http_user_agent'		=> 'visitors.http_user_agent',
		'http_vars'				=> 'visitors.http_vars',
		'device'				=> 'visitors.device',
		'platform'				=> 'visitors.platform',
		'browswer'				=> 'visitors.browser'
		
	);
	protected $_cascadeField = '';
    
    public function fetch(AbstractEntity $visitor)
    {
    	// Check if ID has been set
    	if(isset($visitor->id))
    	{
    		return $this->findById($visitor->id, $visitor);
    	}
    	throw new \Exception('Visitor entity requires ID to be fetched');
    }
    protected function setEntitySpecificData(AbstractEntity $visitor)
    {
		$visitor->user = new CollectionProxy($this->_dataMapperFactory->build('user'), array('id' => array(array('operator' => '=', 'value' => $visitor->user_id))));
    }
}