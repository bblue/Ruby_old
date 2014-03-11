<?php
namespace View;

use App\ServiceFactory;
use App\Template;

abstract class AbstractPresentationObject
{
	protected $sTemplatePrefix;

	protected $serviceFactory;
	protected $template;

	public function __construct(Template $template)
	{
		$this->template = $template;
	}
	
	private function getTemplatePrefix($toLower = false)
	{
		return (empty($this->sTemplatePrefix)) ? null : ((($toLower) ? strtolower($this->sTemplatePrefix) : $this->sTemplatePrefix) . '_');	
	}
	
	public function setTemplatePrefix($sPrefix)
	{
		$this->sTemplatePrefix = strtoupper($sPrefix);
		return $this;
	}
	
	protected function assign_vars(array $aVars)
	{
		foreach ($aVars as $key => $val)
		{
	    	$aVars[$this->getTemplatePrefix().$key] = $val;
		    unset($aVars[$key]);
		}
		
		$this->template->assign_vars($aVars);
	}
	
	protected function assign_var($key, $value)
	{
		$this->template->assign_var($this->getTemplatePrefix().$key, $value);
	}

	/**
	* Wrapper function to add specific prefix to the variable pairs
	* 
	* This function will convert <blockname> into <prefix_blockname>
	* 
	* @param string $blockname Block identifier for the template
	* @param array $vararray Array containing key and var pairs
	* 
	* @return void
	*/
	protected function assign_block_vars($blockname, array $vararray)
	{
		$this->template->assign_block_vars($this->getTemplatePrefix(true).$blockname, $vararray);
	}
	
	protected function assign_display($handle, $template_var, $include_once = false)
	{
		$this->template->assign_display($handle, ($this->getTemplatePrefix().$template_var), $include_once);
	}
}