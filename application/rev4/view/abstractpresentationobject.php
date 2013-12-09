<?php
namespace View;

use App\ServiceFactory;

abstract class AbstractPresentationObject
{
	protected $sName;
	private $sPresentationName;
	protected $serviceFactory;
	protected $aVars = array();

	
	public function setPresentationName($sName)
	{
		$this->sPresentationName = $sName;
		return $this;
	}
	
	public function getName()
	{
		if(!isset($this->sName))
		{
			$array = explode('\\', get_called_class());
			$this->sName = end($array) . ((!empty($this->sPresentationName)) ? '_' . $this->sPresentationName : '');
		}
		return $this->sName;
	}
	
	public function getVars()
	{
		return (isset($this->aVars['default'])) ? $this->aVars['default'] : array();
	}
	
	public function getBlockVars()
	{
		return (isset($this->aVars['blocks'])) ? $this->aVars['blocks'] :  array();
	}
	
	public function getAllVars()
	{
		return $this->aVars;
	}
	
	protected function assign_vars(array $aVars)
	{
		foreach($aVars as $key => $value)
		{
			$this->assign_var($key, $value);
		}
	}
	
	protected function assign_var($key, $value)
	{
		$this->aVars['default'][strtoupper($this->getName()) . '_' . $key] = $value;
	}
	
	protected function assign_block_vars($blockname, array $vararray)
	{
		$this->aVars['blocks'][strtolower($this->getName()) . '_' . $blockname][] = $vararray;
	}
}