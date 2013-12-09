<?php
namespace Model\Mappers;
use 
	App\DatabaseDataMapper,
	App\AbstractEntity,
	App\CollectionProxy;

final class LogMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'Log';
	protected $_acceptedFields = array(
		'id'						=> 'logs.id',
		'user_id'					=> 'logs.user_id',
		'text'						=> 'logs.text',
		'timestamp'					=> 'logs.timestamp'
	);
	protected $_cascadeField = '';
    
    public function fetch(AbstractEntity $logEntry)
    {
    	// Check if ID has been set
    	if($logEntry->id)
    	{
    		return $this->findById($logEntry->id, $logEntry);
    	}
    	throw new Exception('LogEntry ID is not set');
    }
    
    protected function setEntitySpecificData(AbstractEntity $logEntry)
    {
		$logEntry->user = new CollectionProxy($this->_dataMapperFactory->build('user'), array('id' => array(array('operator' => '=', 'value' => $logEntry->user_id))));
    }
}