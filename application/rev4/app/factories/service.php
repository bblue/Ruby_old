<?php
namespace App\Factories;

use App\Factory;

final class Service extends Factory
{
	private $dataMapperFactory;
	private $entityFactory;
	
	public function __construct(Factory $dataMapperFactory, Factory $entityFactory)
	{
		$this->entityFactory = $entityFactory;
		$this->dataMapperFactory = $dataMapperFactory;
	}
	
	protected function construct($sServiceName)
	{
		// Load the logging service for injection
		$logService = ($sServiceName == 'logging') ? null : $this->build('logging', true);
		$sServiceName = 'Model\Services\\' . $sServiceName;
		
		
		return new $sServiceName($this->dataMapperFactory, $this->entityFactory, $logService);
	}
}