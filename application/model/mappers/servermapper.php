<?php
namespace Model\Mappers;

use 
	App\AbstractDataMapper,
	App\AbstractEntity,
	Model\Domain\Visitor\Visitor as Visitor;

final class ServerMapper extends AbstractDataMapper
{  
	public function fetch(AbstractEntity $entity)
	{
		if($entity instanceof Visitor)
		{
			return $this->fetchVisitor($entity);
		}
		throw new \Exception('Entity (' . (is_object($entity) ? get_class($entity) : $entity) . ') cannot be mapped by this datamapper (' . __CLASS__ . ')');
	}
	
	private function fetchVisitor(Visitor $visitor)
	{
		$visitor->remote_addr = $_SERVER['REMOTE_ADDR'];
		$visitor->http_user_agent = $_SERVER['HTTP_USER_AGENT'];
		$visitor->http_vars = serialize($this->extractHttpVars($_SERVER));
	}
	
	private function extractHttpVars($headers = array())
	{
		foreach ($headers as $key => $value) {
			if (substr($key,0,5) == 'HTTP_') {
				$httpHeaders[$key] = $value;
			}
		}
		return $httpHeaders ? : array();	
	}
	
}