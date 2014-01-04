<?php
namespace App;

use Model\Domain\Visitor\Visitor;
	
use App\Factory;

use Model\Services\Logging;

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