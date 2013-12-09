<?php
namespace App;

final class ServiceFactory extends AbstractFactory
{
	private $dataMapperFactory;
	private $entityFactory;
	
	public function __construct(AbstractFactory $dataMapperFactory, AbstractFactory $entityFactory)
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