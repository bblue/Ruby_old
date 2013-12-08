<?php
namespace Model\Mappers;

use 
	Model\AbstractDataMapper,
	Model\AbstractEntity,
	Model\Domain\Visitor\Visitor;

final class ServerMapper extends AbstractDataMapper
{  
	public function fetch(AbstractEntity $entity)
	{
		if($entity instanceof Visitor)
		{
			$this->fetchVisitor($entity);
		}
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