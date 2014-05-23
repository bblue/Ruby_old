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
		'timestamp'					=> 'logs.timestamp',
		'type'						=> 'logs.type'
	);
	protected $_cascadeFields = array();

    public function fetch(AbstractEntity $logEntry)
    {
    	// Check if ID has been set
    	if($logEntry->id) {
    		return $this->findById($logEntry->id, $logEntry);
    	}
    	throw new Exception('LogEntry ID is not set');
    }

    protected function setEntitySpecificData(AbstractEntity $logEntry)
    {
    	$this->resetFilters();
    	$this->addFilter('id', $logEntry->user_id);

		$logEntry->user = 	new CollectionProxy(
								$this->_dataMapperFactory->build('user'),
								$this->getFilters()
							);
    }
}