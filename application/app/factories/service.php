<?php
namespace App\Factories;

use App\Factory;

final class Service extends Factory
{
	private $dataMapperFactory;
	private $entityFactory;

	private $visitor;

	public function __construct(Factory $dataMapperFactory, Factory $entityFactory)
	{
		$this->entityFactory = $entityFactory;
		$this->dataMapperFactory = $dataMapperFactory;
	}

	protected function construct($sServiceName)
	{
		// Load the logging service for injection
		$logService = ($sServiceName != 'logging') ? $this->build('logging', true) : null;

		// Add namespace to classname
		$sServiceName = 'Model\Services\\' . $sServiceName;

		// Create class and return it
		return new $sServiceName($this->dataMapperFactory, $this->entityFactory, $logService);
	}

	public function register($class, $sClassName)
	{
		$this->_cache[$sClassName] = $class;
	}
}