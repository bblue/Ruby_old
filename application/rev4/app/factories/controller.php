<?php
namespace App\Factories;
use App\Factory;
use App\Boot\Request;

final class Controller extends Factory
{
	private $serviceFactory;
	private $request;
	
	public function __construct(Service $serviceFactory, Request $request)
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