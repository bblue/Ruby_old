<?php
namespace App;

abstract class Factory
{
	private $_cache = array();
	
	public function getCache()
	{
		return $this->_cache;
	}
	
	public function build($sClassName, $cache = false, $sTitle = '')
	{
		// Check for cached version
		if($cache && array_key_exists($sClassName, $this->_cache))
		{
			return $this->_cache[$sClassName];
		}

		// Create a new instance
		$class = $this->construct($sClassName);
		if(!is_object($class))
		{
			throw new \Exception('Unable to create instance of ' . $sClassName . ', called by ' . get_called_class());
		}
		
		if($cache)
		{
			$this->saveToCache((!empty($sTitle) ? $sTitle : $sClassName), $class);
		}

		return $class;
	}
	
	private function saveToCache($sClassName, $class)
	{
		return $this->_cache[$sClassName] = $class;
	}
	
	public function store($sTitle, $class)
	{
		if(array_key_exists($sTitle, $this->_cache) === false)
		{
			return $this->saveToCache();
		}
		return false;
	}
	
	abstract protected function construct($sClassName);
}