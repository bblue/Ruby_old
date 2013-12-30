<?php
namespace App\Factories;

use App\CacheableFactory,
	App\Factory;


final class Service extends CacheableFactory
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
		$sServiceName = 'Model\Services\\' . $sServiceName;
		return new $sServiceName($this->dataMapperFactory, $this->entityFactory);
	}
}