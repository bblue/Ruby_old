<?php
namespace App\Boot;
use Lib\Validation;

final class Request
{
	private $sUrl;
	public $aUrlParams = array();
	private $sCommand;

	public function __construct($sUrl)
	{
		$url = trim($sUrl, '/');
		$this->sUrl = $url;
	}

	public function getUrl()
	{
		return $this->sUrl;
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

	public function _get($key)
	{
		if(empty($_GET[$key]) === false) {
			return $_GET[$key];
		}
		return null;
	}

	public function _post($key)
	{
		if(empty($_POST[$key]) === false) {
			return $_POST[$key];
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