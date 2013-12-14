<?php
namespace App\Boot;
use Lib\Validation;

final class Request
{
	private $sUrl;
	
	private $sCommand;
	
	private $sResourceName;
	
	const DEFAULT_RETURN_DATA_TYPE = 'template'; //@todo: flytte denne til et bedre egnet sted (view f.eks)
	
	public function __construct($sUrl)
	{
		$this->sUrl = $sUrl;
	}
	
	public function getUrl()
	{
		return $this->sUrl;
	}
	
	public function getResourceName()
	{
		return $this->sResourceName = $this->sResourceName ? : $this->getGetValue('a') . (($command = $this->getGetValue('sa'))? '/' . $command : '' ) ? : '';
	}
	
	public function setCommand($sCommandName)
	{
		$this->sCommand = strtolower($sCommandName);
		return $this;
	}
	
	public function getReturnDataType()
	{
		return ($returnDataType = $this->getGetValue('returnDataType')) ? strtolower($returnDataType) : $this->DEFAULT_RETURN_DATA_TYPE;
	}
	
	public function getCommand()
	{
		return $this->sCommand = $this->sCommand ? : $this->getGetValue('sa');
	}

	private function sanitizeUri($sUri)
	{
		$validation = new Validation();
		$validation->addSource(array('uri' => $sUri));
		$validation->addValidationRule('uri', 'url', true);
		$validation->validate();
		return $validation->sanitized['uri'];
	}
	
	public function __get($key)
	{
		return $this->getPostValue($key);
	}
	
	public function getPostValue($key)
	{
		if(empty($_POST[$key]) === false)
		{
			return $_POST[$key];
		}
		return null;
	}
	
	private function getGetValue($key)
	{
		if(empty($_GET[$key]) === false)
		{
			return $_GET[$key];
		}
		return null;
	}
		
	private function getServerValue($key)
	{
		if(array_key_exists($key, $_SERVER))
		{
			return $_SERVER[$key];
		}
		return null;
	}
}