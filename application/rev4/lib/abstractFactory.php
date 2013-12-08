<?php
namespace Lib;

abstract class AbstractFactory
{
	private $_cache = array();
	
	public function getCache()
	{
		return $this->_cache;
	}
	
	public function build($sClassName, $store = false)
	{
		// Check for cached version
		if(array_key_exists($sClassName, $this->_cache))
		{
			$class = $this->_cache[$sClassName];
		}
		else
		{
			// Create a new instance
			$class = $this->construct($sClassName);
			if(!is_object($class))
			{
				throw new \Exception('Unable to create instance of ' . $sClassName . ', called by ' . get_called_class());
			}
			
			if($store)
			{
				$this->saveToCache($sClassName, $class);
			}
		}
		return $class;
	}
	
	private function saveToCache($sClassName, $class)
	{
		return $this->_cache[$sClassName] = $class;
	}
	
	public function store($sClassName, $class)
	{
		if(array_key_exists($sClassName, $this->_cache) === false)
		{
			return $this->saveToCache();
		}
		return false;
	}
	
	abstract protected function construct($sClassName);
}