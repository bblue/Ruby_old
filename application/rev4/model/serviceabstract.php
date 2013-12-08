<?php
namespace Model;

use Model\Domain\Visitor\Visitor;

use Model\Domain\Log\Log;

use Lib\AbstractFactory;

abstract class ServiceAbstract
{
	protected $dataMapperFactory;
	protected $entityFactory;
	
	public function __construct(DataMapperFactory $dataMapperFactory, EntityFactory $entityFactory)
	{
		$this->dataMapperFactory = $dataMapperFactory;
		$this->entityFactory = $entityFactory;
	}
	
	protected function setModelState($sModelState, Log $logEntry)
	{
		$model = $this->entityFactory->build('model');
		
		$mapper = $this->dataMapperFactory->build('session');
		$mapper->fetch($model);
		
		$model->addModelResponseLogId($logEntry->id, $sModelState);

		return $mapper->store($model);
	}
	
	protected function createLogEntry($text, Visitor $visitor)
	{
		$logEntry = $this->entityFactory->build('log');
		$logEntry->text = $text;
		$logEntry->user_id = $visitor->user_id;
		$logEntry->timestamp = time();
		
		return $this->dataMapperFactory
			->build('log')
			->insert($logEntry);
	}
}