<?php
namespace App\Factories;

use App\Factory;
use App\Boot\SessionHandler;
use Lib\Db\DatabaseAdapterInterface;

final class DataMapper extends Factory
{
	private $db;
	private $sessionHandler;
	private $collectionFactory;
	private $entityFactory;
	
	public function __construct(DatabaseAdapterInterface $db, SessionHandler $sessionHandler, Collection $collectionFactory, Entity $entityFactory)
	{
		$this->db = $db;
		$this->sessionHandler = $sessionHandler;
		$this->collectionFactory = $collectionFactory;
		$this->entityFactory = $entityFactory;
	}
	
	protected function construct($sMapperName)
	{
		if(!isset($sMapperName))
		{
			throw new \Exception('Mapper name is unset');
		}
		$sDataMapperName = 'Model\\Mappers\\' . $sMapperName . 'mapper';
		
		if(!class_exists($sDataMapperName))
		{
			throw new \Exception('Datamapper does not exist');
		}

		switch (strtolower($sMapperName))
		{
			default:
				return new $sDataMapperName($this->db, $this->collectionFactory, $this->entityFactory, $this);
				break;
			case 'session':
				return new $sDataMapperName($this->sessionHandler, $this->collectionFactory, $this->entityFactory);
				break;
			case 'user':
			case 'visitor':
				return new $sDataMapperName($this->db, $this->collectionFactory, $this->entityFactory, $this);
				break;
			case 'server':
				return new $sDataMapperName();
				break;
		}
		
	}
}