<?php
namespace App;

use App\Factory;

abstract class ServiceAbstract
{
	protected $dataMapperFactory;
	protected $entityFactory;
	protected $log;
	protected $_cache = array();

	public function __construct(Factory $dataMapperFactory, Factory $entityFactory, $logService = null)
	{
		$this->dataMapperFactory = $dataMapperFactory;
		$this->entityFactory = $entityFactory;
		$this->log = $logService;
	}
}