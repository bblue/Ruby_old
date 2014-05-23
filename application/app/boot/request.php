<?php
namespace App\Boot;
use Lib\Validation;

final class Request
{
	private $sUrl;
	public $aUrlParams = array();
	private $sCommand;

	public function isAjaxRequest()
	{
		return ('XMLHttpRequest' == $this->_server('HTTP_X_REQUESTED_WITH'));
	}

	public function getUrl()
	{
		if(isset($this->sUrl)){
			return $this->sUrl;
		}
		// Remove querystring
		$url = strtok($this->_server('REQUEST_URI'),'?');

		//?a={R:1}&sa={R:2}&mt={R:3}

		// Remove trailing slash
		$url = trim($url, '/');

		return $this->sUrl = $url;
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

	public function __isset($key)
	{
		$var = $this->_post($key);
		return isset($var);
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

	public function _post($key = null)
	{
		if($key === null) {
			return $_POST;
		}

		if(empty($_POST[$key]) === false) {
			return $_POST[$key];
		}
		return null;
	}

	public function _server($key)
	{
		if(empty($_SERVER[$key]) === false) {
			return $_SERVER[$key];
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