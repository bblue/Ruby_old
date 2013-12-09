<?php
namespace App;

use App\Boot\Request;

final class ControllerFactory extends AbstractFactory
{
	private $serviceFactory;
	private $request;
	
	public function __construct(ServiceFactory $serviceFactory, Request $request)
	{
		$this->serviceFactory = $serviceFactory;
		$this->request = $request;
	}
	
	protected function construct($sControllerName)
	{
		$sControllerName = '\\Controllers\\' . $sControllerName;
		return ((!class_exists($sControllerName)) ? false : new $sControllerName($this->serviceFactory, $this->request));
	}
}