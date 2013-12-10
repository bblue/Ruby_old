<?php
namespace App\Boot;
use Lib\Validation;

final class Request
{
	private $sUri;
	
	private $sCommand;
	
	private $sResourceName;
	
	const DEFAULT_RETURN_DATA_TYPE = 'template'; //@todo: flytte denne til et bedre egnet sted (view f.eks)
	const DEFAULT_RESOURCE_NAME = 'recipes';
	
	public function __construct($sUri)
	{
		$this->sUri = $sUri;
	}
	
	public function getUri()
	{
		return $this->sUri;
	}
	
	public function getResourceName()
	{
		return $this->sResourceName = $this->sResourceName ? : $this->getGetValue('a') ? : $this->DEFAULT_RESOURCE_NAME;
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